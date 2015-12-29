<?php

namespace Core\URLUtils;

/**
 * URL Utilites
 *
 * @author Miljan Pantic
 */
class URL {
    
    private static $pageName = "";
    private static $currLang = "";
    private static $protocol = "";
    private static $mainUrl = "";
    private static $mainUrlNoLang = "";
    private static $urlParts = array();
    
    /* Interface functions */
    
    /**
     * Parse URL and populates internal URL class values.
     * 
     * @param bool $multilanguage Is site multilanguage or not
     * @param string $defaultLanguage Default language from config
     * @param string $defaultPage Default page from config
     * @param string $sslPort SSL Port from config
     * @param string $siteName Site name from config
     */
    public static function processURL(
            $multilanguage, 
            $defaultLanguage, 
            $defaultPage,
            $sslPort,
            $siteName            
            ) {
        
        URL::parseUrl($multilanguage, $defaultLanguage, $defaultPage);
        URL::determineProtocol($sslPort);
        URL::generateMainUrlVals(URL::$protocol, $siteName, URL::$currLang);
    }        
    
    /**
     * Returns current language.
     * 
     * @return string Current language.
     */
    public static function getCurrentLanguage() {
        
        return URL::$currLang;
        
    }
    
    /**
     * Return current page name.
     * 
     * @return string Current page name.
     */
    public static function getCurrentPage() {
        
        return URL::$pageName;
        
    }
    
    /**
     * Returns current protocol from URL.
     * 
     * @return string Protocol https or http
     */
    public static function getProtocol() {
        
        return URL::$protocol;
        
    }
    
    /**
     * Returns main url.
     * 
     * @return string Main URL
     */
    public static function getMainUrl() {
        
        return URL::$mainUrl;
        
    }
    
     /**
     * Returns main url without lang.
     * 
     * @return string Main URL with no lang
     */
    public static function getMainUrlNoLang() {
        
        return URL::$mainUrlNoLang;
        
    }
    
    /**
     * Redirects to a given location.
     * 
     * @param string $location
     */
    public static function redirect($location) {
        
        header("Location: $location");
        exit;
        
    }
    
    /**
     * Retreives URL Parts.
     * 
     * @return array URL Parts.
     */
    public static function getUrlParts() {
        
        return URL::$urlParts;
        
    }
    
    /**
     * Parses URL and populates URL class internal fields.
     * 
     * @param bool $multilanguage Is site multilanguage.
     * @param string $defaultLanguage 
     * @param string $defaultPage
     */
    public static function parseUrl(
            $multilanguage, 
            $defaultLanguage, 
            $defaultPage
            )
    {       
        $urlStr = '';
        $urlPartsStartIndex = 1;
        $pageName = '';
        
        if (isset($_GET['pageName'])) {
            
            $urlStr = htmlentities(addslashes($_GET['pageName']), ENT_NOQUOTES, 'UTF-8');
            
        }        

        $urlArr = explode("/", $urlStr);
        
        if ($multilanguage) {                                                
            
            // if lang is present in the url
            if (isset($urlArr[0]) && $urlArr[0] != "" && strlen($urlArr[0]) == 2) {
                
                $urlPartsStartIndex = 2;
                
                $language = $urlArr[0];

                if (isset($urlArr[1]) && $urlArr[1] != "")
                {
                    $pageName = $urlArr[1];
                }
                
            } else {
                $pageName = $urlArr[0];
                $language = $defaultLanguage;
            }           
            
        } else {
            
            $pageName = $urlStr;            
            $language = $defaultLanguage;
            
        }

        if (strlen($pageName) == 0) {
            
            $pageName = $defaultPage;
            
        }
        
        for ($i = $urlPartsStartIndex; $i < count($urlArr); $i++) {
            
            URL::$urlParts[] = $urlArr[$i];
            
        }
        
        URL::$currLang = $language;
        URL::$pageName = $pageName;        
    }
    
    /***********************/
    
    /* Internal functions */        
    
    /**
     * Determines http or https.
     * 
     * @param string $sslPort
     */
    private static function determineProtocol($sslPort) {
        
        URL::$protocol = 
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
                || $_SERVER['SERVER_PORT'] == $sslPort) 
                ? "https://" : "http://";
        
    }
    
    /**
     * Generates main url and main url with no language.
     * 
     * @param string $protocol http or https
     * @param string $siteName Site name from main config
     * @param string $currLang Current language
     */
    private static function generateMainUrlVals($protocol, $siteName, $currLang) {
        
        URL::$mainUrlNoLang = 
                $protocol . 
                $_SERVER['HTTP_HOST'] . 
                str_replace('\\', '/', $siteName) . 
                '/';
        
        URL::$mainUrl = URL::$mainUrlNoLang . $currLang . "/";
        
    }
    
    /**********************/
}
