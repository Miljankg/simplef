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
    'wrap_components' => array(
        'type' => 'Console\\Core\\Action\\BoolOperation',
        'config_index' => 'wrap_components',
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
    'use_authentication' => array(
        'type' => 'Console\\Core\\Action\\BoolOperation',
        'config_index' => 'use_authentication',
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
        'allowed_values' => array('add', 'remove', 'add_dependency', 'remove_dependency', 'enable_js', 'disable_js', 'enable_css', 'disable_css', 'add_to_common', 'remove_from_common')
    ),
    'page' => array(
        'type' => 'Console\\Core\\Action\\PageOperation',
        'config_index' => '',
        'allowed_values' => array('add', 'remove', 'add_dependency', 'remove_dependency', 'add_role', 'remove_role', 'set_template', 'set_default_page', 'set_empty_page_index', 'set_page_not_found_page', 'set_page_maintenance', 'set_error_page_url')
    ),
    'roles' => array(
        'type' => 'Console\\Core\\Action\\RolesOperation',
        'config_index' => '',
        'allowed_values' => array('add', 'remove')
    ),
    'users' => array(
        'type' => 'Console\\Core\\Action\\UsersOperation',
        'config_index' => '',
        'allowed_values' => array('add' => array('username', 'role', 'password'), 'remove' => array('username'), 'change_password' => array('username', 'password'), 'change_role' => array('username', 'role'))
    ),
    'db_connection' => array(
        'type' => 'Console\\Core\\Action\\DbConnectionOperation',
        'config_index' => '',
        'allowed_values' => array('add', 'remove', 'update')
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

$configConsole['db_fields']                                 = array('db_type', 'db_user', 'db_pass', 'db_host', 'db_name');
