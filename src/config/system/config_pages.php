<?php

use Core\Configuration as Conf;

/* Output components */
define("COMP1", 'out_comp1');
define("COMP2", 'out_comp2');

Conf\Config::add('output_components', array(
    
                COMP1,
                COMP2
    
            )
        );

/* Pages */
define("PAGE_LANG_TEST", "lang_test");
define("PAGE_TEST", "test");
define("PAGE_404", "404");


Conf\Config::add('pages',
        array(
            PAGE_TEST,
            PAGE_LANG_TEST,
            PAGE_404
        )
    );
        
        
Conf\Config::add('pages_out_components', 
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
    ); 

Conf\Config::add('pages_templates',
        array(
            
        )
    );

/* Pages */
Conf\Config::add('default_page', ""); // Empty = root
Conf\Config::add('empty_page_index', "lang_test");
Conf\Config::add('page_not_found_page', PAGE_404);