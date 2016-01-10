<?php

namespace Core\Components;

use Core\Configuration\Config;

/**
 * Represents OutputComponent in SF
 *
 * @author Miljan Pantic
 */
abstract class OutputComponent extends SFComponent {
    
    protected $tplEnabled = false;
    protected $tplEngine = null;
    protected $tpl = "";   
    
    protected $langObj = null;


    public function __construct($name, Config $config = null, $langObj = null, array $logicComponents) {
        parent::__construct($name, $config, $logicComponents);
        
        $this->langObj = $langObj;
        
    }


    /* Interface functions */
    
    public function runComponentLogic() {
        
        $this->execute();
        
        $return = null;
        
        if ($this->tplEnabled) {
            
            $return = $this->getTemplateContent();
            
        }
        
        return $return;
        
    }
    
    public function enableTplLoading(\Smarty $tplEngine, $tpl) {
        
        $this->tplEnabled = true;
        $this->tplEngine = $tplEngine;
        $this->tpl = $tpl;
        
    }
    
    public function disableTplLoading() {
        
        $this->tplEnabled = false;
        $this->tplEngine = null;
        $this->tpl = "";
        
    }        
    
    protected abstract function execute();    
    
    /***********************/
    
    /* Internal functions */
    
    /**
     * Retreives template content.
     * 
     * @return string Fetched template
     */
    protected function getTemplateContent() {
        
        return $this->tplEngine->fetch($this->tpl);
        
    }
    
    /**
     * Outputs passed data.
     * 
     * @param string $data Data to output.
     */
    protected function output($data) {
        
        die($data);
        
    }
    
    /**********************/
}
