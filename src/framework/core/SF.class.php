<?php

namespace Core;

use Core\Configuration\ConfigLoader;
use Core\Configuration\Config;
use Core\Configuration\ConfigLocations;
use Core\Exception\ExceptionHandler;
use Core\CLI\CLIUtils;
use Core\Logging\Logger;
use Core\URLUtils\URL;
use Core\Routing\Pages;
use Core\Components\SFComponentLoader;
use Core\Lang\Language;
use Core\Database\DB;

/**
 * Class which task is to boot complete Simple Framework.
 *
 * @author Miljan Pantic
 */
class SF {
    
    private $coreDirectory = "core";
    
    private $db = null;
    
    public static $config = null;
    public static $lang = null;
    
    private $mainLoadedConfig;
    private $requiredMainConfig = array(
        'display_errors',
        'error_reporting',
        'debug_level',
        'ds',
        'app_webroot_dir',
        'document_root',
        'framework_dir',
        'app_dir',
        'main_url',
        'protocol',
        'main_url_no_lang',
        'current_page',
        'current_language',
        'url_parts',
        'config_type',
        'config_db'
    );  
    
    private $requiredSystemConfig = array(
        'output_components_url',
        'index_url',
        'main_lang_dir',
        'ssl_port',
        'out_comp_ns',
        'is_cli',
        'is_site_multilang',
        'default_language',
        'available_langs',
        'lang_dir',
        'sf_libs',
        'error_log_enabled',
        'log_file',
        'log_time_format',
        'new_line',
        'output_components',
        'pages',
        'pages_out_components',
        'pages_templates',
        'default_page',
        'empty_page_index',
        'page_not_found_page',
        'wrap_components',
        'output_components_logic',
        'common_output_components'
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
        
        $this->registerAutoload();                             
        
        $this->setUpExceptionHandling();                                     
        
        $this->loadAdditionalConfig(
                $this->mainLoadedConfig['framework_dir'],
                $this->mainLoadedConfig['config_type'],
                $this->mainLoadedConfig
                );
        
        // Check required fields
        $this->checkRequiredConfigFields(
                SF::$config->getAllFields(),
                $this->requiredSystemConfig
                );                    
        
        // Set up logger
        if (SF::$config->get('error_log_enabled') == true) {
            
            $this->setUpLogger();                        
            
        }                   
        
        $this->setIniValues();
        
        $frameworkDir = SF::$config->get('framework_dir');
        
        // Load libs
        $this->loadLibs($frameworkDir);
        
        // Parse URL
        URL::processURL(
                SF::$config->get('is_site_multilang'), 
                SF::$config->get('default_language'),
                SF::$config->get('default_page'),
                SF::$config->get('ssl_port'),
                SF::$config->get('app_webroot_dir')
                );         

        $this->checkLanguage(URL::getCurrentLanguage());

        $this->genConfigValues();
        
        // Init template engine
        $this->tplEngine = $this->initTplEngine(SF::$config->get('app_dir'));
        
        $this->assignValsIntoTpl();
        
        $this->connectToDb();
        
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
     * 
     * @param $root Framework root dir
     */
    private function loadCoreClasses($root) {
        
        $directoryIterator = new \RecursiveDirectoryIterator(
                $root . $this->coreDirectory
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
     * @param $root Framework root dir
     * @param $configType Config type
     * @param $loadedConfig Config that is already loaded
     * @return array Loaded configuration
     */
    private function loadAdditionalConfig($root, $configType, $loadedConfig) {
        
        SF::$config = new Config('SF Global', $loadedConfig);            
        
        $configLoader = new ConfigLoader(
                $configType
                );
        
        $phpConfigLocation = null;
        $dbObj = null;
        
        if ($configType == ConfigLocations::PHP_FILE) {
            
            $phpConfigLocation = $root . 'config/';
                                    
        }                        
        
        $newConfigFields = $configLoader->loadConfiguration(
                $phpConfigLocation, 
                $loadedConfig,
                $dbObj
                );                
        
        SF::$config->addMultipleConfigValues($newConfigFields);                
    }
    
    /**
     * Sets up Exception handler API.
     */
    private function setUpExceptionHandling() {
        
        ExceptionHandler::setIsCli(CLIUtils::isCli());
        
        set_exception_handler(array("Core\Exception\ExceptionHandler", "handleException"));
        
    }
    
    /**
     * Sets up Logger.
     */
    private function setUpLogger() {
        
        Logger::setLogFile(SF::$config->get('log_file'));        
        
        try {
            
            Logger::setNewLine(SF::$config->get('new_line'));
            Logger::setTimestampFormat(SF::$config->get('log_time_format'));
            
        } catch (Exception $ex) {

            // nothing to do, logger will take its own timestamp format and / or new line.
            
        }                
        
    }       
    
    /**
     * Loads page.
     */
    private function loadPage() {                
        
        $tplDir = $this->tplEngine->getTemplateDir(0);
        
        $componentLoader = new SFComponentLoader(
                SF::$config->get('app_dir') . 'components/output/', 
                $tplDir . 'out_components/', 
                SF::$config->get('output_components'), 
                SF::$config->get('out_comp_ns'),
                SF::$config->get('logic_comp_ns'),
                $this->tplEngine,
                SF::$config->get('config_type'),
                URL::getCurrentLanguage(),
                SF::$config->get('wrap_components'),
                SF::$config->get('logic_components_dir'),
                SF::$config->get('output_components_logic'),
                $this->db,
                SF::$config->get('common_output_components'),
                SF::$config->get('current_page')
                );
        
        $pages = new Pages(
                SF::$config->get('pages'),
                SF::$config->get('pages_out_components'),
                SF::$config->get('pages_templates'),
                SF::$config->get('empty_page_index'),
                $this->tplEngine,
                $tplDir . 'pages/',
                $componentLoader
                );                     
        
        $pages->pageNotFoundPage = SF::$config->get('page_not_found_page');
        
        $currPage = URL::getCurrentPage();                
        
        /*$this->handlePageTranslations(
                $currPage, 
                SF::$config->get('pages'), 
                $pages->pageNotFoundPage);*/                
        
        $header = "";
        
        $content = $pages->getCurrentPageContent(
                $currPage, 
                SF::$config->get('output_components_url'),
                $header
                );                        
        
        $header = $this->genHeaderIndex() . $header;
        
        SF::$lang = new Language("SF Global");
        
        SF::$lang->loadLang(URL::getCurrentLanguage(), SF::$config->get('main_lang_dir'));
        
        $this->tplEngine->assign(
                'header',
                $header
                );      
        
        // assign to the main tpl
        $this->tplEngine->assign(
                'mainContent',
                $content
                );                
        
    }
    
    /**
     * Generates index page header.
     * 
     * @return string Index header
     */
    private function genHeaderIndex() {
        
        $indexCss = SF::$config->get('index_url') . 'css/index.css';
        $indexJs = SF::$config->get('index_url') . 'js/index.js';
        
        $headerIndex = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$indexCss}\">\n";
        $headerIndex .= "<script src=\"{$indexJs}\"></script>\n";
        
        return $headerIndex;
        
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
        
        $libsToLoad = SF::$config->get('sf_libs');
        
        foreach ($libsToLoad as $library) {
            
            require_once $documentRoot . 'lib/' . $library . '/incl_lib.php';
            
        }
        
    }
    
    /**
     * Inits template engine.
     * 
     * @param string $rootDir Root dir
     * @return Smarty Smarty object   
     */
    private function initTplEngine($rootDir) {
        
        $smarty = new \Smarty();

        $smarty->setTemplateDir($rootDir . 'templates');
        $smarty->setCompileDir($rootDir . 'templates_c');
        $smarty->setCacheDir($rootDir . 'cache');		
        
        return $smarty;
        
    }
    
    /**
     * Displays page content.
     * 
     * @param string $fetchedTpl Contect of a fetched tpl.
     */
    private function displayContent($fetchedTpl) {
        
        $this->tplEngine->display('index/index.tpl');
        
    }
    
    /**
     * Register autoload function.
     */
    private function registerAutoload() {
                        
        spl_autoload_register(array($this, 'loadClass'));
    
    }
    
    /**
     * Loads single class.
     * 
     * @param string $class $class name
     */
    private function loadClass($class) {
                                                                
        $classFile = 
                $this->mainLoadedConfig['framework_dir'] . 
                str_replace('\\', DIRECTORY_SEPARATOR, lcfirst($class)) . 
                '.class.php';

        if( is_file($classFile) && !class_exists($class) ) {
                    
            require $classFile;
                  
        }        
        
    }
    
    private function setIniValues()
    {
        $iniConfigFields = array(
            'display_errors',
            'error_reporting'
        );
        
        foreach ($iniConfigFields as $field) {
        
            ini_set($field, SF::$config->get($field));
            
        }   
        
    }
    
    /**
     * Generates configuration values.
     */
    private function genConfigValues() {         
        
        $mainUrl = URL::getMainUrlNoLang();
        
        SF::$config->set('main_url', URL::getMainUrl());
        SF::$config->set('main_url_no_lang', URL::getMainUrlNoLang());
        SF::$config->set('protocol', URL::getProtocol());
        SF::$config->set('current_page', URL::getCurrentPage());
        SF::$config->set('current_language', URL::getCurrentLanguage());
        SF::$config->set('url_parts', URL::getUrlParts());
        SF::$config->set('output_components_url', $mainUrl . SF::$config->get('output_components_url'));
        SF::$config->set('index_url', $mainUrl . SF::$config->get('index_url'));
        
    }
    
    /**
     * Assign values to the template.
     */
    private function assignValsIntoTpl() {
        
        $this->tplEngine->assign('configMain', SF::$config);        
        $this->tplEngine->assign('langMain', SF::$lang);                 
        
    }
    
    private function connectToDb() {
        
        $dbConfig = SF::$config->get('db_config');
        
        if (is_array($dbConfig) && !empty($dbConfig)) {
            
            $this->db = new DB($dbConfig);
            
        }        
        
    }

    private function checkLanguage($currLanguage) {

        if (!in_array($currLanguage, SF::$config->get('available_langs'))) {

            throw new \Exception("Language \"$currLanguage\" is not configured.");

        }

    }

    /**********************/
    
}
