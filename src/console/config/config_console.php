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
        'allowed_values' => array('add' => array('language-mark'), 'remove' => array('language-mark'),
                                'disable' => array('language-mark'), 'enable' => array('language-mark'),
                                'set_default' => array('language-mark'))
    ),
    'logic_component' => array(
        'type' => 'Console\\Core\\Action\\LogicComponentOperation',
        'config_index' => '',
        'allowed_values' => array('add' => array('component-name', 'component-dependencies'), 'remove' => array('component-name'),
                                'add_dependency' => array('component-name', 'logic-component'), 'remove_dependency' => array('component-name', 'logic-component'),
                                'export' => array('component-name', 'path'), 'import' => array('path'),
                                'add_sql_file' => array('component-name', 'file-name'), 'remove_sql_file' => array('component-name', 'file-name'), 'merge' => array('path'))
    ),
    'output_component' => array(
        'type' => 'Console\\Core\\Action\\OutputComponentOperation',
        'config_index' => '',
        'allowed_values' => array('add' => array('component-name'), 'remove' => array('component-name'),
                                'add_dependency' => array('component-name', 'logic-component'),
                                'remove_dependency' => array('component-name', 'logic-component'),
                                'enable_js' => array('component-name'), 'disable_js' => array('component-name'),
                                'enable_css' => array('component-name'), 'disable_css' => array('component-name'),
                                'add_to_common' => array('component-name'), 'remove_from_common' => array('component-name'),
                                'export' => array('component-name', 'path'), 'import' => array('path'))
    ),
    'page' => array(
        'type' => 'Console\\Core\\Action\\PageOperation',
        'config_index' => '',
        'allowed_values' => array('add' => array('page-name', 'page-dependencies'), 'remove' => array('page-name'),
                                'add_dependency' => array('page-name', 'output-component'), 'remove_dependency'  => array('page-name', 'output-component'),
                                'add_role' => array('page-name', 'role'), 'remove_role' => array('page-name', 'role'),
                                'set_template' => array('template-name'), 'set_default_page' => array('page-name'),
                                'set_empty_page_index' => array('page-name'), 'set_page_not_found_page' => array('page-name'),
                                'set_page_maintenance' => array('page-name'), 'set_error_page_url' => array('page-name'))
    ),
    'roles' => array(
        'type' => 'Console\\Core\\Action\\RolesOperation',
        'config_index' => '',
        'allowed_values' => array('add' => array('role-name'), 'remove' => array('role-name'))
    ),
    'users' => array(
        'type' => 'Console\\Core\\Action\\UsersOperation',
        'config_index' => '',
        'allowed_values' => array('add' => array('username', 'role', 'password'), 'remove' => array('username'), 'change_password' => array('username', 'password'), 'change_role' => array('username', 'role'))
    ),
    'db_connection' => array(
        'type' => 'Console\\Core\\Action\\DbConnectionOperation',
        'config_index' => '',
        'allowed_values' => array('add' => array('connection-name'), 'remove' => array('connection-name'), 'update' => array('connection-name', 'field-to-update', 'field-value'))
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
