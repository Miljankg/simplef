<?php

namespace Framework\Core\FrameworkClasses\Components;

use Framework\Core\FrameworkClasses\Configuration\Config;
use Framework\Core\ISF;
use \Exception;

/**
 * SFComponent base class
 *
 * @author Miljan Pantic
 */
abstract class SFComponent
{
    //<editor-fold desc="Members">

    protected $name;
    protected $config = null;

    /** @var LogicComponent */
    protected $logicComponents;

    /** @var ISF */
    protected $sf = null;

    //</editor-fold>

    //<editor-fold desc="Construct">

    /**
     * SFComponent constructor.
     *
     * @param string $name Component name.
     * @param Config|null $config Config object.
     * @param LogicComponent[] $logicComponents Arrays of logic components.
     * @param ISF $sf Simple Framework instance.
     */
    public function __construct($name, Config $config = null, array $logicComponents = array(), ISF $sf)
    {
        $this->name = $name;
        $this->config = $config;
        $this->logicComponents = $logicComponents;
        $this->sf = $sf;
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Retrieves logic component.
     *
     * @param string $logicComponentName
     * @return LogicComponent
     * @throws Exception If logic component does not exists
     */
    protected function getLogicComponent($logicComponentName)
    {
        if (!array_key_exists($logicComponentName, $this->logicComponents))
            throw new Exception("Logic $logicComponentName does not exists for the component $this->name.");
        
        return $this->logicComponents[$logicComponentName];
    }

    /**
     * Retrieves class name from component name.
     *
     * @param string $componentName Component name.
     * @return string Class name.
     */
    public static function getClassName($componentName)
    {
        $separator = "_";
        $compNameArr = explode($separator, $componentName);
        $className = "";

        foreach ($compNameArr as $compNameArrElement)
            $className .= ucfirst($compNameArrElement);

        return $className;
    }

    //</editor-fold>
}
