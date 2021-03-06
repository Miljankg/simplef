<?php

$configConsole = array();

require_once __DIR__ . '/console/config/config_console.php';

$rootDir = '';

$separatorStart = '==================== ACTION STARTED ====================';
$separatorEnd   = '===================== ACTION ENDED =====================';
$nl = PHP_EOL;
$dnl = $nl . $nl;
$configDir = $configConsole['config_dir'];
$config = array();


require_once $configDir . 'constants.php';
require_once $configDir . 'config_main.php';

$frameworkDir = $config['framework_dir'];

require_once __DIR__ . '/framework/core/Loaders/ClassLoader.class.php';

$classLoader = new Framework\Core\Loaders\ClassLoader($config['document_root']);

require_once __DIR__ . '/console/core/Parameters/ScriptParams.class.php';
require_once $frameworkDir . 'core/FrameworkClasses/Configuration/ConfigLocations.class.php';
require_once $frameworkDir . 'core/FrameworkClasses/Configuration/ConfigLoader.class.php';



spl_autoload_register(array($classLoader, 'loadClass'));

echo $nl . $separatorStart . $dnl;

try
{
    /** @var \Framework\Core\FrameworkClasses\Configuration\ConfigLoader */
    $configLoader = null;
    $unparsedConfig = array();
    $configFileMapping = array();

    $configLoader = new \Framework\Core\FrameworkClasses\Configuration\ConfigLoader(\Framework\Core\FrameworkClasses\Configuration\ConfigLocations::PHP_FILE);
    $newConfiguration = $configLoader->loadConfiguration($frameworkDir . 'config/', $config, null, $unparsedConfig, $configFileMapping);

    $dbObj = null;

    if ($config['config_type'] == \Framework\Core\FrameworkClasses\Configuration\ConfigLocations::DB)
    {
        $dbFactory = new \Framework\Core\Database\DbFactory(array('simplef_db' => $config['config_db']));

        $dbObj = $dbFactory->GetDbInstance('simplef_db');
    }

    $config = new Console\Core\Config\ConfigFromFile($configDir, $config['document_root'] . 'framework/config/constants.php', $unparsedConfig, $newConfiguration, $configFileMapping, $dbObj);

    $sfAssist = new \Console\Core\SfAssist($argv, $config, $configConsole);

    $sfAssist->execute($configConsole['action_mapping']);

}
catch (Exception $ex)
{
    echo '### ERROR ###  ' . PHP_EOL . PHP_EOL . $ex->getMessage() . PHP_EOL . PHP_EOL . '#############';
}

echo $dnl . $separatorEnd . $nl;