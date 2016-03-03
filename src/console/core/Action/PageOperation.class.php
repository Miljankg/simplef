<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/27/2016
 * Time: 3:32 PM
 */

namespace Console\Core\Action;


class PageOperation extends Operation
{
    protected $pages;

    /**
     * Should display the value if preview_value is passed.
     *
     * @return mixed
     */
    protected function previewValue()
    {
        if ($this->previewValueValue != $this->value)
            return false;

        return true;

        $pages = $this->pages;

        $str = '';

        $pageName = $this->scriptParams->askForUserInput(
            "Please enter $this->componentType component name if you want to filter output (or enter for full report): "
        );

        if (!empty($pageName))
        {
            $this->checkIfComponentOrPageExists($pageName, true, 'pages');

            $pages = array(
                $pageName => $pages[$pageName]
            );
        }

        foreach ($pages as $page => $pageDependencies)
        {
            $str .= 'Name: ' . $page . $this->nl;
            $str .= 'Dependencies: [ ' . $this->arrToStr($pageDependencies) . ' ] ' . $this->dnl;
        }

        return $str;
    }

    protected function addPage($filesToAdd, $name)
    {
        $typeName = ucfirst('page');
        $pages = $this->pages;

        if (array_key_exists($name, $pages))
            throw new \Exception("$typeName \"$name\" is already configured.");

        $dependencies = $this->scriptParams->askForUserInput(
            "Please enter page dependencies separated by , (comma) or just press enter for none: "
        );

        $dependenciesArr = array();

        if (!empty($dependencies))
            $dependenciesArr = explode(',', $dependencies);

        $dependencyConfig = $this->config->get('output_components');

        foreach ($dependenciesArr as $key => $dependency)
        {
            if (!isset($dependencyConfig[$dependency]))
                throw new \Exception("Dependency output component \"{$dependency}\" does not exists.");

            if (in_array($name, $dependencyConfig[$dependency]))
                throw new \Exception("Circular dependency detected $name<=>$dependency.");

            $dependenciesArr[$key] = trim($dependency);
        }

        $pages[$name] = $dependenciesArr;

        $this->config->set('pages', $pages);

        foreach ($filesToAdd as $file => $content)
        {
            $noPhpTag = false;

            if (substr($file, -4) != '.php')
                $noPhpTag = true;

            $this->createPhpFile($file, $content, $noPhpTag);
        }

        define('PAGE_' . strtoupper($name), $name);

        return "Page \"$name\" successfully added.";
    }

    protected function removeComponent($name, array $directoriesToRemove)
    {
        $pages = $this->pages;

        if (!array_key_exists($name, $pages))
            throw new \Exception("Page \"$name\" is not configured.");

        $areYouSure = "Are you sure that you want to delete page \"$name\" (yes|no)?";

        $sure = $this->scriptParams->askForUserInput($areYouSure, array('yes', 'no'));

        if ($sure == 'no')
            return "Giving up on removing page.";

        unset($pages[$name]);

        $this->config->set('pages', $pages);

        foreach ($directoriesToRemove as $directory)
            $this->deleteDirectory($directory);

        $constant = 'PAGE_' . strtoupper($name);

        if (defined($constant))
            $this->config->queueConstantForRemoval($constant);

        return "Page \"$name\" removed successfully.";
    }

    protected function addDependency($name)
    {
        $pages = $this->pages;

        $this->checkIfComponentOrPageExists($name, true, 'pages');

        $dependency = $this->scriptParams->askForUserInput(
            "Enter the name of the output component to add as dependency:"
        );

        $this->checkIfComponentOrPageExists($dependency, true, 'output_components');

        if (isset($pages[$name]) && in_array($dependency, $pages[$name]))
            throw new \Exception("Page \"$name\" already has output component \"$dependency\" as its dependency.");

        array_push($pages[$name], $dependency);

        $this->config->set('pages', $pages);

        return "Output component \"$dependency\" added as dependency for \"$name\" page.";
    }

    protected function removeDependency($name)
    {
        $pages = $this->pages;

        $this->checkIfComponentOrPageExists($name);

        $dependency = $this->scriptParams->askForUserInput(
            "Enter the name of the logic component to remove as dependency:"
        );

        $this->checkIfComponentOrPageExists($dependency, true, 'output_components');

        if (!in_array($dependency, $pages[$name]))
            throw new \Exception("Page \"$name\" does not have output component \"$dependency\" as its dependency.");

        if(($key = array_search($dependency, $pages[$name])) !== false)
        {
            unset($pages[$name][$key]);
        }

        $this->config->set('pages', $pages);

        return "Output component \"$dependency\" removed as dependency for \"$name\" page.";
    }

    protected function checkIfComponentOrPageExists($componentName, $throw = true, $configIndexToCheck = '')
    {
        $objects = $this->pages;

        if (!empty($configIndexToCheck))
            $objects = $this->config->getParsed($configIndexToCheck);

        $word = (substr($configIndexToCheck, 5) == 'pages') ? 'Page' : 'Component';
        $wordPl = ($word == 'Page') ? 'Pages' : 'Components';

        if (!isset($objects[$componentName]))
        {
            if ($throw)
                throw new \Exception("$word \"$componentName\" does not exists in configured $wordPl.");
            else
                return false;
        }

        return true;
    }

    protected function genArrayOfComponentFiles($componentName)
    {
        $pageTemplateDirectory = $this->config->getParsed('page_template_directory');

        $files = array();

        $files[$outputComponentTemplateDirectory . "$componentName/$componentName.tpl"] = "This is component \"$componentName\". Hello!";
        $files[$outputComponentTemplateDirectory . "$componentName/css/$componentName.css"] = "";
        $files[$outputComponentTemplateDirectory . "$componentName/js/$componentName.js"] = "";

        return $files;
    }

    protected function genArrayOfComponentDirectories($componentName)
    {
        $outputComponentDirectory = $this->componentDirectory;
        $outputComponentTemplateDirectory = $this->outputComponentTemplateDirectory;

        $directories = array();

        array_push($directories, $outputComponentDirectory . "$componentName/");
        array_push($directories, $outputComponentTemplateDirectory . "$componentName/");

        return $directories;
    }

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     */
    public function perform()
    {
        $previewValue = $this->previewValue();

        if ($previewValue !== false)
            return $previewValue;

        $componentName = $this->scriptParams->askForUserInput(
            'Please enter page name: '
        );

        if (empty($componentName))
            throw new \Exception('Page name cannot be empty.');

        $componentFiles = $this->genArrayOfComponentFiles($componentName);
        $directories = $this->genArrayOfComponentDirectories($componentName);

        $output = '';

        switch ($this->value)
        {
            case 'add':
                $output = $this->addComponent($componentFiles, $componentName);
                break;
            case 'remove':
                $output = $this->removeComponent($componentName, $directories);
                break;
            case 'add_dependency':
                $output = $this->addDependency($componentName);
                break;
            case 'remove_dependency':
                $output = $this->removeDependency($componentName);
                break;
        }

        return $output;
    }
}