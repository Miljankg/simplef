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

        $pages = $this->config->get('pages');

        $str = '';

        $pageName = $this->scriptParams->askForUserInput(
            "Please enter page name if you want to filter output (or just press enter for full report): "
        );

        if (!empty($pageName))
        {
            $this->checkIfComponentOrPageExists($pageName, true, 'pages');

            $pages = array(
                $pageName => $pages[$pageName]
            );
        }

        foreach ($pages as $page => $pageConfig)
        {
            $pageConfigParsed = array();

            foreach ($pageConfig as $configIndex => $value)
            {
                $valueStr = ($value) ? 'true' : 'false';

                $pageConfigParsed[] = $configIndex . ' = ' . $valueStr;
            }

            $pagesDependencies = $this->config->get('pages_out_components');

            $pageDependencies = array();

            if (isset($pagesDependencies[$page]) || is_array($pagesDependencies[$page]))
            {
                $pageDependencies = $this->arrToStr($pagesDependencies[$page]);
            }

            $str .= 'Name: ' . $page . $this->nl;
            $str .= 'Config: [ ' . $this->arrToStr($pageConfigParsed) . ' ] ' . $this->nl;
            $str .= 'Dependencies: [ ' . $pageDependencies . ' ] ' . $this->dnl;
        }

        return $str;
    }

    protected function addPage($filesToAdd, $name)
    {
        $typeName = ucfirst('page');
        $pages = $this->config->get('pages');

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

    protected function removePage($name, array $directoriesToRemove)
    {
        $pages = $this->config->get('pages');

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
        $this->checkIfComponentOrPageExists($name, true, 'pages');

        $dependency = $this->scriptParams->askForUserInput(
            "Enter the name of the output component to add as dependency:"
        );

        $this->checkIfComponentOrPageExists($dependency, true, 'output_components');

        $pagesDependencies = $this->config->get('pages_out_components');

        if (isset($pagesDependencies[$name]) && in_array($dependency, $pagesDependencies[$name]))
            throw new \Exception("Page \"$name\" already has output component \"$dependency\" as its dependency.");

        array_push($pagesDependencies[$name], $dependency);

        $this->config->set('pages_out_components', $pagesDependencies);

        return "Output component \"$dependency\" added as dependency for \"$name\" page.";
    }

    protected function removeDependency($name)
    {
        $this->checkIfComponentOrPageExists($name, true, 'pages');

        $dependency = $this->scriptParams->askForUserInput(
            "Enter the name of the output component to remove as dependency:"
        );

        $this->checkIfComponentOrPageExists($dependency, true, 'output_components');

        $pagesDependencies = $this->config->get('pages_out_components');

        if (!in_array($dependency, $pagesDependencies[$name]))
            throw new \Exception("Page \"$name\" does not have output component \"$dependency\" as its dependency.");

        if(($key = array_search($dependency, $pagesDependencies[$name])) !== false)
        {
            unset($pagesDependencies[$name][$key]);
        }

        $this->config->set('pages_out_components', $pagesDependencies);

        return "Output component \"$dependency\" removed as dependency for \"$name\" page.";
    }

    protected function checkIfComponentOrPageExists($componentName, $throw = true, $configIndexToCheck = '')
    {
        $objects = $this->pages;

        if (!empty($configIndexToCheck))
            $objects = $this->config->getParsed($configIndexToCheck);

        $word = (substr($configIndexToCheck, 0, 5) == 'pages') ? 'Page' : 'Component';
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

    protected function genArrayOfPageFiles($pageName)
    {
        $pageTemplateDirectory = $this->config->getParsed('page_template_directory');

        $files = array();

        $files[$pageTemplateDirectory . "$pageName/$pageName.tpl"] = "This is page \"$pageName\". Hello!";
        $files[$pageTemplateDirectory . "$pageName/css/$pageName.css"] = "";
        $files[$pageTemplateDirectory . "$pageName/js/$pageName.js"] = "";

        return $files;
    }

    protected function genArrayOfPageDirectories($pageName)
    {
        $pageTemplateDirectory = $this->config->getParsed('page_template_directory');

        $directories = array();

        array_push($directories, $pageTemplateDirectory . "$pageName/");

        return $directories;
    }

    private function addRole($pageName)
    {
        $pages = $this->config->get('pages');

        if (!isset($pages[$pageName]))
            throw new \Exception("Page \"$pageName\" does not exists.");

        $pagesAccess = $this->config->get('pages_access');

        $pageRoles = array();

        if (isset($pagesAccess[$pageName]) && is_array($pagesAccess[$pageName]))
            $pageRoles = $pagesAccess[$pageName];

        $question = "Please, enter the role name: ";

        $roleName = $this->scriptParams->askForUserInput($question);

        if (empty($roleName))
            throw new \Exception("Role name cannot be empty.");

        if (in_array($roleName, $pageRoles))
            throw new \Exception("Role \"$roleName\" already exists for page \"$pageName\".");

        $roles = $this->config->get('roles');

        if (!in_array($roleName, $roles))
            throw new \Exception("Role \"$roleName\" does not exists.");

        array_push($pageRoles, $roleName);

        $pagesAccess[$pageName] = $pageRoles;

        $this->config->set('pages_access', $pagesAccess);

        return "Role \"$roleName\" added for page \"$pageName\"";
    }

    private function removeRole($pageName)
    {
        $pages = $this->config->get('pages');

        if (!isset($pages[$pageName]))
            throw new \Exception("Page \"$pageName\" does not exists.");

        $pagesAccess = $this->config->get('pages_access');

        $question = "Please, enter the role name: ";

        $roleName = $this->scriptParams->askForUserInput($question);

        if (empty($roleName))
            throw new \Exception("Role name cannot be empty.");

        $roles = $this->config->get('roles');

        if (!in_array($roleName, $roles))
            throw new \Exception("Role \"$roleName\" does not exists.");

        if (!isset($pagesAccess[$pageName]) || !in_array($roleName, $pagesAccess[$pageName]))
        {
            throw new \Exception("Role \"$roleName\" is not configured for page \"$pageName\".");
        }

        if(($key = array_search($roleName, $pagesAccess[$pageName])) !== false)
        {
            unset($pagesAccess[$pageName][$key]);
        }

        $this->config->set('pages_access', $pagesAccess);

        return "Role \"$roleName\" removed successfully for page \"$pageName\".";
    }

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     * @throws \Exception If page name is empty.
     */
    public function perform()
    {
        $previewValue = $this->previewValue();

        if ($previewValue !== false)
            return $previewValue;

        $pageName = $this->scriptParams->askForUserInput(
            'Please enter page name: '
        );

        if (empty($pageName))
            throw new \Exception('Page name cannot be empty.');

        $pageFiles = $this->genArrayOfPageFiles($pageName);
        $directories = $this->genArrayOfPageDirectories($pageName);

        $output = '';

        switch ($this->value)
        {
            case 'add':
                $output = $this->addPage($pageFiles, $pageName);
                break;
            case 'remove':
                $output = $this->removePage($pageName, $directories);
                break;
            case 'add_dependency':
                $output = $this->addDependency($pageName);
                break;
            case 'remove_dependency':
                $output = $this->removeDependency($pageName);
                break;
            case 'add_role':
                $output = $this->addRole($pageName);
                break;
            case 'remove_role':
                $output = $this->removeRole($pageName);
                break;
        }

        return $output;
    }
}