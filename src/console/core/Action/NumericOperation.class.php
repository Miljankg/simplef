<?php

namespace Console\Core\Action;


class NumericOperation extends Operation
{
    //<editor-fold desc="Members">

    protected $allowedValues = array();

    //</editor-fold>

    //<editor-fold desc="Public functions">

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     * @throws \Exception If value is not numeric.
     */
    public function perform()
    {
        $previewValue = $this->previewValue();

        if ($previewValue !== false)
            return $previewValue;

        if (!is_numeric($this->value))
            throw new \Exception("Value must be a number.");

        if ($this->config->get($this->index) == $this->value)
            throw new \Exception("$this->name is already set to $this->value.");

        $this->config->set($this->index, $this->value);

        return "$this->name successfully set to {$this->value}.";
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
            return "Value of {$this->name} is: " . $this->config->get($this->index);

        return false;
    }

    //</editor-fold>
}