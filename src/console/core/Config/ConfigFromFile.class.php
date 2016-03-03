<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/24/2016
 * Time: 2:07 AM
 */

namespace Console\Core\Config;


class ConfigFromFile implements IConfig
{
    //<editor-fold desc="Members">

    private $configFileMapping = array();
    private $loadedConfig = array();
    private $loadedConfigParsed = array();
    private $rootDir;
    private $constantsFile;
    private $constantsToRemove = array();

    //</editor-fold>

    public function __construct($rootDir, $constantsFile, $config, $configParsed, $configFileMapping)
    {
        $this->rootDir = $rootDir;
        $this->constantsFile = $constantsFile;
        $this->loadedConfig = $config;
        $this->loadedConfigParsed = $configParsed;
        $this->configFileMapping = $configFileMapping;
    }

    public function queueConstantForRemoval($constantName)
    {
        array_push($this->constantsToRemove, $constantName);
    }

    //<editor-fold desc="IConfig functions">

    /**
     * Retrieves value by given index.
     *
     * @param string $index Index to search for.
     * @return mixed Founded value.
     * @throws \Exception If index does not exists.
     */
    public function getParsed($index)
    {
        if (!isset($this->loadedConfigParsed[$index]))
            throw new \Exception('No config index: ' . $index);

        return $this->loadedConfigParsed[$index];
    }

    /**
     * Set value in config.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setParsed($key, $value)
    {
        $this->loadedConfigParsed[$key] = $value;
    }

    /**
     * Retrieves value by given index.
     *
     * @param string $index Index to search for.
     * @return mixed Founded value.
     * @throws \Exception If index does not exists.
     */
    public function get($index)
    {
        if (!isset($this->loadedConfig[$index]))
            throw new \Exception('No config index: ' . $index);

        return $this->loadedConfig[$index];
    }

    /**
     * Set value in config.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->loadedConfig[$key] = $value;
    }

    /**
     * Save changes to the data source.
     */
    public function saveChanges()
    {
        $currFile = '';

        foreach ($this->configFileMapping as $config => $file)
        {
            if ($currFile != $file)
            {
                file_put_contents($file, '<?php' . PHP_EOL . PHP_EOL);
                $currFile = $file;
            }

            $value = var_export($this->loadedConfig[$config], true);

            $string = str_pad("\$config['{$config}']", 40) . " = {$value};" . PHP_EOL;

            file_put_contents($file, $string, FILE_APPEND | LOCK_EX);
        }

        $definedConstants = get_defined_constants('true');

        $constantFile = $this->constantsFile;

        file_put_contents($constantFile, '<?php' . PHP_EOL . PHP_EOL);

        foreach ($definedConstants['user'] as $constantName => $constantValue)
        {
            if (in_array($constantName, $this->constantsToRemove))
                continue;

            $varStr = var_export($constantValue, true);

            $string = "define('$constantName', $varStr);" . PHP_EOL;
            file_put_contents($constantFile, $string, FILE_APPEND | LOCK_EX);
        }
    }

    //</editor-fold>
}