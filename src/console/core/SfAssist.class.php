<?php

namespace Console\Core;

use Console\Core\Action\Action;
use Console\Core\Config\IConfig;
use Console\Core\Parameters\ScriptParams\ScriptParams;

class SfAssist
{
    //<editor-fold desc="Members">

    /** @var ScriptParams */
    private $scriptParams;
    private $config = null;
    private $consoleConfig;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /**
     * SfAssist constructor.
     *
     * @param array $arguments Script arguments.
     * @param IConfig $config Config object.
     * @param array $consoleConfig Console config.
     */
    public function __construct(array $arguments, IConfig $config, array $consoleConfig)
    {
        $this->scriptParams = new ScriptParams($arguments);
        $this->config = $config;
        $this->consoleConfig = $consoleConfig;
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

    /**
     * Executes sf console actions.
     *
     * @param array $actionMapping Action mapping.
     */
    public function execute(array $actionMapping)
    {
        $action = $this->scriptParams->getAction();
        $value = $this->scriptParams->getValue();

        $actionObj = new Action($action, $value, $actionMapping, $this->consoleConfig, $this->scriptParams);

        $output = $actionObj->execute($this->config);

        $this->config->saveChanges();

        $this->output($output);
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Outputs given text.
     *
     * @param string $text Text to output.
     */
    private function output($text)
    {
        echo $text;
    }

    //</editor-fold>
}