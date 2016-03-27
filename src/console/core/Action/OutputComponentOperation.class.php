<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/27/2016
 * Time: 3:41 AM
 */

namespace Console\Core\Action;

use Console\Core\Config\IConfig;
use Console\Core\Parameters\ScriptParams\ScriptParams;
use Framework\Core\FrameworkClasses\Components\SFComponent;

class OutputComponentOperation extends ComponentOperation
{
    protected $outputComponentTemplateDirectory;
    protected $outputComponentOptionsConfigIndex;
    protected $outputComponentsOptions;

    public function __construct($name, $value, $index, IConfig $config, array $allowedValues, array $consoleConfig, ScriptParams $scriptParams)
    {
        parent::__construct($name, $value, $index, $config, $allowedValues, $consoleConfig, $scriptParams);

        $this->componentConfigIndex = $consoleConfig['output_component_config_index'];
        $this->componentType = $consoleConfig['output_component_type'];
        $this->componentConstantPrefix = $consoleConfig['output_component_constant_prefix'];
        $this->componentTypeName = ucfirst($this->componentType);
        $this->componentDirConfigIndex = $consoleConfig['output_component_directory_config_index'];
        $this->componentDirectory = $this->config->getParsed($this->componentDirConfigIndex);
        $this->components = $this->config->get($this->componentConfigIndex);
        $this->outputComponentTemplateDirectory = $this->config->getParsed($consoleConfig['output_component_template_dir_config_index']);
        $this->outputComponentOptionsConfigIndex = $consoleConfig['output_component_options_config_index'];
        $this->outputComponentsOptions = $this->config->get($this->outputComponentOptionsConfigIndex);
        $this->dependencyArray = $this->config->get($consoleConfig['output_dependency_config_index']);
    }

    private function askCssJsEnableDisable($what, $disable = false)
    {
        $word = (!$disable) ? 'enable' : 'disable';

        $question = ucfirst($word) . " $what (yes|no)?";

        $yesNo = $this->scriptParams->askForUserInput($question, array('yes', 'no'));

        return ($yesNo == 'yes');
    }

    protected function addComponent($filesToAdd, $name)
    {
        $statusText = parent::addComponent($filesToAdd, $name);

        $js = $this->askCssJsEnableDisable('JS');
        $css = $this->askCssJsEnableDisable('CSS');

        $ocOptions = $this->outputComponentsOptions;

        $thisOcOptions = array('css' => $css, 'js' => $js);

        $ocOptions[$name] = $thisOcOptions;

        $this->config->set($this->outputComponentOptionsConfigIndex, $ocOptions);

        return $statusText;
    }

    protected function removeComponent($name, array $directoriesToRemove)
    {
        $pagesOutComponents = $this->config->getParsed('pages_out_components');

        $pagesToUpdate = array();

        foreach ($pagesOutComponents as $pageName => $outComponents)
        {
            if (in_array($name, $outComponents))
            {
                $pagesToUpdate[] = $pageName;
            }
        }

        $question = "Are you sure that you want to delete $this->componentType component $name (yes|no)?";

        if (!empty($pagesToUpdate))
        {
            $question = "Do you really want to remove output component \"$name\", it is a dependency for next pages (pages will be updated, also)(yes|no)\: " . $this->arrToStr($pagesToUpdate);
        }

        $answer = $this->scriptParams->askForUserInput($question, array('yes', 'no'));

        if ($answer == 'no')
        {
            return "Giving up on removing output component.";
        }

        $pagesDependencies = $this->config->get('pages_out_components');

        foreach ($pagesToUpdate as $pageName)
        {
            if (!in_array($name, $pagesDependencies[$pageName]))
                throw new \Exception("Page \"$pageName\" does not have output component \"$name\" as its dependency.");

            if(($key = array_search($name, $pagesDependencies[$pageName])) !== false)
            {
                unset($pagesDependencies[$pageName][$key]);
            }
        }

        $this->config->set('pages_out_components', $pagesDependencies);

        $statusText = parent::removeComponent($name, $directoriesToRemove, false);

        $ocOptions = $this->outputComponentsOptions;

        if (isset($ocOptions[$name]))
            unset($ocOptions[$name]);

        $this->config->set($this->outputComponentOptionsConfigIndex, $ocOptions);

        return $statusText;
    }

    private function editOutputComponentOptions($name, $option, $enable = true)
    {
        $this->checkIfComponentExists($name);

        $ocOptions = array();

        if (isset($this->outputComponentsOptions[$name]))
            $ocOptions = $this->outputComponentsOptions[$name];

        $ocOptions[$option] = $enable;

        $this->outputComponentsOptions[$name] = $ocOptions;

        $this->config->set($this->outputComponentOptionsConfigIndex, $this->outputComponentsOptions);

        $word = ($enable) ? 'enabled' : 'disabled';

        return strtoupper($option) . " has been $word for output component \"$name\"";
    }

    protected function genArrayOfComponentFiles($componentName)
    {
        $outputComponentDirectory = $this->componentDirectory;
        $outputComponentTemplateDirectory = $this->outputComponentTemplateDirectory;
        $languages = $this->config->get('available_langs');

        $className = SFComponent::getClassName($componentName);

        $files = array();

        $files[$outputComponentDirectory . "$componentName/$componentName.php"] = "namespace Components\Output;\n\nuse Framework\Core\FrameworkClasses\Components\OutputComponent;\n\nclass {$className} extends OutputComponent\n{\n    protected function execute()\n    {\n    }\n}";
        $files[$outputComponentDirectory . "$componentName/config/{$componentName}_config.php"] = "";

        foreach ($languages as $language)
        {
            $files[$outputComponentDirectory . "$componentName/lang/$language/$language.php"] = "";
        }

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

    public function perform()
    {
        $previewValue = $this->previewValue();

        if ($previewValue !== false)
            return $previewValue;

        $componentName = $this->scriptParams->askForUserInput(
            'Please enter output component name: '
        );

        if (empty($componentName))
            throw new \Exception('Output component name cannot be empty.');

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
            case 'enable_js':
                $output = $this->editOutputComponentOptions($componentName, 'js', true);
                break;
            case 'disable_js':
                $output = $this->editOutputComponentOptions($componentName, 'js', false);
                break;
            case 'enable_css':
                $output = $this->editOutputComponentOptions($componentName, 'css', true);
                break;
            case 'disable_css':
                $output = $this->editOutputComponentOptions($componentName, 'css', false);
                break;
        }

        return $output;
    }
}