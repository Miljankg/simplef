<?php

// Load main system configuration
require_once 'config/constants.php';
require_once 'config/system/config_main.php';
require_once 'core/SF.class.php';

// Create instance of the boot class
$sf = new \Core\SF($config);

// Start framework
$sf->execute();

