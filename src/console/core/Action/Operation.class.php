<?php

namespace Console\Core\Action;

use Console\Core\Config\IConfig;
use Console\Core\Parameters\ScriptParams\ScriptParams;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

abstract class Operation
{
    //<editor-fold desc="Members">

    protected $name;
    protected $value;
    protected $index;
    protected $allowedValues;
    protected $previewValueValue = 'preview_value';
    protected $consoleConfig;
    protected $nl = PHP_EOL;
    protected $dnl;

    /** @var ScriptParams */
    protected $scriptParams;

    /** @var IConfig */
    protected $config;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    public function __construct($name, $value, $index, IConfig $config, array $allowedValues, array $consoleConfig, ScriptParams $scriptParams)
    {
        $this->init($name, $value, $index, $config, $allowedValues, $consoleConfig, $scriptParams);

        if ($value != $this->previewValueValue && !empty($this->allowedValues) && !in_array($value, $this->allowedValues))
            throw new \Exception("Value {$value} is not allowed value for action {$this->name}." . PHP_EOL . PHP_EOL . $this->allowedValuesToStr());
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Initializes operation object.
     *
     * @param string $name Name of the operation.
     * @param string $value Action value.
     * @param string $index Index to work with.
     * @param IConfig $config Config value to work with.
     * @param array $allowedValues Allowed values.
     * @param array $consoleConfig
     * @param ScriptParams $scriptParams
     */
    protected function init($name, $value, $index, IConfig $config, array $allowedValues, array $consoleConfig, ScriptParams $scriptParams)
    {
        $this->name = $name;
        $this->value = $value;
        $this->config = $config;
        $this->index = $index;
        $this->allowedValues = $allowedValues;
        $this->scriptParams = $scriptParams;
        $this->consoleConfig = $consoleConfig;
        $this->dnl = $this->nl . $this->nl;
    }

    /**
     * Converts allowed values to string.
     *
     * @return string
     */
    protected function allowedValuesToStr()
    {
        $str = 'No allowed values specified';

        if (!empty($this->allowedValues))
        {
            $str = "Allowed values are: " . PHP_EOL;

            $str .= implode(', ' . PHP_EOL, $this->allowedValues);
        }

        return $str;
    }

    /**
     * Should display the value if preview_value is passed.
     *
     * @return mixed
     */
    protected abstract function previewValue();

    protected function createPhpFile($path, $content = '', $noPhpTag = false)
    {
        if (empty($path))
            throw new \Exception('Path cannot be empty');

        if(!file_exists(dirname($path)))
            mkdir(dirname($path), 0777, true);

        $content = ($noPhpTag) ? $content : '<?php' . $this->dnl . $content;

        file_put_contents($path, $content);
    }

    protected function deleteDirectory($dir)
    {
        if (!file_exists($dir))
            return;

        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }

    protected function arrToStr(array $array, $separator = ', ')
    {
        return implode($separator, $array);
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     */
    public abstract function perform();


    //</editor-fold>
}