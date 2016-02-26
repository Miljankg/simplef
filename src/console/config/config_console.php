<?php

$configConsole['action_mapping'] = array(
    'debug_mode' => array(
        'type' => 'Console\\Core\\Action\\BoolOperation',
        'config_index' => 'debug_mode',
        'allowed_values' => array()
    ),
    'error_log' => array(
        'type' => 'Console\\Core\\Action\\BoolOperation',
        'config_index' => 'error_log_enabled',
        'allowed_values' => array()
    ),
    'ajax_get_index' => array(
        'type' => 'Console\\Core\\Action\\StringOperation',
        'config_index' => 'ajax_get_index',
        'allowed_values' => array()
    ),
    'ajax_level' => array(
        'type' => 'Console\\Core\\Action\\StringOperation',
        'config_index' => 'ajax_level',
        'allowed_values' => array()
    ),
    'site_multilingual' => array(
        'type' => 'Console\\Core\\Action\\BoolOperation',
        'config_index' => 'is_site_multilang',
        'allowed_values' => array()
    ),
    'log_time_format' => array(
        'type' => 'Console\\Core\\Action\\StringOperation',
        'config_index' => 'log_time_format',
        'allowed_values' => array()
    ),
    'error_logging' => array(
        'type' => 'Console\\Core\\Action\\BoolOperation',
        'config_index' => 'error_log_enabled',
        'allowed_values' => array()
    ),
    'log_file_path' => array(
        'type' => 'Console\\Core\\Action\\StringOperation',
        'config_index' => 'log_file',
        'allowed_values' => array()
    ),
    'log_level' => array(
        'type' => 'Console\\Core\\Action\\StringOperation',
        'config_index' => 'log_level',
        'allowed_values' => array()
    ),
    'maintenance_mode' => array(
        'type' => 'Console\\Core\\Action\\BoolOperation',
        'config_index' => 'maintenance_mode',
        'allowed_values' => array()
    ),
    'ssl_port' => array(
        'type' => 'Console\\Core\\Action\\NumericOperation',
        'config_index' => 'ssl_port',
        'allowed_values' => array()
    ),
    'language' => array(
        'type' => 'Console\\Core\\Action\\LanguageOperation',
        'config_index' => '',
        'allowed_values' => array('add', 'remove', 'disable', 'enable', 'set_default')
    )
);

$configConsole['config_dir']    = 'framework/config/';
$configConsole['constant_file'] = $configConsole['config_dir'] . 'constants.php';