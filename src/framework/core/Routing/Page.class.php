<?php

namespace Core\Routing;

use Core\Components\SFComponentLoader;

/**
 * Page API
 */
class Page {        
    
    private $pageName;
    private $outComponents;
    private $template;
    private $componentLoader = null;

    public $tplName;
    
    public function __construct(
            $pageName, 
            array $outComponents, 
            $template,
            $tplName,
            SFComponentLoader $componentLoader
            ) {
        
        $this->init($pageName, $outComponents, $template, $tplName, $componentLoader);
        
    }
    
    /* Interface functions */    
    public function getContent(\Smarty $tplEngine, array &$header) {
        
        $header = array();
        
        $outComponentData = $this->loadOutputComponents();
                        
        foreach ($outComponentData as $outComponentName => $outComponentValues) {
            
            $header[$outComponentName]['js'] = $outComponentValues['js'];
            $header[$outComponentName]['css'] = $outComponentValues['css'];
            
            $outComponentContent = $outComponentValues['content'];
            
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
            $tplName,
            SFComponentLoader $componentLoader
            ) {
                
        $this->pageName = $pageName;
        $this->outComponents = $outComponents;
        $this->componentLoader = $componentLoader;
        
        if (empty($template)) {
            
            throw new \Exception("Template path can not be empty.");
            
        }
        
        $this->template = $template;
        $this->tplName = $tplName;
        
    }
    
    private function loadOutputComponents() {
        
        return $this->componentLoader->loadOutputComponents($this->outComponents);             
        
    }
    
    /**********************/
    
    
    
}

