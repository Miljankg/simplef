<?php

namespace Core;

use Core\Configuration as Conf;

/**
 * Class which task is to boot complete Simple Framework.
 *
 * @author Miljan Pantic
 */
class Boot {
    
    private $coreDirectory = "core";
    
    private $mainLoadedConfig;
    private $requiredMainConfig = array(
        'document_root',
        'config_type'
    );   
    
    public function __construct(array $mainLoadedConfig) {
        
        $this->mainLoadedConfig = $mainLoadedConfig;
        
    }
    
    public function startSimpleFramework() {
        
        // Check required fields
        $this->checkRequiredMainConfigFields();    
        
        // Load core classes
        $this->loadCoreClasses();              
        
        $this->loadAdditionalConfig();
        
        print_r(Conf\Config::$config);
        
    }
    
    /* Internal functions */        
    
    /**
     * Loads core classes using require_once method.
     */
    private function loadCoreClasses() {
        
        $directoryIterator = new \RecursiveDirectoryIterator(
                $this->mainLoadedConfig['document_root'] . $this->coreDirectory
                );                
        $recursiveIterator = new \RecursiveIteratorIterator($directoryIterator);
        $classList = new \RegexIterator(
                $recursiveIterator, 
                '/^.+\.class\.php$/i', 
                \RecursiveRegexIterator::GET_MATCH
                );

        foreach($classList as $filePath => $object) {
            
            require_once $filePath;
            
        }
    }
    
    /**
     * Checks if all required main config fields are loaded.
     * 
     * In case of exception, die is called.
     */
    private function checkRequiredMainConfigFields() {
        
        foreach ($this->requiredMainConfig as $reqField) {
            
            if (!isset($this->mainLoadedConfig[$reqField])) {
                
                die("Main system configuration is missing \"{$reqField}\" field.");
                
            }
            
        }       
        
    }
    
    /**
     * Loads additioanl configuration.
     * 
     * @return array Loaded configuration
     */
    private function loadAdditionalConfig() {
        
        $configLoader = new Conf\ConfigLoader($this->mainLoadedConfig['config_type']);
        
        $phpConfigLocation = null;
        $dbObj = null;
        
        if ($this->mainLoadedConfig['config_type'] == Conf\ConfigLocations::PHP_FILE) {
            
            $phpConfigLocation = $this->mainLoadedConfig['document_root'] . 'config/';
                                    
        }
        
        return $configLoader->loadConfiguration($phpConfigLocation, $dbObj);        
    }
    
    /**********************/
    
}
