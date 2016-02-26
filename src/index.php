<?php

$config = array();

// Load main system configuration
require_once 'framework/config/constants.php';
require_once 'framework/config/config_main.php';
require_once 'framework/core/ISF.interface.php';
require_once 'framework/core/SF.class.php';


// Create instance of the boot class

/** @var Framework\Core\ISF */
$sf = new Framework\Core\SF($config);

// Start framework
$sf->execute();

