<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/26/2016
 * Time: 2:43 PM
 */

namespace Console\Core\Action;


abstract class ComponentOperation extends Operation
{
    protected $componentConfigIndex;
    protected $componentType;
    protected $componentConstantPrefix;
    protected $componentTypeName;
    protected $componentDirConfigIndex;
    protected $components = array();
    protected $componentDirectory;
    protected $dependencyType;
    protected $dependencyArray;

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

        $components = $this->components;

        $str = '';

        $componentName = $this->scriptParams->askForUserInput(
            "Please enter $this->componentType component name if you want to filter output (or enter for full report): "
        );

        if (!empty($componentName))
        {
            $this->checkIfComponentExists($componentName);

            $components = array(
                $componentName => $components[$componentName]
            );
        }

        foreach ($components as $component => $componentDependencies)
        {
            $str .= 'Name: ' . $component . $this->nl;
            $str .= 'Dependencies: [ ' . $this->arrToStr($componentDependencies) . ' ] ' . $this->dnl;
        }

        return $str;
    }

    protected function addComponent($filesToAdd, $name)
    {
        $typeName = ucfirst($this->componentType);
        $components = $this->components;

        if (array_key_exists($name, $components))
            throw new \Exception("$typeName component \"$name\" is already configured.");

        $dependencies = $this->scriptParams->askForUserInput(
            "Please enter $this->componentType component dependencies separated by , (comma) or just press enter for none: "
        );

        $dependenciesArr = array();

        if (!empty($dependencies))
            $dependenciesArr = explode(',', $dependencies);

        $dependencyConfig = $this->dependencyArray;

        foreach ($dependenciesArr as $key => $dependency)
        {
            if (!isset($dependencyConfig[$dependency]))
                throw new \Exception("Dependency logic component \"{$dependency}\" does not exists.");

            if (in_array($name, $dependencyConfig[$dependency]))
                throw new \Exception("Circular dependency detected $name<=>$dependency.");

            $dependenciesArr[$key] = trim($dependency);
        }

        $components[$name] = $dependenciesArr;

        $this->config->set($this->componentConfigIndex, $components);

        foreach ($filesToAdd as $file => $content)
        {
            $noPhpTag = false;

            if (substr($file, -4) != '.php')
                $noPhpTag = true;

            $this->createPhpFile($file, $content, $noPhpTag);
        }

        define($this->componentConstantPrefix . strtoupper($name), $name);

        return "$typeName component successfully added.";
    }

    protected function removeComponent($name, array $directoriesToRemove)
    {
        $components = $this->components;
        $componentsDir = $this->config->get($this->componentDirConfigIndex);

        if (!array_key_exists($name, $components))
            throw new \Exception("$this->componentTypeName component \"$name\" is not configured.");

        $areYouSure = "Are you sure that you want to delete $this->componentType component $name (yes|no)?";

        $sure = $this->scriptParams->askForUserInput($areYouSure, array('yes', 'no'));

        if ($sure == 'no')
            return "Giving up on removing $this->componentType component.";

        unset($components[$name]);

        $this->config->set($this->componentConfigIndex, $components);

        foreach ($directoriesToRemove as $directory)
            $this->deleteDirectory($directory);

        $constant = $this->componentConstantPrefix . strtoupper($name);

        if (defined($constant))
            $this->config->queueConstantForRemoval($constant);

        return "$this->componentTypeName component \"$name\" removed successfully.";
    }

    protected function addDependency($name)
    {
        $components = $this->components;

        $this->checkIfComponentExists($name);

        $dependency = $this->scriptParams->askForUserInput(
            "Enter the name of the logic component to add as dependency:"
        );

        $this->checkIfComponentExists($dependency, true, 'logic_components');

        if (isset($components[$dependency]) && in_array($name, $components[$dependency]))
            throw new \Exception("Circular dependency detected $name<=>$dependency.");

        if (isset($components[$name]) && in_array($dependency, $components[$name]))
            throw new \Exception("$this->componentTypeName component \"$name\" already has logic component \"$dependency\" as its dependency.");

        array_push($components[$name], $dependency);

        $this->config->set($this->componentConfigIndex, $components);

        return "Logic component \"$dependency\" added as dependency for \"$name\" component.";
    }

    protected function removeDependency($name)
    {
        $components = $this->components;

        $this->checkIfComponentExists($name);

        $dependency = $this->scriptParams->askForUserInput(
            "Enter the name of the logic component to remove as dependency:"
        );

        $this->checkIfComponentExists($dependency, true, 'logic_components');

        if (!in_array($dependency, $components[$name]))
            throw new \Exception("$this->componentTypeName component \"$name\" does not have logic component \"$dependency\" as its dependency.");

        if(($key = array_search($dependency, $components[$name])) !== false)
        {
            unset($components[$name][$key]);
        }

        $this->config->set($this->componentConfigIndex, $components);

        return "Logic component \"$dependency\" removed as dependency for \"$name\" component.";
    }

    protected abstract function genArrayOfComponentFiles($componentName);
    protected abstract function genArrayOfComponentDirectories($componentName);

    protected function checkIfComponentExists($componentName, $throw = true, $configIndexToCheck = '')
    {
        $components = $this->components;

        if (!empty($configIndexToCheck))
            $components = $this->config->getParsed($configIndexToCheck);

        if (!isset($components[$componentName]))
        {
            if ($throw)
                throw new \Exception("Component \"$componentName\" does not exists in configured components.");
            else
                return false;
        }

        return true;
    }

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     */
    public function perform()
    {
        // TODO: Implement perform() method.
    }
}