<?php

namespace Framework\Core\FrameworkClasses\Configuration;

use Framework\Core\Database\DB;
use Framework\Core\IO\File;

/**
 * Class that loads configuration from the specific location.
 *
 * @author Miljan Pantic
 */
class ConfigLoader {
    
    private $configLocation;   
    
    public function __construct($configLocation) {
        
        $this->configLocation = $configLocation;
        
    }
    
    /* Interface functions */

    /**
     *
     * @param string $configPhpFileDir config file location
     * @param array $alreadyLoadedConfig Config that is already loaded
     * @param DB $dbObj database object for config from DB
     * @param bool $unparsed
     * @param null $configFileMapping
     * @return array Loaded configuration
     * @throws \Exception
     */
    public function loadConfiguration(
            $configPhpFileDir = null, 
            array $alreadyLoadedConfig = array(), 
            $dbObj = null,
            &$unparsed = false,
            &$configFileMapping = null
            ) {

        $config = array();

        switch ($this->configLocation) {
            
            case ConfigLocations::PHP_FILE:
                $config = $this->loadConfigurationFromPhpFile($configPhpFileDir, $alreadyLoadedConfig, $configFileMapping);
                break;
            case ConfigLocations::DB:
                $config = $this->loadConfigurationFromDb($dbObj);
                break;
            default :
                throw new \Exception("Invalid config location \"$this->configLocation\"");
                
        }

        if (is_array($unparsed))
            $unparsed = $config;

        $this->parseConfigValues($config);

        return $config;

    }
            
    
    /**********************/
            
    /* Internal functions */

    private function parseConfigValues(&$config)
    {
        foreach ($config as $key => $value)
        {
            if (!is_array($value) && preg_match_all('/#\$(.*?)#/', $value, $matches) > 0)
            {
                foreach ($matches[1] as $matchKey => $match)
                {
                    if (!isset($config[$match]))
                        throw new \Exception('Config does not contain index: ' . $match . ' to re-use.');

                    if (is_array($config[$match]))
                        throw new \Exception('Array config ' . $match . ' cannot be reused.');

                    $config[$key] = str_replace($matches[0][$matchKey], $config[$match], $config[$key]);
                }
            }
        }
    }

    /**
     * Loads PHP configuration files from the specified location. Function includes
     * all config files, except main config and those files will contain Config::add entries.
     *
     * @param string $configPhpFileDir Directory where config php files are stored.
     * @param array $alreadyLoadedConfig Config that is already loaded.
     * @param $configFileMapping
     * @return array
     */
    private function loadConfigurationFromPhpFile($configPhpFileDir, array $alreadyLoadedConfig = array(), &$configFileMapping = null) {

        $confDirToLoadFrom = $configPhpFileDir . '/';

        $filesToLoad = File::getFileList(
                 $confDirToLoadFrom,
                 '/.*config_.*\.php/',
                 false
                 );

        $config = $alreadyLoadedConfig;

        foreach ($filesToLoad as $fileToLoad)
        {
            /** @noinspection PhpIncludeInspection */
            require_once $fileToLoad;

            if (is_array($configFileMapping)) {
                foreach ($config as $key => $value) {
                    if (!array_key_exists($key, $alreadyLoadedConfig) && !array_key_exists($key, $configFileMapping))
                        $configFileMapping[$key] = $fileToLoad;
                }
            }
        }

        return $config;
    }

    /**
     * Loads configuration from the DB.
     *
     * @param DB $dbObj Database connection object
     * @return array Loaded configuration
     */
    private function loadConfigurationFromDb($dbObj) {
        
        return array();
        
    }
    
    /**********************/
    
}
