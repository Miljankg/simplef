<?php

//<editor-fold desc="Paths">

$config['ds']                   = DIRECTORY_SEPARATOR;

$config['app_webroot_dir'] = str_replace(
        str_replace(
                '/', 
                $config['ds'], 
                $_SERVER['DOCUMENT_ROOT']
                ), 
        '', 
        dirname(dirname(__DIR__))
        ); // Application location within the server webroot

$config['app_webroot_dir']      = (php_sapi_name() == 'cli') ? $config['app_webroot_dir'] : ltrim($config['app_webroot_dir'], $config['ds']);

$docRootTmp = (empty($_SERVER['DOCUMENT_ROOT'])) ? "" : $_SERVER['DOCUMENT_ROOT'] . $config['ds'];

$config['document_root']        = $docRootTmp . $config['app_webroot_dir'] . $config['ds'];
$config['framework_dir']        = $config['document_root'] . 'framework' . $config['ds'];
$config['app_dir']              = $config['document_root'] . 'app' . $config['ds'];

//</editor-fold>

//<editor-fold desc="Fields generated during framework initalization">

$config['main_url']             = "";
$config['protocol']             = "";
$config['main_url_no_lang']     = "";
$config['current_page']         = "";
$config['current_language']     = "";
$config['url_parts']            = "";

//</editor-fold>

//<editor-fold desc="Config loading related settings">

$config['config_type']          = "php_file"; // php_file or db
$config['config_db']            = array(      // In case when framework config is stored in the DB, this fields will be used
    'host' => '',
    'user' => '',
    'pass' => '',
    'type' => 'mysql' // currently only mysql
);

//</editor-fold>

//<editor-fold desc="Class loading">

$config['important_classes'] = array(
    'url' => 'Framework\Core\FrameworkClasses\URLUtils\Url',
    'component_loader' => 'Framework\Core\FrameworkClasses\Components\SFComponentLoader',
    'logger' => 'Framework\Core\FrameworkClasses\Logging\Logger',
    'page_loader' => 'Framework\Core\FrameworkClasses\Routing\PageLoader',
    'config' => 'Framework\Core\FrameworkClasses\Configuration\Config',
    'db_factory' => 'Framework\Core\Database\DbFactory'
);

//</editor-fold>