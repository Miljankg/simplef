<?php

//<editor-fold desc="Output components">

$config['output_components']        = array(
    
                
    
            );

$config['output_components_logic']  = array(
    
);

//</editor-fold>

//<editor-fold desc="Pages">

define("PAGE_404", "404");
define("PAGE_MAINTENANCE", "maintenance");
define("PAGE_ERROR", "error_page");

$config['pages']                    = array(
            PAGE_404,
            PAGE_MAINTENANCE,
            PAGE_ERROR
        );
        
        
$config['pages_out_components']     = array(
            PAGE_404 => array(),
            PAGE_MAINTENANCE => array(),
            PAGE_ERROR
        ); 

$config['pages_templates']          = array(
            
        );

//</editor-fold>

//<editor-fold desc="Additional routing configuration">

$config['default_page']             = ""; // Empty = root
$config['empty_page_index']         = "";
$config['page_not_found_page']      = PAGE_404;
$config['page_maintenance']         = PAGE_MAINTENANCE;
$config['error_page_url']           = $config['main_url'] . PAGE_ERROR;
$config['wrap_components']          = true;
$config['common_output_components'] = array();

//</editor-fold>