<?php

namespace Core;

use Core\Configuration as Conf;
use Core\Exception as Ex;
use Core\CLI as CLI;
use Core\Logging as Log;
use Core\URLUtils as URL;
use Core\Lang as Lang;
use Core\Routing as Route;
use Core\Components as Comp;

/**
 * Class which task is to boot complete Simple Framework.
 *
 * @author Miljan Pantic
 */
class SF {
    
    private $coreDirectory = "core";
    
    private $mainLoadedConfig;
    private $requiredMainConfig = array(
        'document_root',
        'config_type'        
    );  
    private $requiredSystemConfig = array(
        'is_site_multilang', 
        'default_language',
        'default_page',
        'error_log_enabled',
        'pages',
        'empty_page_index'
    );
    
    private $tplEngine = null;
    
    public function __construct(array $mainLoadedConfig) {
        
        $this->mainLoadedConfig = $mainLoadedConfig;
        
    }
    
    /* Interface functions */
    
    /**
     * Executes SF.
     */
    public function execute() {
        
        $this->bootUp();
        
        $this->display();
                                
    }
        
    /***********************/
    
    /* Internal functions */        
    
    /**
     * Boots up SF.
     */
    private function bootUp() {                
        
        // Check required fields
        $this->checkRequiredConfigFields(
                $this->mainLoadedConfig,
                $this->requiredMainConfig
                );                                    
        
        // Load core classes
        $this->loadCoreClasses();                              
        
        $this->setUpExceptionHandling();                     
        
        $this->loadAdditionalConfig();
        
        // Check required fields
        $this->checkRequiredConfigFields(
                Conf\Config::getAllFields(),
                $this->requiredSystemConfig
                );    
                        
        // Set up logger
        if (Conf\Config::get('error_log_enabled') == true) {
            
            $this->setUpLogger();                        
            
        }                          
        
        $documentRoot = Conf\Config::get('document_root');
        
        // Load libs
        $this->loadLibs($documentRoot);
        
        // Parse URL
        URL\URL::processURL(
                Conf\Config::get('is_site_multilang'), 
                Conf\Config::get('default_language'),
                Conf\Config::get('default_page'),
                Conf\Config::get('ssl_port'),
                Conf\Config::get('app_webroot_location')
                );
        
        // Load language
        $this->loadLanguage();  
        
        // Init template engine
        $this->tplEngine = $this->initTplEngine($documentRoot);
    }
    
    /**
     * Displays content.
     */
    private function display() {
        
        // Load page
        $fetchedTpl = $this->loadPage();   
        
        $this->displayContent($fetchedTpl);
        
    }


    /**
     * Loads core classes using require_once method.
     */
    private function loadCoreClasses() {
        
        $directoryIterator = new \RecursiveDirectoryIterator(
                $this->mainLoadedConfig['document_root'] . $this->coreDirectory
                );                
        $recursiveIterator = new \RecursiveIteratorIterator($directoryIterator);
        $classList = new \RegexIterator(
                $recursiveIterator, 
                '/^.+\.class\.php$/i', 
                \RecursiveRegexIterator::GET_MATCH
                );

        foreach($classList as $filePath => $object) {
            
            require_once $filePath;
            
        }
    }
    
    /**
     * Checks if all required config fields are loaded.
     * 
     * In case of exception, die is called.
     * 
     * @param array $arrayToCheck Config array to check
     * @param array $requiredFelds Required fields
     */
    private function checkRequiredConfigFields(array $arrayToCheck, array $requiredFelds) {
        
        foreach ($requiredFelds as $reqField) {
            
            if (!isset($arrayToCheck[$reqField])) {
                
                die("Configuration is missing \"{$reqField}\" field.");
                
            }
            
        }       
        
    }
    
    /**
     * Loads additioanl configuration and closes the config.
     * 
     * @return array Loaded configuration
     */
    private function loadAdditionalConfig() {
        
        $configLoader = new Conf\ConfigLoader($this->mainLoadedConfig['config_type']);
        
        $phpConfigLocation = null;
        $dbObj = null;
        
        if ($this->mainLoadedConfig['config_type'] == Conf\ConfigLocations::PHP_FILE) {
            
            $phpConfigLocation = $this->mainLoadedConfig['document_root'] . 'config/';
                                    
        }
        
        foreach ($this->mainLoadedConfig as $key => $value) {
            
            Conf\Config::add($key, $value);
            
        }
        
        $loadedConfig = $configLoader->loadConfiguration($phpConfigLocation, $dbObj);        
        
        Conf\Config::closeConfig();
        
        return $loadedConfig;
    }
    
