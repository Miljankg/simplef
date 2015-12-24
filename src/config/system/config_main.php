<?php

$config['display_errors']       = "on"; // Intended for ini_set
$config['error_reporting']      = "E_ALL"; // Intended for ini_set
$config['debug_level']          = 1; // Framework debug level

$config['app_webroot_location'] = str_replace(str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']), '', dirname(dirname(__DIR__))); // Application location within the server webroot

/* dirs */
$config['document_root']        = $_SERVER['DOCUMENT_ROOT'] . $config['app_webroot_location'] . '/';

/* https */
$config['ssl_port']             = "443";

/* Config */
$config['config_type']          = "php_file"; // php_file or db
$config['config_db']            = array(      // In case when framework config is stored in the DB, this fields will be used
    'host' => '',
    'user' => '',
    'pass' => '',
    'type' => 'mysql' // currently only mysql
);
