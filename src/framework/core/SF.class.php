<?php

namespace Core;

use Core\Configuration\ConfigLoader;
use Core\Configuration\Config;
use Core\Configuration\ConfigLocations;
use Core\Exception\ExceptionHandler;
use Core\CLI\CLIUtils;
use Core\Logging\Logger;
use Core\Logging\ILogger;
use Core\URLUtils\URL;
use Core\Routing\Pages;
use Core\Components\SFComponentLoader;
use Core\Lang\Language;
use Core\Database\DB;
use \Exception;


/**
 * Class which task is to boot complete Simple Framework.
 *
 * @author Miljan Pantic
 */
class SF implements ISF {

    //<editor-fold desc="Members">

    private $db = null;

    /** @var Config */
    public static $config = null;

    /** @var Language */
    public static $lang = null;

    /** @var ILogger */
    public static $logger = null;

    private $mainLoadedConfig;
    private $requiredMainConfig = array(
        'display_errors',
        'error_reporting',
        'debug_mode',
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
        'common_output_components',
        'pages_url'
    );

    /** @noinspection PhpUndefinedClassInspection */

    /** @var \Smarty */
    private $tplEngine = null;

    //</editor-fold>

    //<editor-fold desc="Constructors">

    public function __construct(array $mainLoadedConfig) {
        
        $this->mainLoadedConfig = $mainLoadedConfig;

    }

    //</editor-fold>

    //<editor-fold desc="Interface functions">
    
