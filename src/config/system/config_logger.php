<?php

use Core\Configuration as Conf;

Conf\Config::add('error_log_enabled', true);

Conf\Config::add('log_file', Conf\Config::get('document_root') . "log/sf.log");
Conf\Config::add('log_time_format', '[ Y-m-d H:i:s ]'); 
Conf\Config::add('new_line', PHP_EOL);
