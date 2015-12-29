<?php

namespace Core\Routing;

use Core\Components as Comp;

/**
 * Page API
 */
class Page {        
    
    private $pageName;
    private $outComponents;
    private $template;
    private $componentLoader = null;
    
    public function __construct(
            $pageName, 
            array $outComponents, 
            $template,
            Comp\SFComponentLoader $componentLoader
            ) {
        
        $this->init($pageName, $outComponents, $template, $componentLoader);
        
    }
    
    /* Interface functions */
    
    public function getContent(\Smarty $tplEngine) {
        
        $outComponentsContents = $this->loadOutputComponents();
        
        foreach ($outComponentsContents as $outComponentName => $outComponentContent) {
            
            $tplEngine->assign($outComponentName, $outComponentContent);
            
        }
        
        return $tplEngine->fetch($this->template);
        
    }
    
    /***********************/
    
    /* Internal functions */
    
    private function init(
            $pageName, 
            array $outComponents, 
            $template,
            Comp\SFComponentLoader $componentLoader
            ) {
                
        $this->pageName = $pageName;
        $this->outComponents = $outComponents;
        $this->componentLoader = $componentLoader;
        
        if (empty($template)) {
            
            throw new \Exception("Template path can not be empty.");
            
        }
        
        $this->template = $template;                
        
    }
    
    private function loadOutputComponents() {
        
        return $this->componentLoader->loadOutputComponents($this->outComponents);             
        
    }
    
    /**********************/
    
    
    
}

