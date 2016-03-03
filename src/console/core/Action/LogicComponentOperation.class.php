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
        $componentsDir = $this->config->get($this->componentDirConfigIndex);

        return array($componentsDir . $componentName . '/');
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