<?php

namespace Core\Routing;

use Core\URLUtils as URL;

/**
 * Pages API
 */
class Pages {    
    
    public $pageNotFoundPage = "404";
    
    /* Interface functions */
    
    /**
     * Retreives modules to load for the current page.
     * 
     * @param array $loadedPagesConfig
     * @param string $currentPage
     * @param string $mainUrl
     * @param string $emptyPageIndex
     * @return array Modules to load
     */
    public function getModulesToLoad(
            array $loadedPagesConfig, 
            $currentPage, 
            $mainUrl, 
            $emptyPageIndex
            ) {
        
        $this->handleEmptyPage($currentPage, $emptyPageIndex);       
                 
        return $this->genListOfModulesToLoad(
                $loadedPagesConfig, 
                $currentPage, 
                $mainUrl
                );
        
    }
        
    /***********************/
    
    /* Internal functions */
    
    /**
     * Redirect to 404.
     * 
     * @param string $mainUrl Main URL
     */
    private function redirectTo404($mainUrl) {
        
        $location = $mainUrl . $this->pageNotFoundPage;
            
        URL\URL::redirect($location); 
        
    }
    
    /**
     * Retreives modules to load for the specified page.
     * 
     * @param array $loadedPagesConfig
     * @param string $currentPage
     * @param string $mainUrl
     * @throws \Exception If 404 page is not configured.
     */
    private function genListOfModulesToLoad(array $loadedPagesConfig, $currentPage, $mainUrl) {
        
        if (!isset($loadedPagesConfig[$currentPage])) {
            
            if ($currentPage == $this->pageNotFoundPage) {
                
                throw new \Exception("Page {$this->pageNotFoundPage} is not configured.");
                
            }                                               
            
            $this->redirectTo404($mainUrl);
            
        }
        else if ($currentPage == $this->pageNotFoundPage 
                && $currentPage != URL\URL::getCurrentPage()) {
            
            $this->redirectTo404($mainUrl);
            
        }
                 
        return $loadedPagesConfig[$currentPage];
        
    }
    
    /**
     * Sets empty page index if current page is empty string.
     * 
     * @param string $currPage Current page
     * @param string $emptyPageIndex Empty page index to set.
     */
    private function handleEmptyPage(&$currPage, $emptyPageIndex) {
        
        if (empty($currPage)) {
            
            $currPage = $emptyPageIndex;
            
        }
        
    }

    /**********************/
    
}

