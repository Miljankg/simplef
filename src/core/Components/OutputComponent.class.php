<?php

namespace Core\Components;

require_once 'SFComponent.class.php';

/**
 * Represents OutputComponent in SF
 *
 * @author Miljan Pantic
 */
class OutputComponent extends SFComponent {
    
    private $tplEnabled = false;
    private $tplEngine = null;
    private $tpl = "";
    
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
    
    /***********************/
    
    /* Internal functions */
    
    /**
     * Retreives template content.
     * 
     * @return string Fetched template
     */
    private function getTemplateContent() {
        
        return $this->tplEngine->fetch($this->tpl);
        
    }
    
    /**********************/
}
