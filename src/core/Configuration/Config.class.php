<?php

namespace Core\Configuration;

/**
 * This class is used to store loaded configuration values.
 *
 * @author Miljan Pantic
 */
class Config {
    
    public static $config = array();
    
    private static $configClosed = false;
    
    /* Interface functions */
    
    /**
     * Merges new config with already loaded one.
     * 
     * @param array $config Config to add
     * @throws \Exception When config is closed for addition
     */
    public static function addMultipleConfigValues(array $config) {
        
        if (Config::$configClosed) {
            
            throw new \Exception("Config is closed for adding new fields.");
        
        }
        
        Config::$config = array_merge(Config::$config, $config);
        
    }    
    
    /**
     * Adds value to the config array.
     * 
     * @param string $index Index to add to
     * @param mixed $value Value to add
     * @throws \Exception if Index is already present.
     */
    public static function add($index, $value) {
        
        if (isset(Config::$config[$index])) {
            
            throw new \Exception("Index $index is already present in the config.");
        
        }
        
        Config::$config[$index] = $value;
    }
    
    /**
     * Returns value from the config, by the passed index.
     * 
     * @param string $index Index to search for
     * @return mixed Value from config
     * @throws \Exception If index does not exists
     */
    public static function get($index) {
        
        if (isset(Config::$config[$index])) {
            
            throw new \Exception("Index $index is does not exists in the config.");
        
        }
        
        return Config::$config[$index];
    }       
    
    /**
     * Closes config for adding new fields.
     */
    public static function closeConfig() {
        
        Config::$configClosed = true;
        
    }
    
    /***********************/
    
}
