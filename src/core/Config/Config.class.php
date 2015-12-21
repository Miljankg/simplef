<?php

namespace Core\Config;

/**
 * This class is used to store loaded configuration values.
 *
 * @author Miljan Pantic
 */
class Config {
    
    private $config = array();
    
    private $configClosed = false;
    
    /* Interface functions */
    
    /**
     * Merges new config with already loaded one.
     * 
     * @param array $config Config to add
     * @throws \Exception When config is closed for addition
     */
    public function addConfig($config) {
        
        if ($this->configClosed) {
            
            throw new \Exception("Config is closed for adding new fields.");
        
        }
        
        $this->config = array_merge($this->config, $config);
        
    }    
    
    /**
     * Retreives required value from loaded configuration.
     * 
     * @param string $index Index to searhc for
     * @return mixed Loaded config field value
     * @throws \Exception When requrested field does not exist
     */
    public function getValue($index) {
        
        if (!isset($this->config[$index])) {
            
            throw new \Exception("There is no config field \"$index\"");
            
        }
        
        return $this->config[$index];
        
    }
    
    /**
     * Closes config for adding new fields.
     */
    public function closeConfig() {
        
        $this->configClosed = true;
        
    }
    
    /***********************/
    
}
