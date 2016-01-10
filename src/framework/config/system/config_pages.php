<?php

/* Output components */
define("COMP1", 'out_comp1');
define("COMP2", 'out_comp2');

define("LOGIC_COMP_1", 'lc1');

$config['output_components'] = array(
    
                COMP1 => array(
                    'template'  => true,
                    'js'        => true,
                    'css'       => true
                ),
                COMP2 => array()
    
            )
        ;

$config['output_components_logic'] = array(
    COMP1 => array(
        LOGIC_COMP_1
    )
);

/* Pages */
define("PAGE_LANG_TEST", "lang_test");
define("PAGE_TEST", "test");
define("PAGE_404", "404");


$config['pages'] =
        array(
            PAGE_TEST,
            PAGE_LANG_TEST,
            PAGE_404
        )
    ;
        
        
$config['pages_out_components'] = 
        array(
            PAGE_TEST => array(
                
                ),
            PAGE_LANG_TEST => array(
                COMP1,
                COMP2
            ),
            PAGE_404 => array(
                
            )
        )
    ; 

$config['pages_templates'] =
        array(
            
        )
    ;

/* Pages */
$config['default_page']         = ""; // Empty = root
$config['empty_page_index']     = PAGE_LANG_TEST;
$config['page_not_found_page']  = PAGE_404;
$config['wrap_components']      = true;