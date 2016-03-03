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
    ),
    'logic_component' => array(
        'type' => 'Console\\Core\\Action\\LogicComponentOperation',
        'config_index' => '',
        'allowed_values' => array('add', 'remove', 'add_dependency', 'remove_dependency')
    ),
    'output_component' => array(
        'type' => 'Console\\Core\\Action\\OutputComponentOperation',
        'config_index' => '',
        'allowed_values' => array('add', 'remove', 'add_dependency', 'remove_dependency', 'enable_js', 'disable_js', 'enable_css', 'disable_css')
    ),
    'page' => array(
        'type' => 'Console\\Core\\Action\\PageOperation',
        'config_index' => '',
        'allowed_values' => array('add', 'remove', 'add_dependency', 'remove_dependency')
    )
);

$configConsole['config_dir']    = 'framework/config/';
$configConsole['constant_file'] = $configConsole['config_dir'] . 'constants.php';

$configConsole['logic_component_config_index']              = 'logic_components';
$configConsole['logic_component_type']                      = 'logic';
$configConsole['logic_component_constant_prefix']           = 'LC_';
$configConsole['logic_component_directory_config_index']    = 'logic_components_dir';
$configConsole['logic_dependency_config_index']             = 'logic_components';

$configConsole['output_component_config_index']             = 'output_components';
$configConsole['output_component_type']                     = 'output';
$configConsole['output_component_constant_prefix']          = 'OC_';
$configConsole['output_component_directory_config_index']   = 'output_components_dir';
$configConsole['output_component_template_dir_config_index']= 'output_components_templates_dir';
$configConsole['output_component_options_config_index']     = 'output_components_options';
$configConsole['output_dependency_config_index']            = 'logic_components';
