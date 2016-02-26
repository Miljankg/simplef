<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/24/2016
 * Time: 12:28 AM
 */

namespace Console\Core\Action;


use Console\Core\Config\IConfig;
use Console\Core\Parameters\ScriptParams\ScriptParams;

class Action
{
    //<editor-fold desc="Members">

    private $name;
    private $value;

    private $actionMapping;
    private $consoleConfig;
    private $scriptParams;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /**
     * Action constructor.
     *
     * @param string $name Name of the action.
     * @param string $value Value of the action.
     * @param array $actionMapping Action mapping
     * @param array $consoleConfig Console config.
     * @param ScriptParams $scriptParams
     */
    public function __construct($name, $value, array $actionMapping, array $consoleConfig, ScriptParams $scriptParams)
    {
        $this->name = $name;
        $this->value = $value;
        $this->actionMapping = $actionMapping;
        $this->consoleConfig = $consoleConfig;
        $this->scriptParams = $scriptParams;
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

    /**
     * Executes action.
     *
     * @param IConfig $config Config object.
     * @return string Output.
     * @throws \Exception If action is not mapped.
     */
    public function execute(IConfig $config)
    {
        if (!isset($this->actionMapping[$this->name]))
            throw new \Exception('No mapped action ' . $this->name);

        $type = $this->actionMapping[$this->name]['type'];

        $object = new $type(
            $this->name,
            $this->value,
            $this->actionMapping[$this->name]['config_index'],
            $config,
            $this->actionMapping[$this->name]['allowed_values'],
            $this->consoleConfig,
            $this->scriptParams
        );

        if (!$object instanceof Operation)
            throw new \Exception("Object must be type of Operation.");

        return $object->perform();
    }

    //</editor-fold>
}