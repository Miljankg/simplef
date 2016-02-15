<?php

$config = array();

// Load main system configuration
require_once 'framework/config/constants.php';
require_once 'framework/config/system/config_main.php';
require_once 'framework/core/ISF.interface.php';
require_once 'framework/core/SF.class.php';


// Create instance of the boot class

/** @var \Core\ISF */
$sf = new \Core\SF($config);

// Start framework
$sf->execute();

