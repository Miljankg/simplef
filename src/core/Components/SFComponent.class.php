<?php

namespace Core\Components;

/**
 * SFComponent base class
 *
 * @author Miljan Pantic
 */
abstract class SFComponent {
    
    private $name;
    private $config = array();
    
    public function __construct($name) {
        
        $this->init($name);
        
    }
    
    /* Interface functions */
    
    public function runComponentLogic() {
        
        $this->execute();                   
        
        return null;
        
    }       
    
    /***********************/


    /* Internal functions */
    
    protected function execute() {
        
        
        
    }
    
    protected function init($name) {
        
        $this->name = $name;
        
    }
    
    /**********************/
    
}
