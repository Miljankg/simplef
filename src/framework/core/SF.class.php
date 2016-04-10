<?php

namespace Framework\Core;

require_once 'loaders\ClassLoader.class.php';

use Framework\Core\Database\DbFactory;
use Framework\Core\FrameworkClasses\Configuration\ConfigLoader;
use Framework\Core\FrameworkClasses\Configuration\Config;
use Framework\Core\FrameworkClasses\Configuration\ConfigLocations;
use Framework\Core\Exception\ExceptionHandler;
use Framework\Core\CLI\CLIUtils;
use Framework\Core\FrameworkClasses\Configuration\IConfig;
use Framework\Core\FrameworkClasses\Logging\Logger;
use Framework\Core\FrameworkClasses\Logging\ILogger;
use Framework\Core\FrameworkClasses\Routing\PageLoader;
use Framework\Core\FrameworkClasses\URLUtils\IUrl;
use Framework\Core\FrameworkClasses\URLUtils\Url;
use Framework\Core\FrameworkClasses\Components\SFComponentLoader;
use Framework\Core\Lang\Language;
use Framework\Core\Database\DB;
use Framework\Core\Loaders\ClassLoader;
use \Exception;
use Framework\Core\FrameworkClasses\Globals\Get;
use Framework\Core\FrameworkClasses\Globals\Post;
use Framework\Core\FrameworkClasses\Session\Session;


/**
 * Class which task is to boot complete Simple Framework.
 *
 * @author Miljan Pantic
 */
class SF implements ISF {

    //<editor-fold desc="Members">

    /** @var IDbFactory */
    private $dbFactory = null;

    /** @var IConfig */
    private $config = null;

    /** @var Language */
    private $lang = null;

    /** @var Language */
    private $langPages = null;

    /** @var ILogger */
    private $logger = null;

    /** @var IUrl */
    private $url = null;

    /** @var Get */
    private $get = null;

    /** @var Post */
    private $post = null;

    /** @var Session */
    private $session = null;

    /** @var ClassLoader */
    private $frameworkClassLoader = null;

    private $mainLoadedConfig;
    private $requiredMainConfig = array(
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
        'config_db',
        'important_classes'
    );

    private $requiredSystemConfig = array(
        'display_errors',
        'debug_mode',
        'error_reporting',
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
        'output_components_options',
        'common_output_components',
        'pages_url',
        'ajax_get_index',
        'pages_access',
        'use_authentication'
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
     * @return IDbFactory
     */
    public function DbFactory()
    {
        return $this->dbFactory;
    }

    /**
     * @return Session
     */
    public function Session()
    {
        return $this->session;
    }

    /**
     * @return Get
     */
    public function Get()
    {
        return $this->get;
    }

    /**
     * @return Post
     */
    public function Post()
    {
        return $this->post;
    }

    /**
     * @return Config
     */
    public function Config()
    {
       return $this->config;
    }

    /**
     * @return Language
     */
    public function Lang()
    {
        return $this->lang;
    }

    public function LangPages()
    {
        return $this->langPages;
    }

    /**
     * @return ILogger
     */
    public function Logger()
    {
       return $this->logger;
    }

    /**
     * @return IUrl
     */
    public function Url()
    {
        return $this->url;
    }
    
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

        $this->frameworkClassLoader = new ClassLoader($this->mainLoadedConfig['document_root']);

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
                $this->config->getAllFields(),
                $this->requiredSystemConfig
                );                    
        
        // Set up logger
        if ($this->config->get('error_log_enabled') == true)
            $this->setUpLogger();
        
        $this->setIniValues();

        $frameworkDir = $this->config->get('framework_dir');
        
        // Load libs
        $this->loadLibs($frameworkDir);
        
        // Parse URL
        $this->processUrl();

        $this->checkLanguage($this->url->getCurrentLanguage());

        $this->genConfigValues();
        
        // Init template engine
        $this->tplEngine = $this->initTplEngine($this->config->get('app_dir'));
        
        $this->assignValsIntoTpl();

        $this->connectToDb();

        $this->initGlobalArrays();

