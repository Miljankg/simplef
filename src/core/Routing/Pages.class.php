<?php

namespace Core\Routing;

use Core\URLUtils as URL;
use Core\Components as Comp;

/**
 * Handles multiple pages operations
 *
 * @author Miljan Pantic
 */
class Pages {
    
    private $pages = array();
    private $tplEngine = null;
    private $emptyPageIndex = "";   
    private $pagesTplDir = "";
    
    public $pageNotFoundPage = "404";
    
    public function __construct(
            array $configuredPages,
            array $configuredOutComponents,
            array $configuredTemplates,
            $emptyPageIndex,
            \Smarty $tplEngine,
            $pagesTplDir,
            Comp\SFComponentLoader $componentLoader
            ) {
        
        $this->init(
                $configuredPages, 
                $configuredOutComponents,
                $configuredTemplates,
                $emptyPageIndex,
                $tplEngine,
                $pagesTplDir,
                $componentLoader
                );
    }    
    
    /* Interface functions */
    
    /**
     * Retreive current page content.
     * 
     * @param type $currentPageName
     * @return string Page content
     * @throws \Exception if page 404 is not configured (prevents redirection loop).
     */
    public function getCurrentPageContent($currentPageName) {
        
        $this->handleEmptyPage($currentPageName);
        
        if (!isset($this->pages[$currentPageName])) {
            
            // Prevent 404 redirection loop if 404 page is not configured
            if ($currentPageName == $this->pageNotFoundPage) {
                
                throw new \Exception("Page {$this->pageNotFoundPage} is not configured.");
                
            }
            
            $this->redirectTo404();
            
        }
        
        $page = $this->pages[$currentPageName];
        
        return $page->getContent($this->tplEngine);
    }       
        
    /***********************/
    
    /* Internal functions */
    
    /**     
     * Initalizes pages.
     * 
     * @param array $configuredPages
     * @param array $configuredOutComponents
     * @param array $configuredTemplates
     * @param string $emptyPageIndex 
     * @param Smarty $tplEngine
     * @param string $pagesTplDir Pages template dir
     */
    private function init(            
            array $configuredPages,
            array $configuredOutComponents,
            array $configuredTemplates,
            $emptyPageIndex,
            \Smarty $tplEngine,
            $pagesTplDir,
            Comp\SFComponentLoader $componentLoader) {
        
        $this->emptyPageIndex = $emptyPageIndex;
        $this->tplEngine = $tplEngine;
        $this->pagesTplDir = $pagesTplDir;
        
        foreach ($configuredPages as $pageName) {
            
            $outCompsToLoad = array();
            $tplToLoad = "";
            
            // Handle out components
            if (isset($configuredOutComponents[$pageName])) {
                
                $outCompsToLoad = $configuredOutComponents[$pageName];
                
            }
            
            // Handle template
            if (isset($configuredTemplates[$pageName])) {
                
                $tplToLoad = $configuredTemplates[$pageName];
                
            }
            
            if (empty($tplToLoad)) {
                
                $tplToLoad = $this->pagesTplDir . $pageName . ".tpl";
                
            }
            
            // Populate pages array
            $this->pages[$pageName] = new Page(
                    $pageName,
                    $outCompsToLoad,
                    $tplToLoad,
                    $componentLoader
                    );
            
        }                
                
    }


    /**
     * Redirect to 404.    
     */
    private function redirectTo404() {
        
        $location = URL\URL::getMainUrl() . $this->pageNotFoundPage;
            
        URL\URL::redirect($location); 
        
    }        
    
    /**
     * Sets empty page index if current page is empty string.
     * 
     * @param string $currPage Current page
     */
    private function handleEmptyPage(&$currPage) {
        
        if (empty($currPage)) {
            
            $currPage = $this->emptyPageIndex;
            
        }
        
    }

    /**********************/
    
}
