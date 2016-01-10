<?php

namespace Core\Configuration;

/**
 * This class is used to store loaded configuration values.
 *
 * @author Miljan Pantic
 */
class Config {
    
    private $config = array();
    
    private $configClosed = false;
    private $namespace = "Unnamed Config";
    
    public function __construct($namespace, array $loadedConfig = array()) 
    {        
        $this->config = $loadedConfig;
        $this->namespace = $namespace;
    }
    
    /* Interface functions */
    
    /**
     * Merges new config with already loaded one.
     * 
     * @param array $config Config to add
     * @throws \Exception When config is closed for addition
     */
    public function addMultipleConfigValues(array &$config) {                
        
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
    public function set($index, $value) {                
        
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
    public function get($index) {
        
        if (!isset($this->config[$index])) {
            
            throw new \Exception("Index $index is does not exists in the {$this->namespace} config.");
        
        }
        
        return $this->config[$index];
    }       
    
    /**
     * Returns all config fields.
     * 
     * @return array Loaded config
     */
    public function getAllFields() {
        
        return $this->config;
        
    }
    
    /**
     * Close config for adding new values
     */
    public function closeConfig() {
        
        $this->configClosed = true;
        
    }
    
    /***********************/
    
    /* Internal functions */
    
    /**
     * Throws exception if configuration is closed.
     * 
     * @throws \Exception If config is cloed
     */
    private function blockIfClosed() {
        
        if ($this->configClosed) {
            
            throw new \Exception("{$this->namespace} config is closed for adding new fields.");
        
        }
        
    }
    
    /**********************/
    
}
