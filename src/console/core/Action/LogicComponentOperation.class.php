<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/26/2016
 * Time: 2:46 PM
 */

namespace Console\Core\Action;

use Console\Core\Config\IConfig;
use Console\Core\Parameters\ScriptParams\ScriptParams;
use Framework\Core\FrameworkClasses\Components\SFComponent;

class LogicComponentOperation extends ComponentOperation
{
    public function __construct($name, $value, $index, IConfig $config, array $allowedValues, array $consoleConfig, ScriptParams $scriptParams)
    {
        parent::__construct($name, $value, $index, $config, $allowedValues, $consoleConfig, $scriptParams);

        $this->componentConfigIndex = $consoleConfig['logic_component_config_index'];
        $this->componentType = $consoleConfig['logic_component_type'];
        $this->componentConstantPrefix = $consoleConfig['logic_component_constant_prefix'];
        $this->componentTypeName = ucfirst($this->componentType);
        $this->componentDirConfigIndex = $consoleConfig['logic_component_directory_config_index'];
        $this->componentDirectory = $this->config->getParsed($this->componentDirConfigIndex);
        $this->components = $this->config->get($this->componentConfigIndex);
        $this->dependencyArray = $this->config->get($consoleConfig['logic_dependency_config_index']);
    }

    protected function genArrayOfComponentFiles($componentName)
    {
        $files = array();

        $logicComponentNamespace = rtrim($this->config->getParsed('logic_comp_ns'), "\\");

        $className = SFComponent::getClassName($componentName);

        $componentDir = $this->config->getParsed('logic_components_dir');

        $files[$componentDir . $componentName . '/' . $componentName . '.php'] = "namespace $logicComponentNamespace;\n\nuse Framework\Core\FrameworkClasses\Components\LogicComponent;\n\nclass $className extends LogicComponent\n{\n    public function init()\n    {\n    }\n}\n";
        $files[$componentDir . $componentName . '/config/' . $componentName . '_config.php'] = "";

        return $files;
    }

    protected function genArrayOfComponentDirectories($componentName)
    {
        $componentsDir = $this->config->getParsed($this->componentDirConfigIndex);

        return array($componentsDir . $componentName . '/');
    }

    protected function removeComponent($name, array $directoriesToRemove, $askSecQuestion = true)
    {
        $question = "Are you sure that you want to delete logic component \"$name\" ";

        $outputComponents = $this->config->get('output_components');

        $outputComponentsToUpdate = array();

        foreach ($outputComponents as $ocName => $logicDependencies)
        {
            if (in_array($name, $logicDependencies))
            {
                $outputComponentsToUpdate[] = $ocName;
            }
        }

        $logicComponents = $this->config->get('logic_components');

        $logicComponentsToUpdate = array();

        foreach ($logicComponents as $lcName => $logicDependencies)
        {
            if (in_array($name, $logicDependencies))
            {
                $logicComponentsToUpdate[] = $lcName;
            }
        }

        $questionAppendix = "";

        if (!empty($outputComponentsToUpdate))
        {
            $outputComponentsToUpdateStr = $this->arrToStr($outputComponentsToUpdate);

            $questionAppendix .= "\nListed as dependency on next output components: $outputComponentsToUpdateStr\n";
        }

        if (!empty($logicComponentsToUpdate))
        {
            $logicComponentsToUpdateStr = $this->arrToStr($logicComponentsToUpdate);

            $questionAppendix .= "\nListed as dependency on next logic components: $logicComponentsToUpdateStr\n";
        }

        if (!empty($questionAppendix))
        {
            $question .= "\n$questionAppendix\nAnswer? (yes|no): ";
        }
        else
        {
            $question .= "(yes|no)";
        }

        $answer = $this->scriptParams->askForUserInput($question, array('yes', 'no'));

        if ($answer == 'no')
        {
            return "Giving up on deleting component.";
        }

        foreach ($outputComponentsToUpdate as $ocName)
        {
            if (!in_array($name, $outputComponents[$ocName]))
                throw new \Exception("Component \"$ocName\" does not have logic component \"$name\" as its dependency.");

            if(($key = array_search($name, $outputComponents[$ocName])) !== false)
            {
                unset($outputComponents[$ocName][$key]);
            }
        }

        $this->config->set('output_components', $outputComponents);

        foreach ($logicComponentsToUpdate as $lcName)
        {
            if (!in_array($name, $logicComponents[$lcName]))
                throw new \Exception("Logic component \"$lcName\" does not have logic component \"$name\" as its dependency.");

            if(($key = array_search($name, $logicComponents[$lcName])) !== false)
            {
                unset($logicComponents[$lcName][$key]);
            }
        }

        $this->config->set('logic_components', $logicComponents);

        return parent::removeComponent($name, $directoriesToRemove, false);
    }

    public function perform()
    {
        $previewValue = $this->previewValue();

        if ($previewValue !== false)
            return $previewValue;

        $componentName = $this->scriptParams->askForUserInput(
            'Please enter logic component name: '
        );

        if (empty($componentName))
            throw new \Exception('Logic component name cannot be empty.');

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