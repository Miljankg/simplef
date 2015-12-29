<?php

namespace Core\Lang;

/**
 * Language API
 *
 * @author Miljan Pantic
 */
class Language {
    
    private static $langArray = array();
    
    /* Interface functions */
    
    /**
     * Loads required language;
     * 
     * @param string $langToLoad
     * @param string $documentRoot
     */
    public static function loadLang($langToLoad, $documentRoot) {
        
        if (!empty(Language::$langArray)) {
            
            throw new \Exception("Lang array already loaded.");
            
        }
        
        $lang = array();
        
        $path = $documentRoot . "lang/" . $langToLoad . "/$langToLoad.php";
        
        require_once $path;
        
        Language::$langArray = $lang;        
    }
    
    /**
     * Returns value of the passed index.
     * 
     * @param string $index Index to search for.
     * @return string Language entry value
     * @throws \Exception If index does not exists.
     */
    public static function get($index) {
        
        if (!isset(Language::$langArray[$index])) {
            
            throw new \Exception("Lang array does not have $index field.");
            
        }        
        
        return Language::$langArray[$index];
    }
    
    /***********************/
    
}
