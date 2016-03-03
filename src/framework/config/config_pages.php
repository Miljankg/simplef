<?php

$config['logic_components']              = array (
  'auth' => 
  array (
    0 => 'test_logic',
  ),
  'test_auth' => 
  array (
  ),
);
$config['output_components']             = array (
  'test' => 
  array (
  ),
  'login' => 
  array (
    0 => 'auth',
  ),
  'test_out' => 
  array (
  ),
  'test_out_sec' => 
  array (
    0 => 'auth',
  ),
);
$config['output_components_options']     = array (
  'test_out' => 
  array (
    'css' => true,
    'js' => true,
  ),
  'test_out_sec' => 
  array (
    'css' => false,
    'js' => true,
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