    /**
     * Sets up Exception handler API.
     */
    private function setUpExceptionHandling() {
        
        Ex\ExceptionHandler::setIsCli(CLI\CLIUtils::isCli());
        
        set_exception_handler(array("Core\Exception\ExceptionHandler", "handleException"));
    }
    
    /**
     * Sets up Logger.
     */
    private function setUpLogger() {
        
        Log\Logger::setLogFile(Conf\Config::get('log_file'));        
        
        try {
            
            Log\Logger::setNewLine(Conf\Config::get('new_line'));
            Log\Logger::setTimestampFormat(Conf\Config::get('log_time_format'));
            
        } catch (Exception $ex) {

            // nothing to do, logger will take its own timestamp format and / or new line.
            
        }                
        
    }
    
    /**
     * Loads language.
     */
    private function loadLanguage() {
        
        Lang\Language::loadLang(
                URL\URL::getCurrentLanguage(), 
                Conf\Config::get('document_root')
                );
        
    }
    
    /**
     * Loads page.
     */
    private function loadPage() {                
        
        $tplDir = $this->tplEngine->getTemplateDir(0);
        
        $componentLoader = new Comp\SFComponentLoader(
                Conf\Config::get('document_root') . 'components/output/', 
                $tplDir . 'out_components/', 
                Conf\Config::get('output_components'), 
                $this->tplEngine
                );
        
        $pages = new Route\Pages(
                Conf\Config::get('pages'),
                Conf\Config::get('pages_out_components'),
                Conf\Config::get('pages_templates'),
                Conf\Config::get('empty_page_index'),
                $this->tplEngine,
                $tplDir . 'pages/',
                $componentLoader
                );                     
        
        $pages->pageNotFoundPage = Conf\Config::get('page_not_found_page');
        
        $currPage = URL\URL::getCurrentPage();                
        
        $this->handlePageTranslations(
                $currPage, 
                Conf\Config::get('pages'), 
                $pages->pageNotFoundPage);
        
        // assign to the main tpl
        $this->tplEngine->assign(
                'mainContent',
                $pages->getCurrentPageContent($currPage)
                );                
        
    }
    
    /**
     * Handle page translations.
     * 
     * @param string $currPage Current page
     * @param array $pages Pages from config
     * @param string $page404
     */
    private function handlePageTranslations(&$currPage, array $pages, $page404) {        
        
        if (!empty($currPage)) {
        
            foreach ($pages as $page) {

                try {

                    $pageTranslated = Lang\Language::get("page_" . $page);                    

                    if ($pageTranslated == $currPage) {

                        $currPage = $page;
                        break;

                    }

                } catch (\Exception $ex) {                    

                }

            }                        
            
            if ($currPage == URL\URL::getCurrentPage()) {
                
                try {
                    
                    Lang\Language::get("page_" . $currPage); 
                    $currPage = $page404;
                    
                } catch (\Exception $ex) {
                    
                    // move on
                    
                }                                
                
            }
        
        }           
    }

    /**
     * Loads SF libraries.
     * 
     * @param string $documentRoot
     */
    private function loadLibs($documentRoot) {
        
        $libsToLoad = Conf\Config::get('sf_libs');
        
        foreach ($libsToLoad as $library) {
            
            require_once $documentRoot . 'lib/' . $library . '/incl_lib.php';
            
        }
        
    }
    
    /**
     * Inits template engine.
     * 
     * @return Smarty Smarty object
     */
    private function initTplEngine($documentRoot) {
        
        $smarty = new \Smarty();

        $smarty->setTemplateDir($documentRoot . 'templates');
        $smarty->setCompileDir($documentRoot . 'templates_c');
        $smarty->setCacheDir($documentRoot . 'cache');
        $smarty->setConfigDir($documentRoot . 'config');				
        
        return $smarty;
        
    }
    
    /**
     * Displays page content.
     * 
     * @param string $fetchedTpl Contect of a fetched tpl.
     */
    private function displayContent($fetchedTpl) {
        
        $this->tplEngine->display('index.tpl');
        
    }
    
    /**********************/
    
}
