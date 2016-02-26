<?php

$configConsole = array();

require_once 'console\\config\\config_console.php';

$rootDir = '';

$separator = '==================================================';
$nl = PHP_EOL;
$dnl = $nl . $nl;
$configDir = $configConsole['config_dir'];
$config = array();


require_once $configDir . 'constants.php';
require_once $configDir . 'config_main.php';

$frameworkDir = $config['framework_dir'];

require_once 'framework\core\Loaders\ClassLoader.class.php';
require_once 'console\Core\Parameters\ScriptParams.class.php';
require_once $frameworkDir . 'core\FrameworkClasses\Configuration\ConfigLocations.class.php';
require_once $frameworkDir . 'core\FrameworkClasses\Configuration\ConfigLoader.class.php';

$classLoader = new Framework\Core\Loaders\ClassLoader($rootDir);

spl_autoload_register(array($classLoader, 'loadClass'));

echo $nl . $separator . $dnl;

try
{
    /** @var \Framework\Core\FrameworkClasses\Configuration\ConfigLoader */
    $configLoader = null;
    $unparsedConfig = array();
    $configFileMapping = array();

    $configLoader = new \Framework\Core\FrameworkClasses\Configuration\ConfigLoader(\Framework\Core\FrameworkClasses\Configuration\ConfigLocations::PHP_FILE);
    $newConfiguration = $configLoader->loadConfiguration($frameworkDir . 'config/', $config, null, $unparsedConfig, $configFileMapping);

    $config = new Console\Core\Config\ConfigFromFile($configDir, $configConsole['constant_file'], $newConfiguration, $unparsedConfig, $configFileMapping);

    $sfAssist = new \Console\Core\SfAssist($argv, $config, $configConsole);

    $sfAssist->execute($configConsole['action_mapping']);

}
catch (Exception $ex)
{
    echo '### ERROR ###  ' . PHP_EOL . PHP_EOL . $ex->getMessage() . PHP_EOL . PHP_EOL . '#############';
}

echo $dnl . $separator . $nl;