<?php

namespace Core\Lang;

/**
 * Language API
 *
 * @author Miljan Pantic
 */
class Language {
    
    private $langArray = array();
    
    private $namespace = "";
    
    public function __construct($namespace) {
        
        $this->namespace = $namespace;
        
    }
    
    /* Interface functions */
    
    /**
     * Loads required language;
     * 
     * @param string $langToLoad
     * @param string $langRoot
     */
    public function loadLang($langToLoad, $langRoot) {
        
        if (!empty($this->langArray)) {
            
            throw new \Exception("Lang array \"{$this->namespace}\" already loaded.");
            
        }
        
        $lang = array();
        
        $path = $langRoot . $langToLoad . "/$langToLoad.php";
        
        require_once $path;
        
        $this->langArray = $lang;  
        
    }
    
    /**
     * Returns value of the passed index.
     * 
     * @param string $index Index to search for.
     * @return string Language entry value
     * @throws \Exception If index does not exists.
     */
    public function get($index) {
        
        if (!isset($this->langArray[$index])) {
            
            throw new \Exception("Lang array \"{$this->namespace}\" does not have $index field.");
            
        }        
        
        return $this->langArray[$index];
    }
    
    /***********************/
    
}
