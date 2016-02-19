<?php

namespace Core\Components;

use Core\Configuration\Config;
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

    //</editor-fold>

    //<editor-fold desc="Construct">

    /**
     * SFComponent constructor.
     *
     * @param string $name Component name.
     * @param Config|null $config Config object.
     * @param LogicComponent[] $logicComponents Array of logic components.
     */
    public function __construct($name, Config $config = null, array $logicComponents = array())
    {
        $this->name = $name;
        $this->config = $config;
        $this->logicComponents = $logicComponents;
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

    //</editor-fold>
}
