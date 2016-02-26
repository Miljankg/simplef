<?php

$config['use_authentication']            = true;
$config['roles']                         = array (
  0 => 'logged_in',
);
$config['users']                         = array (
  'test_user' => 
  array (
    'role' => 'logged_in',
    'password' => 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',
  ),
);
$config['db_config']                     = array (
);
$config['output_components_url']         = 'app/templates/out_components/';
$config['index_url']                     = 'app/templates/index/';
$config['pages_url']                     = 'app/templates/pages/';
$config['main_lang_dir']                 = 'C:\\xampp\\htdocs\\simplef\\src\\app\\lang/';
$config['components_dir']                = 'C:\\xampp\\htdocs\\simplef\\src\\app\\components/';
$config['output_components_dir']         = 'C:\\xampp\\htdocs\\simplef\\src\\app\\components/output/';
$config['logic_components_dir']          = 'C:\\xampp\\htdocs\\simplef\\src\\app\\components/logic/';
$config['config_dir']                    = 'C:\\xampp\\htdocs\\simplef\\src\\framework\\config/';
$config['ssl_port']                      = 443;
$config['out_comp_ns']                   = 'Components\\Output\\';
$config['logic_comp_ns']                 = 'Components\\Logic\\';
$config['is_cli']                        = '';
$config['ajax_get_index']                = 'ajax';
$config['ajax_level']                    = '1';
$config['enable_session']                = true;
$config['maintenance_mode']              = false;
$config['is_site_multilang']             = true;
$config['default_language']              = 'en';
$config['available_langs']               = array (
  0 => 'en',
);
$config['disabled_langs']                = array (
);
$config['lang_dir']                      = 'C:\\xampp\\htdocs\\simplef\\src\\app\\lang\\';
$config['sf_libs']                       = array (
  0 => 'smarty',
);
$config['error_log_enabled']             = true;
$config['log_file']                      = 'C:\\xampp\\htdocs\\simplef\\src\\framework\\log\\sf.log';
$config['log_time_format']               = '[ Y-m-d H:i:s ]';
$config['new_line']                      = '
';
$config['logic_components']              = array (
  'auth' => 
  array (
  ),
);
$config['output_components']             = array (
  'test' => 
  array (
    'js' => true,
    'css' => true,
  ),
  'login' => 
  array (
    'js' => true,
    'css' => true,
  ),
);
$config['output_components_logic']       = array (
  'test' => 
  array (
  ),
  'login' => 
  array (
    0 => 'auth',
  ),
);
$config['pages']                         = array (
  404 => 
  array (
  ),
  'maintenance' => 
  array (
  ),
  'error_page' => 
  array (
  ),
  'login' => 
  array (
  ),
  'test_page' => 
  array (
    'js' => true,
    'css' => false,
  ),
  'test_page_second' => 
  array (
  ),
);
$config['pages_out_components']          = array (
  404 => 
  array (
  ),
  'maintenance' => 
  array (
  ),
  'error_page' => 
  array (
  ),
  'login' => 
  array (
    0 => 'login',
  ),
  'test_page' => 
  array (
    0 => 'test',
  ),
  'test_page_second' => 
  array (
  ),
);
$config['pages_templates']               = array (
);
$config['pages_access']                  = array (
  'test_page_second' => 
  array (
    0 => 'logged_in',
  ),
);
$config['default_page']                  = '';
$config['empty_page_index']              = 'test_page';
$config['page_not_found_page']           = '404';
$config['page_maintenance']              = 'maintenance';
$config['error_page_url']                = 'error_page';
$config['wrap_components']               = true;
$config['common_output_components']      = array (
  'test' => 
  array (
  ),
);
