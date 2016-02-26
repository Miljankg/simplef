<?php

namespace Framework\Core\FrameworkClasses\Configuration;

use \Exception;

/**
 * This class is used to store loaded configuration values.
 *
 * @author Miljan Pantic
 */
class Config implements IConfig
{
    //<editor-fold desc="Members">

    private $config = array();
    
    private $configClosed = false;
    private $namespace = "Un-named Config";

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /**
     * Config constructor.
     *
     * @param string $namespace Name of the configuration.
     * @param array $loadedConfig Already loaded config.
     */
    public function __construct($namespace, array $loadedConfig = array())
    {        
        $this->config = $loadedConfig;
        $this->namespace = $namespace;
    }

    //</editor-fold>

    //<editor-fold desc="IConfig functions">

    /**
     * Merges new config with already loaded one.
     *
     * @param array $config Config to add
     * @throws \Exception When config is closed for addition
     */
    public function addMultipleConfigValues(array &$config)
    {
        $this->blockIfClosed();

        $this->config = array_merge($this->config, $config);
    }

    /**
     * Adds value to the config array.
     *
     * @param string $index Index to add to
     * @param mixed $value Value to add
     * @throws \Exception if Index is already present.
     */
    public function set($index, $value)
    {
        $this->blockIfClosed();

        $this->config[$index] = $value;
    }

    /**
     * Returns value from the config, by the passed index.
     *
     * @param string $index Index to search for
     * @return mixed Value from config
     * @throws \Exception If index does not exists
     */
    public function get($index)
    {
        if (!isset($this->config[$index]))
            throw new \Exception("Index \"$index\" is does not exists in the \"{$this->namespace}\" config.");

        return $this->config[$index];
    }

    /**
     * Returns all config fields.
     *
     * @return array Loaded config
     */
    public function getAllFields()
    {
        return $this->config;
    }

    /**
     * Close config for adding new values
     */
    public function closeConfig()
    {
        if ($this->configClosed)
            throw new Exception("Config already closed.");

        $this->configClosed = true;
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Throws exception if configuration is closed.
     * 
     * @throws \Exception If config is closed
     */
    private function blockIfClosed()
    {
        if ($this->configClosed)
            throw new \Exception("{$this->namespace} config is closed for adding new fields.");
    }

    //</editor-fold>
}
