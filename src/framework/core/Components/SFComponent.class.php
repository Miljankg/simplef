<?php

namespace Core\Components;

use Core\Configuration\Config;

/**
 * SFComponent base class
 *
 * @author Miljan Pantic
 */
abstract class SFComponent {
    
    protected $name;
    protected $config = null;
    protected $logicComponents;
    
    public function __construct($name, Config $config = null, array $logicComponents = array()) {
        
        $this->name = $name;
        $this->config = $config;
        $this->logicComponents = $logicComponents;
        
    }
    
    /* Interface functions */              
    
    /***********************/


    /* Internal functions */                     
    
    /**
     * Retreives logic component.
     * 
     * @param string $logicComponentName
     * @return LogicComponent 
     * @throws Exception If component does not exists
     */
    protected function getLogicComponent($logicComponentName) {
        
        if (!array_key_exists($logicComponentName, $this->logicComponents)) {
            
            throw new \Exception("Logic $logicComponentName does not exists for the component $this->name.");
            
        }
        
        return $this->logicComponents[$logicComponentName];
        
    }


    /**********************/
    
}
