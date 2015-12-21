<?php

// Load main system configuration
require_once 'config/constants.php';
require_once 'config/system/config_main.php';
require_once $config['core_classes_dir'] . '/Boot.class.php';

// Create instance of the boot class
$boot = new \Core\Boot($config);

// Start framework
$boot->startSimpleFramework();

