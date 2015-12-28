<?php

use Core\Configuration as Conf;

/* Pages */
Conf\Config::add('pages', 
        array(
            'test' => array(
                'some_component'
                ),
            'lang_test' => array(
                'mika',
                'djoka'
            ),
            '404' => array(
                
            )
        )
    ); 

/* Pages */
Conf\Config::add('default_page', ""); // Empty = root
Conf\Config::add('empty_page_index', "lang_test");