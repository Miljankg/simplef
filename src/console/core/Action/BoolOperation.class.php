<?php

namespace Console\Core\Action;

use Console\Core\Config\IConfig;
use Console\Core\Parameters\ScriptParams\ScriptParams;

class BoolOperation extends Operation
{
    //<editor-fold desc="Constructor">

    public function __construct($name, $value, $index, IConfig $config, array $allowedValues, $consoleConfig, ScriptParams $scriptParams)
    {
        $allowedValues = array('enable', 'disable');

        parent::__construct($name, $value, $index, $config, $allowedValues, $consoleConfig, $scriptParams);
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

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

        $boolValue = false;
        $word = 'disabled';

        if ($this->value == 'enable')
        {
            $boolValue = true;
            $word = 'enabled';
        }

        if ($this->config->get($this->index) === $boolValue)
            throw new \Exception("$this->name already $word.");

        $this->config->set($this->index, $boolValue);

        return "$this->name successfully $word.";
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Should display the value if preview_value is passed.
     *
     * @return mixed
     */
    protected function previewValue()
    {
        if ($this->previewValueValue == $this->value)
        {
            $valStr = ($this->config->get($this->index)) ? 'true' : 'false';

            return "Value of {$this->name} is: " . $valStr;
        }

        return false;
    }

    //</editor-fold>
}