    /**
     * Executes SF.
     */
    public function execute() {

        $this->bootUp();

        $this->display();

    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">
    
    /**
     * Boots up SF.
     */
    private function bootUp() {

        $this->registerAutoload();

        $this->setUnhandledExceptionHandler();

        // Check required fields
        $this->checkRequiredConfigFields(
                $this->mainLoadedConfig,
                $this->requiredMainConfig
                );
        
        $this->loadAdditionalConfig(
                $this->mainLoadedConfig['framework_dir'],
                $this->mainLoadedConfig['config_type'],
                $this->mainLoadedConfig
                );

        $this->setExceptionHandlingParams();

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
        $this->loadPage();
        
        $this->displayContent();
        
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
     * @param string $root Framework root dir
     * @param string $configType Config type
     * @param array $loadedConfig Config that is already loaded
     * @return array Loaded configuration
     */
    private function loadAdditionalConfig($root, $configType, array $loadedConfig) {
        
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
     * Sets default exception handler for unhandled exceptions.
     */
    private function setUnhandledExceptionHandler()
    {
        set_exception_handler(array('Core\Exception\ExceptionHandler', 'handleException'));
        register_shutdown_function(array('Core\SF', 'shutdownFunction'));
    }

    public static function shutdownFunction()
    {
        $last_error = error_get_last();

        if ($last_error === null)
            return;

        $exception = new \ErrorException(
            "FATAL_ERROR [ " . $last_error['message'] . " ] ",
            0,
            1,
            $last_error['file'],
            $last_error['line'],
            null
        );

        ExceptionHandler::handleException($exception);
    }

    /**
     * Sets up Exception handler API.
     */
    private function setExceptionHandlingParams() {
        
        ExceptionHandler::setParams(
            CLIUtils::isCli(),
            !SF::$config->get('debug_mode'),
            SF::$config->get("main_url") . SF::$config->get('error_page_url'),
            SF::$config->get('log_level'),
            SF::$config->get('system_exception_type')
        );
        
    }
    
    /**
     * Sets up Logger.
     */
    private function setUpLogger() {

        Logger::setInstance(
            SF::$config->get('log_file'),
            SF::$config->get('new_line'),
            SF::$config->get('log_time_format'),
            SF::$config->get('debug_mode')
        );

        SF::$logger = Logger::getInstance();

        ExceptionHandler::setLogger(Logger::getInstance());
        
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
                SF::$config->get('current_page'),
                SF::$logger
                );

        $pages = new Pages(
                SF::$config->get('pages'),
                SF::$config->get('pages_out_components'),
                SF::$config->get('pages_templates'),
                SF::$config->get('empty_page_index'),
                SF::$config->get('maintenance_mode'),
                $this->tplEngine,
                $tplDir . 'pages/',
                $componentLoader,
                SF::$logger
                );                     
        
        $pages->pageNotFoundPage = SF::$config->get('page_not_found_page');
        $pages->pageMaintenance = SF::$config->get('page_maintenance');
        
        $currPage = URL::getCurrentPage();                
        
        /*$this->handlePageTranslations(
                $currPage, 
                SF::$config->get('pages'), 
                $pages->pageNotFoundPage);*/                
        
        $header = "";

        $content = $pages->getCurrentPageContent(
                $currPage, 
                SF::$config->get('output_components_url'),
                SF::$config->get('pages_url'),
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
    /*private function handlePageTranslations(&$currPage, array $pages, $page404) {
        
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
            
            if ($currPage == URL::getCurrentPage()) {
                
                try {
                    
                    Lang\Language::get("page_" . $currPage); 
                    $currPage = $page404;
                    
                } catch (\Exception $ex) {
                    
                    // move on
                    
                }                                
                
            }
        
        }           
    }*/

    /**
     * Loads SF libraries.
     * 
     * @param string $documentRoot
     */
    private function loadLibs($documentRoot)
    {
        $libsToLoad = SF::$config->get('sf_libs');
        
        foreach ($libsToLoad as $library) {

            $path = $documentRoot . 'lib/' . $library . '/incl_lib.php';

            SF::$logger->logDebug("Loading lib \"{$library}\", from: \"{$path}\"");

            /** @noinspection PhpIncludeInspection */
            require_once $path;
        }
        
    }

    /** @noinspection PhpUndefinedClassInspection */

    /**
     * Inits template engine.
     * 
     * @param string $rootDir Root dir
     * @return \Smarty Smarty object
     */
    private function initTplEngine($rootDir) {

        /** @noinspection PhpUndefinedClassInspection */
        $smartyObj = new \Smarty();

        $smartyObj->setTemplateDir($rootDir . 'templates');
        $smartyObj->setCompileDir($rootDir . 'templates_c');
        $smartyObj->setCacheDir($rootDir . 'cache');
        
        return $smartyObj;
        
    }
    
    /**
     * Displays page content.
     */
    private function displayContent() {
        
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
     * @param string $oopElement Name of the class or a interface to load
     */
    private function loadClass($oopElement) {

        $tmp = explode("\\", $oopElement);

        $oopElementName = end($tmp);

        $suffix = ".class";

        if(strpos($oopElementName, 'I') === 0 && ctype_upper(substr($oopElementName, 0, 2)))
        {
            $suffix = ".interface";
        }

        $suffix .= ".php";

        $file =
                $this->mainLoadedConfig['framework_dir'] . 
                str_replace('\\', DIRECTORY_SEPARATOR, lcfirst($oopElement)) .
                $suffix;

        if( is_file($file) && !class_exists($oopElement) ) {

            /** @noinspection PhpIncludeInspection */
            require $file;
                  
        }        
        
    }

    /**
     * Sets PHP.INI values from config.
     */
    private function setIniValues()
    {
        $displayErrorField = 'display_errors';
        $errorReportingField = 'error_reporting';

        $iniConfigFields = array(
            $displayErrorField,
            $errorReportingField
        );

        $debugModeDependentFields = array(
            $displayErrorField => "off",
            $errorReportingField => 0
        );

        $debugMode = SF::$config->get("debug_mode");

        foreach ($iniConfigFields as $field)
        {
            $value = SF::$config->get($field);

            SF::$logger->logDebug("Setting INI field \"{$field}\" to value: {$value}");

            if (!$debugMode && array_key_exists($field, $debugModeDependentFields))
                $value = $debugModeDependentFields[$field];

            ini_set($field, $value);
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
        SF::$config->set('pages_url', $mainUrl . SF::$config->get('pages_url'));
        
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

    //</editor-fold>

}
