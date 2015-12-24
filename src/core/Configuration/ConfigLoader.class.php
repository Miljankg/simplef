<?php

namespace Core\Configuration;

use Core\IO as IOOps;

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
    
    private $configTypes = array('user', 'system');
    
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
     * Loads PHP configuration files from the specified location. Function includes
     * all config files, except main config and those files will contain Config::add entries.
     * 
     * @param string $configPhpFileDir Directory where config php files are stored.
     */
    private function loadConfigurationFromPhpFile($configPhpFileDir) {                        
        
        foreach ($this->configTypes as $configDir) {
            
            $confDirToLoadFrom = $configPhpFileDir . $configDir . '/';
            
            $filesToLoad = IOOps\File::getFileList(
                    $confDirToLoadFrom,
                    '/.*config_.*\.php/',
                    false
                    );
            
            foreach ($filesToLoad as $fileToLoad) {
                
                require_once $fileToLoad;
                
            }
            
        }                        
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
