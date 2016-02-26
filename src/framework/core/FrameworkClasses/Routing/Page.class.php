<?php

namespace Framework\Core\FrameworkClasses\Routing;

use Framework\Core\FrameworkClasses\Components\SFComponentLoader;
use Framework\Core\FrameworkClasses\Logging\ILogger;

/**
 * Page API
 */
class Page
{
    //<editor-fold desc="Members">

    private $pageName;
    private $outComponents;
    private $template;
    private $loadSpecificComponent = false;
    public $jsLoad = false;
    public $cssLoad = false;

    /** @var SFComponentLoader */
    private $componentLoader = null;

    /** @var ILogger */
    private $logger = null;

    public $tplName;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    /**
     * Page constructor.
     * @param string $pageName Name of the page.
     * @param string[] $outComponents Output components to load.
     * @param string $template Template name to load.
     * @param $tplName
     * @param SFComponentLoader $componentLoader
     * @param ILogger $logger
     * @param $loadSpecificComponent
     * @param bool $jsLoad Load js?
     * @param bool $cssLoad Load css?
     */
    public function __construct(
            $pageName, 
            array $outComponents, 
            $template,
            $tplName,
            SFComponentLoader $componentLoader,
            ILogger $logger,
            $loadSpecificComponent,
            $jsLoad,
            $cssLoad
            ) {
        
        $this->init(
            $pageName,
            $outComponents,
            $template,
            $tplName,
            $componentLoader,
            $logger,
            $loadSpecificComponent,
            $jsLoad,
            $cssLoad
        );
        
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

        $componentsToLoad = $this->outComponents;

        if ($this->loadSpecificComponent !== false)
        {
            $componentsToLoad = array($this->loadSpecificComponent);
        }

        $outComponentData = $this->loadOutputComponents($componentsToLoad);
                        
        foreach ($outComponentData as $outComponentName => $outComponentValues)
        {
            $header[$outComponentName]['js'] = $outComponentValues['js'];
            $header[$outComponentName]['css'] = $outComponentValues['css'];
            
            $outComponentContent = $outComponentValues['content'];

            if ($this->loadSpecificComponent !== false)
                return $outComponentContent;

            $tplEngine->assign('oc_' . $outComponentName, $outComponentContent);
        }
        
        return $tplEngine->fetch($this->template);
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Initializes Page class.
     *
     * @param string $pageName Name of the page.
     * @param string[] $outComponents Output components to load.
     * @param string $template Template name to load.
     * @param string $tplName Template name.
     * @param SFComponentLoader $componentLoader
     * @param ILogger $logger
     * @param mixed|bool|string $loadSpecificComponent Specific component to load.
     * @param bool $jsLoad Load JS?
     * @param bool $cssLoad Load CSS?
     * @throws \Exception If template is empty.
     */
    private function init(
            $pageName, 
            array $outComponents, 
            $template,
            $tplName,
            SFComponentLoader $componentLoader,
            ILogger $logger,
            $loadSpecificComponent,
            $jsLoad,
            $cssLoad
            )
    {
        $this->pageName = $pageName;
        $this->outComponents = $outComponents;
        $this->componentLoader = $componentLoader;
        $this->logger = $logger;
        $this->loadSpecificComponent = $loadSpecificComponent;
        $this->jsLoad = $jsLoad;
        $this->cssLoad = $cssLoad;
        
        if (empty($template))
            throw new \Exception("Template path can not be empty.");
        
        $this->template = $template;
        $this->tplName = $tplName;
    }

    /**
     * Loads output components.
     *
     * @param string[] $componentsToLoad Components to load.
     * @return array Loaded components data.
     */
    private function loadOutputComponents(array $componentsToLoad)
    {
        return $this->componentLoader->loadOutputComponents($componentsToLoad);
    }

    //</editor-fold>
}

