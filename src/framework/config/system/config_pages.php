<?php

/* Output components */

$config['output_components'] = array(
    
                
    
            );

$config['output_components_logic'] = array(
    
);

/* Pages */
define("PAGE_404", "404");


$config['pages'] =
        array(
            PAGE_404
        );
        
        
$config['pages_out_components'] = 
        array(
            PAGE_404 => array(
                
            )
        ); 

$config['pages_templates'] =
        array(
            
        );

/* Pages */
$config['default_page']         = ""; // Empty = root
$config['empty_page_index']     = "";
$config['page_not_found_page']  = PAGE_404;
$config['wrap_components']      = true;