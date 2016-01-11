<?php

$config['display_errors']       = "on"; // Intended for ini_set
$config['error_reporting']      = E_ALL; // Intended for ini_set
$config['debug_level']          = 1; // Framework debug level

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

/* dirs */
$config['document_root']        = $_SERVER['DOCUMENT_ROOT'] . $config['ds'] . $config['app_webroot_dir'] . $config['ds'];
$config['framework_dir']        = $config['document_root'] . 'framework' . $config['ds'];
$config['app_dir']              = $config['document_root'] . 'app' . $config['ds'];

$config['main_url']             = ""; // Generated during framework initialization
$config['protocol']             = ""; // Generated during framework initialization
$config['main_url_no_lang']     = ""; // Generated during framework initialization
$config['current_page']         = ""; // Generated during framework initialization
$config['current_language']     = ""; // Generated during framework initialization
$config['url_parts']            = ""; // Generated during framework initialization

/* Config */
$config['config_type']          = "php_file"; // php_file or db
$config['config_db']            = array(      // In case when framework config is stored in the DB, this fields will be used
    'host' => '',
    'user' => '',
    'pass' => '',
    'type' => 'mysql' // currently only mysql
);
