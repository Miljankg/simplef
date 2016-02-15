<?php

/* Output components */

$config['output_components'] = array(
    
                
    
            );

$config['output_components_logic'] = array(
    
);

/* Pages */
define("PAGE_404", "404");
define("PAGE_MAINTENANCE", "maintenance");

$config['pages'] =
        array(
            PAGE_404,
            PAGE_MAINTENANCE
        );
        
        
$config['pages_out_components'] = 
        array(
            PAGE_404 => array(),
            PAGE_MAINTENANCE => array()
        ); 

$config['pages_templates'] =
        array(
            
        );

/* Misc */
$config['default_page']         = ""; // Empty = root
$config['empty_page_index']     = "";
$config['page_not_found_page']  = PAGE_404;
$config['page_maintenance']     = PAGE_MAINTENANCE;
$config['wrap_components']      = true;
$config['common_output_components'] = array();