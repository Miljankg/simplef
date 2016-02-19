<?php

namespace Core\Routing;

use Core\Components\SFComponentLoader;
use Core\Logging\ILogger;

/**
 * Page API
 */
class Page
{
    //<editor-fold desc="Members">

    private $pageName;
    private $outComponents;
    private $template;

    /** @var SFComponentLoader */
    private $componentLoader = null;

    /** @var ILogger */
    private $logger = null;

    public $tplName;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    public function __construct(
            $pageName, 
            array $outComponents, 
            $template,
            $tplName,
            SFComponentLoader $componentLoader,
            ILogger $logger
            ) {
        
        $this->init($pageName, $outComponents, $template, $tplName, $componentLoader, $logger);
        
    }

    //</editor-fold>

    //<editor-fold desc="Public functions">

    /** @noinspection PhpUndefinedClassInspection */

    /**
     * Retrieves page content.
     *
     * @param \Smarty $tplEngine Template engine.
     * @param array $header Variable for storing header for each output component.
     * @return string Page content.
     */
    public function getContent(
        /** @noinspection PhpUndefinedClassInspection */
        \Smarty $tplEngine,
        array &$header
        ) {
        
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

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    private function init(
            $pageName, 
            array $outComponents, 
            $template,
            $tplName,
            SFComponentLoader $componentLoader,
            ILogger $logger
            )
    {
        $this->pageName = $pageName;
        $this->outComponents = $outComponents;
        $this->componentLoader = $componentLoader;
        $this->logger = $logger;
        
        if (empty($template))
            throw new \Exception("Template path can not be empty.");
        
        $this->template = $template;
        $this->tplName = $tplName;
    }
    
    private function loadOutputComponents()
    {
        return $this->componentLoader->loadOutputComponents($this->outComponents);
    }

    //</editor-fold>
}

