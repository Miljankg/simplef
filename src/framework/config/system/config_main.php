<?php

//<editor-fold desc="php.ini file values">

$config['display_errors']       = "on";
$config['error_reporting']      = E_ALL;

//</editor-fold>

//<editor-fold desc="Debugging">

define("LOG_LEVEL_ALL", "ALL");
define("LOG_LEVEL_SYSTEM_ONLY", "SYSTEM_ONLY");

$config['log_level']            = LOG_LEVEL_ALL; // Logging level [ ALL | SYSTEM_ONLY | ExceptionType1,ExceptionType2,ExceptionType3... ]
$config['debug_mode']           = true; // Framework debug level [ true | false ]
$config['system_exception_type']= "SfException";

//</editor-fold>

//<editor-fold desc="Maintenance">

$config['maintenance_mode']     = true;

//</editor-fold>

//<editor-fold desc="Paths">

$config['ds']                   = DIRECTORY_SEPARATOR;

$config['app_webroot_dir'] = str_replace(
        str_replace(
                '/', 
                $config['ds'], 
                $_SERVER['DOCUMENT_ROOT']
                ), 
        '', 
        dirname(dirname(dirname(__DIR__)))
        ); // Application location within the server webroot

$config['app_webroot_dir'] = ltrim($config['app_webroot_dir'], $config['ds']);

$config['document_root']        = $_SERVER['DOCUMENT_ROOT'] . $config['ds'] . $config['app_webroot_dir'] . $config['ds'];
$config['framework_dir']        = $config['document_root'] . 'framework' . $config['ds'];
$config['app_dir']              = $config['document_root'] . 'app' . $config['ds'];

//</editor-fold>

//<editor-fold desc="Fields generated during framework initalization">

$config['main_url']             = "";
$config['protocol']             = "";
$config['main_url_no_lang']     = "";
$config['current_page']         = "";
$config['current_language']     = "";
$config['url_parts']            = "";

//</editor-fold>

//<editor-fold desc="Config loading related settings">

$config['config_type']          = "php_file"; // php_file or db
$config['config_db']            = array(      // In case when framework config is stored in the DB, this fields will be used
    'host' => '',
    'user' => '',
    'pass' => '',
    'type' => 'mysql' // currently only mysql
);

//</editor-fold>
