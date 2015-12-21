<?php

namespace Core;

/**
 * Class which task is to boot complete Simple Framework.
 *
 * @author Miljan Pantic
 */
class Boot {
    
    private $mainLoadedConfig;
    private $requiredMainConfig = array(
        'core_classes_dir',
        'core_classes',
        'document_root',
        'config_type'
    );
    
    private $loadedGlobalConfig;
    
    public function __construct(array $mainLoadedConfig) {
        
        $this->mainLoadedConfig = $mainLoadedConfig;
        
    }
    
    public function startSimpleFramework() {
        
        // Check required fields
        $this->checkRequiredMainConfigFields();    
        
        // Load core classes
        $this->loadCoreClasses();      
        
        // Load additional configs        
        $this->loadedGlobalConfig = new Config\Config();
        
        $this->loadedGlobalConfig->addConfig($this->loadAdditionalConfig());
        
    }
    
    /* Internal functions */        
    
    /**
     * Loads core classes using require_once method.
     */
    private function loadCoreClasses() {
        
        $coreClassPath = $this->mainLoadedConfig['document_root'] 
                . $this->mainLoadedConfig['core_classes_dir'] . '\\';
        
        foreach ($this->mainLoadedConfig['core_classes'] as $coreClass) {
            
            require_once $coreClassPath . $coreClass . '.class.php';
            
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
        
        $configLoader = new Config\ConfigLoader($this->mainLoadedConfig['config_type']);
        
        $phpConfigLocation = null;
        $dbObj = null;
        
        if ($this->mainLoadedConfig['config_type'] == Config\ConfigLocations::PHP_FILE) {
            
            $phpConfigLocation = $this->mainLoadedConfig['document_root'] . 'config/';
                                    
        }
        
        return $configLoader->loadConfiguration($phpConfigLocation, $dbObj);        
    }
    
    /**********************/
    
}