        $this->sessionAndAuth($this->config->get('pages_access'));
    }
    
    /**
     * Displays content.
     */
    private function display()
    {
        // Load page
        $this->loadPage();
        
        $this->displayContent();
    }

    /**
     * Initializes global arrays.
     */
    private function initGlobalArrays()
    {
        $this->get = new Get($_GET, false);
        $this->post = new Post($_POST, false);
    }

    private function sessionAndAuth(array $pagesAccessConfig)
    {
        $useAuth = $this->config->get('use_authentication');

        $session = new Session();

        $this->session = $session;

        if($session->sessionExists())
            $session->sessionUpdate();
        else
            $session->sessionCreate();

        $currentPage = $this->url->getCurrentPageName();
		
        $userRole = $session->getUserData('role');
		
		if ($currentPage == '')
			$currentPage = $this->config->get('empty_page_index');		
		
        if ($useAuth)
        {
            if (!isset($pagesAccessConfig[$currentPage]) ||
                empty($pagesAccessConfig[$currentPage]) ||
                in_array($userRole, $pagesAccessConfig[$currentPage])
            )
                return;
            else
            {
				$this->session->setUserData('PAGE_BEFORE_LOGIN', $this->url->getCurrentUrl());
				
                $mainUrl = $this->url->getMainUrl();

                $loginPage = $mainUrl . 'login&not_allowed=1';

                $this->url->redirect($loginPage);
            }
        }
    }

    /**
     * Loads class and checks if it implements passed interface.
     *
     * @param string $index Index of a class in a config array.
     * @param string $interface Interface which class should implement.
     * @throws Exception If class does not implement wanted interface.
     */
    private function loadClassCheckInterface($index, $interface)
    {
        $importantClasses = $this->mainLoadedConfig['important_classes'];
        $rootDir = $this->mainLoadedConfig['app_webroot_dir'];

        if (!isset($importantClasses[$index]))
            throw new Exception("There is no important class \"{$index}\"");

        $class = $importantClasses[$index];

        $interfaces = class_implements($class);

        $this->frameworkClassLoader->loadClass($class);

        if(empty($interfaces) || !in_array($interface, $interfaces))
            throw new Exception("Class \"{$class}\" does not implements \"{$interface}\".");
    }

    private function processUrl()
    {
        $this->loadClassCheckInterface('url', 'Framework\Core\FrameworkClasses\URLUtils\IUrl');

        $this->url = new Url(
            $this->config->get('is_site_multilang'),
            $this->config->get('default_language'),
            $this->config->get('default_page'),
            $this->config->get('ssl_port'),
            $this->config->get('app_webroot_dir')
        );
    }
    
    /**
     * Checks if all required config fields are loaded.
     * 
     * In case of exception, die is called.
     * 
     * @param array $arrayToCheck Config array to check
     * @param array $requiredFields Required fields
     */
    private function checkRequiredConfigFields(array $arrayToCheck, array $requiredFields) {
        
        foreach ($requiredFields as $reqField) {
            
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

        $this->loadClassCheckInterface('config', 'Framework\Core\FrameworkClasses\Configuration\IConfig');
        $this->config = new Config('SF Global', $loadedConfig);            
        
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

        $this->config->addMultipleConfigValues($newConfigFields);                
    }

    /**
     * Sets default exception handler for unhandled exceptions.
     */
    private function setUnhandledExceptionHandler()
    {
        set_exception_handler(array('Framework\Core\Exception\ExceptionHandler', 'handleException'));
        register_shutdown_function(array('Framework\Core\SF', 'shutdownFunction'));
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
            !$this->config->get('debug_mode'),
            $this->config->get("main_url") . $this->config->get('error_page_url'),
            $this->config->get('log_level'),
            $this->config->get('system_exception_type')
        );
        
    }
    
    /**
     * Sets up Logger.
     */
    private function setUpLogger()
    {
        $this->loadClassCheckInterface('logger', 'Framework\Core\FrameworkClasses\Logging\ILogger');

        $this->logger = new Logger(
            $this->config->get('log_file'),
            $this->config->get('new_line'),
            $this->config->get('log_time_format'),
            $this->config->get('debug_mode')
        );

        ExceptionHandler::setLogger($this->logger);
        
    }       
    
    /**
     * Loads page.
     */
    private function loadPage()
    {
        $this->lang = new Language("SF Global");
        $this->langPages = new Language("Pages lang");

        $currLang = $this->url->getCurrentLanguage();

        $this->lang->loadLang($this->config->get('main_lang_dir') . $currLang . "/$currLang.php");
        $this->langPages->loadLang($this->config->get('main_lang_dir') . $currLang . "/{$currLang}_pages.php");

        $currPageName = $this->url->getCurrentPageName();

        $actualPageName = $this->langPages->getIndex($currPageName);

        if ($actualPageName === null)
            $actualPageName = $currPageName;

        $tplDir = $this->tplEngine->getTemplateDir(0);

        $this->loadClassCheckInterface('component_loader', 'Framework\Core\FrameworkClasses\Components\IComponentLoader');
        $this->loadClassCheckInterface('page_loader', 'Framework\Core\FrameworkClasses\Routing\IPageLoader');

        $componentLoader = new SFComponentLoader(
                $this->config->get('app_dir') . 'components/output/', 
                $tplDir . 'out_components/', 
                $this->config->get('output_components'), 
                $this->config->get('out_comp_ns'),
                $this->config->get('logic_components'),
                $this->config->get('logic_comp_ns'),
                $this->tplEngine,
                $this->config->get('config_type'),
                $this->url->getCurrentLanguage(),
                $this->config->get('wrap_components'),
                $this->config->get('logic_components_dir'),
                $this->config->get('output_components_options'),
                $this->dbFactory,
                $this->config->get('common_output_components'),
                $this->config->get('current_page'),
                $this->logger,
                $this
                );

        $ajaxLevel = $this->getAjaxLevel();
        $loadSpecificComponent = false;

        if (!is_numeric($ajaxLevel))
            $loadSpecificComponent = $ajaxLevel;

        $pageLoader = new PageLoader(
                $this->config->get('pages'),
                $this->config->get('pages_out_components'),
                $this->config->get('pages_templates'),
                $this->config->get('empty_page_index'),
                $this->config->get('maintenance_mode'),
                $this->tplEngine,
                $tplDir . 'pages/',
                $componentLoader,
                $this->logger,
                $this->url,
                $this->config->get('output_components_url'),
                $this->config->get('pages_url'),
                $loadSpecificComponent
                );

        $pageLoader->pageNotFoundPage = $this->config->get('page_not_found_page');
        $pageLoader->pageMaintenance = $this->config->get('page_maintenance');
        
        $header = "";

        $content = $pageLoader->getCurrentPageContent(
                $actualPageName,
                $header
                );

        if ($loadSpecificComponent !== false || $ajaxLevel == 2)
        {
            $ajaxData = array(
                'content' => $content,
                'header' => $header
            );

            $this->outputJsonEncoded($ajaxData);
        }

        $header = $this->genHeaderIndex() . $header;
        
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

    private function getAjaxLevel()
    {
        $value = 0;

        $index = $this->config->get('ajax_get_index');

        if (isset($_GET[$index]))
            $value = $_GET[$index];

        return $value;
    }
    
    /**
     * Generates index page header.
     * 
     * @return string Index header
     */
    private function genHeaderIndex() {
        
        $indexCss = $this->config->get('index_url') . 'css/index.css';
        $indexJs = $this->config->get('index_url') . 'js/index.js';

        $headerIndex = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$indexCss}\">\n";
        $headerIndex .= "<script src=\"{$indexJs}\"></script>\n";
        
        return $headerIndex;
        
    }

    /**
     * Loads SF libraries.
     * 
     * @param string $documentRoot
     */
    private function loadLibs($documentRoot)
    {
        $libsToLoad = $this->config->get('sf_libs');
        
        foreach ($libsToLoad as $library) {

            $path = $documentRoot . 'lib/' . $library . '/incl_lib.php';

            $this->logger->logDebug("Loading lib \"{$library}\", from: \"{$path}\"");

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

        $indexTpl = 'index/index.tpl';

        $ajaxLevel = $this->getAjaxLevel();

        if ($ajaxLevel == 1)
        {
            $this->outputJsonEncoded(array('content' => $this->tplEngine->fetch($indexTpl)));
        }

        $this->tplEngine->display($indexTpl);
    }

    /**
     * Outputs json decoded data.
     *
     * @param array $dataToOutput
     */
    private function outputJsonEncoded(array $dataToOutput)
    {
        echo json_encode($dataToOutput);
        exit;
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
    private function loadClass($oopElement)
    {
        $this->frameworkClassLoader->loadClass($oopElement);
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

        $debugMode = $this->config->get("debug_mode");

        foreach ($iniConfigFields as $field)
        {
            $value = $this->config->get($field);

            $this->logger->logDebug("Setting INI field \"{$field}\" to value: {$value}");

            if (!$debugMode && array_key_exists($field, $debugModeDependentFields))
                $value = $debugModeDependentFields[$field];

            ini_set($field, $value);
        }
    }
    
    /**
     * Generates configuration values.
     */
    private function genConfigValues() {         
        
        $mainUrl = $this->url->getMainUrlNoLang();
        
        $this->config->set('main_url', $this->url->getMainUrl());
        $this->config->set('main_url_no_lang', $this->url->getMainUrlNoLang());
        $this->config->set('protocol', $this->url->getProtocol());
        $this->config->set('current_page', $this->url->getCurrentPageName());
        $this->config->set('current_language', $this->url->getCurrentLanguage());
        $this->config->set('url_parts', $this->url->getUrlParts());
        $this->config->set('output_components_url', $mainUrl . $this->config->get('output_components_url'));
        $this->config->set('index_url', $mainUrl . $this->config->get('index_url'));
        $this->config->set('pages_url', $mainUrl . $this->config->get('pages_url'));
        
    }
    
    /**
     * Assign values to the template.
     */
    private function assignValsIntoTpl() {
        
        $this->tplEngine->assign('configMain', $this->config);        
        $this->tplEngine->assign('langMain', $this->lang);                 
        
    }

    /**
     * Connect to configured DBs.
     */
    private function connectToDb()
    {
        $dbConfig = $this->config->get('db_config');

        $this->loadClassCheckInterface('db_factory', 'Framework\Core\Database\IDbFactory');

        if (is_array($dbConfig) && !empty($dbConfig))
            $this->dbFactory = new DbFactory($dbConfig);
    }

    private function checkLanguage($currLanguage) {

        if (!in_array($currLanguage, $this->config->get('available_langs'))) {

            throw new \Exception("Language \"$currLanguage\" is not configured.");

        }

        if (in_array($currLanguage, $this->config->get('disabled_langs'))) {

            throw new \Exception("Language \"$currLanguage\" is disabled.");

        }

    }

    //</editor-fold>
}
