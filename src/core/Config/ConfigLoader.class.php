<?php

namespace Core\Config;

/**
 * Class that loads configuration from the specific location.
 *
 * @author Miljan Pantic
 */
class ConfigLoader {
    
    private $validConfigLocations = array(
        ConfigLocations::DB,
        ConfigLocations::PHP_FILE
    );
    
    private $configLocation;
    
    public function __construct($configLocation) {
        
        $this->configLocation = $configLocation;
        
    }
    
    /* Interface functions */
    
    /**
     * 
     * @param string $configPhpFileDir config file location
     * @param DB $dbObj database object for config from DB
     * @return array Loaded configuration
     */
    public function loadConfiguration($configPhpFileDir = null, $dbObj = null) {
        
        switch ($this->configLocation) {
            
            case ConfigLocations::PHP_FILE:
                return $this->loadConfigurationFromPhpFile($configPhpFileDir);
            case ConfigLocations::DB:
                return $this->loadConfigurationFromDb($dbObj);
            default :
                throw new \Exception("Invalid config location \"$this->configLocation\"");
                
        }
        
    }
            
    
    /**********************/
            
    /* Internal functions */
    
    /**
     * Loads PHP configuration files from the specified location.
     * 
     * @param string $configPhpFileDir Directory where config php files are stored.
     * @return array Loaded configuration
     */
    private function loadConfigurationFromPhpFile($configPhpFileDir) {
        
        $config = array();
        
        $configDirs = array('user', 'system');
        
        foreach ($configDirs as $configDir) {
            
            $confDirToLoadFrom = $configPhpFileDir . $configDir . '/';
            
            foreach (glob($confDirToLoadFrom . "config_*.php") as $filename)
            {                
                require_once $filename;
            }
            
        }        
        
        return $config;
    }
    
    /**
     * Loads configuration from the DB.
     * 
     * @param DB $dbObj Database connection object
     * @return array Loaded configuration
     */
    private function loadConfigurationFromDb($dbObj) {
        
        return array();
        
    }
    
    /**********************/
    
}
