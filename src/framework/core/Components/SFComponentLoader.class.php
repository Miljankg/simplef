<?php

namespace Core\Components;

use Core\Configuration\Config;
use Core\Lang\Language;
use Core\Database\DB;
use Core\Logging\ILogger;
use \Exception;

/**
 * API For loading Output Component and their logic dependencies.
 *
 * @author Miljan Pantic
 */
class SFComponentLoader implements IComponentLoader
{
    //<editor-fold desc="Members">

    private $outputComponentsDir = "";
    private $outputComponentsTplDir = "";
    private $configuredOutputComponents = "";
    private $tplEngine = null;    
    private $outputComponentNamespace = '';
    private $logicComponentConfig;
    private $logicComponentNamespace = '';
    private $configType = null;
    private $currLang = "";
    private $wrapComponents = false;
    private $db = null;
    private $logicCompDir = "";
    private $outCompLogic = "";
    private $loadedLogicComponents = array();
    private $commonComponents = array();
    private $currPageName;

    /** @var ILogger */
    private $logger = null;

    //</editor-fold>

    //<editor-fold desc="Constructor">

    public function __construct(
        $outputComponentsDir,
        $outputComponentsTplDir,
        $configuredOutputComponents,
        $outputComponentNamespace,
        array $logicComponentConfig,
        $logicComponentNamespace,
        /** @noinspection PhpUndefinedClassInspection */
        \Smarty $tplEngine,
        $configType,
        $currLang,
        $wrapComponents,
        $logicCompDir,
        $outCompLogic,
        DB $db = null,
        $commonComponents,
        $currPageName,
        ILogger $logger)
    {
        $this->outputComponentsDir = $outputComponentsDir;
        $this->outputComponentsTplDir = $outputComponentsTplDir;
        $this->configuredOutputComponents = $configuredOutputComponents;
        $this->tplEngine = $tplEngine;
        $this->configType = $configType;
        $this->outputComponentNamespace = $outputComponentNamespace;
        $this->currLang = $currLang;
        $this->wrapComponents = $wrapComponents;
        $this->db = $db;
        $this->logicCompDir = $logicCompDir;
        $this->outCompLogic = $outCompLogic;
        $this->logicComponentNamespace = $logicComponentNamespace;
        $this->commonComponents = $commonComponents;
        $this->currPageName = $currPageName;
        $this->logger = $logger;
        $this->logicComponentConfig = $logicComponentConfig;
    }

    //</editor-fold>

    //<editor-fold desc="IComponentLoader functions">

    /**
     * Loads passed list of output components.
     *
     * @param array $outputComponentsToLoad
     * @return array Array of component contents
     * @throws Exception If some component is not an instance of the OutputComponent class
     */
    public function loadOutputComponents(array $outputComponentsToLoad)
    {
        $componentContents = array();
        
        $ocConfigVals = array(
            'template',
            'css',
            'js'
        );

        foreach ($this->commonComponents as $component => $exceptionPage)
            if ($this->currPageName != $exceptionPage)
                $outputComponentsToLoad[] = $component;
        
        foreach ($outputComponentsToLoad as $outComponentName)
        {
            if (!isset($this->configuredOutputComponents[$outComponentName]))
                throw new Exception("Output component \"$outComponentName\" is not configured, but it is listed in the pages dependencies.");

            $ocData = array();

            foreach ($ocConfigVals as $ocConfigVal)
            {
                $ocData[$ocConfigVal] = true;

                if (isset($this->configuredOutputComponents[$outComponentName][$ocConfigVal]) &&
                    !$this->configuredOutputComponents[$outComponentName][$ocConfigVal]
                )
                    $ocData[$ocConfigVal] = false;
            }

            $componentDir = $this->outputComponentsDir . $outComponentName . '/';
            $componentPhpFile = $componentDir . $outComponentName . '.php';
            $componentConfigDir = $componentDir . 'config/';
            $componentLangDir = $componentDir . 'lang/';

            $this->logger->logDebug("Loading Output component \"{$outComponentName}\"");

            /** @noinspection PhpIncludeInspection */
            require $componentPhpFile;

            $langObj = $this->loadComponentLang(
                $componentLangDir,
                $this->currLang,
                $outComponentName
            );

            $configObj = $this->loadComponentConfig(
                $componentConfigDir,
                $outComponentName
            );

            $logicComponents = array();

            if (isset($this->outCompLogic[$outComponentName]) &&
                is_array($this->outCompLogic[$outComponentName])
            )
            {
                $logicToLoad = $this->outCompLogic[$outComponentName];
                $logicComponents = $this->loadLogicComponents($logicToLoad);
            }
                                    
            $className = $this->outputComponentNamespace . $this->getClassName($outComponentName);
            
            $outputComponent = new $className($outComponentName, $configObj, $langObj, $logicComponents);
            
            if (!$outputComponent instanceof OutputComponent)
                throw new Exception("Component \"$outputComponent\" is not an instance of OutputComponent.");

            
            if ($ocData['template'])
                $outputComponent->enableTplLoading(
                        $this->tplEngine, 
                        $this->outputComponentsTplDir . $outComponentName . '/' . $outComponentName . '.tpl'
                    );
            
            $this->tplEngine->assign('configOutComp', $configObj);
            $this->tplEngine->assign('langOutComp', $langObj);
            
            $componentContent = $outputComponent->runComponentLogic();                        
            
            if ($componentContent != null)
            {
                if ($this->wrapComponents)
                    $componentContent = "<div id=\"{$outComponentName}\" class=\"output-component\">\n{$componentContent}\n</div>";

                $componentContents[$outComponentName]['content'] = $componentContent;
                $componentContents[$outComponentName]['css'] = $ocData['js'];
                $componentContents[$outComponentName]['js'] = $ocData['css'];
            }
        }                
        
        return $componentContents;
    }

    /**
     * Load multiple logic components.
     *
     * @param string[] $logicComponentsToLoad Array of logic component names to load.
     * @return LogicComponent[] Loaded logic components.
     * @throws Exception If logic component is not configured properly.
     */
    public function loadLogicComponents($logicComponentsToLoad)
    {
        $logicForReturn = array();
        
        foreach ($logicComponentsToLoad as $logicToLoad => $logicDependencies) {
            if (!isset($this->logicComponentConfig[$logicToLoad]))
                throw new Exception("Logic \"{$logicToLoad}\" is not configured properly.");

            if (array_key_exists($logicToLoad, $this->loadedLogicComponents)) {
                $logicForReturn[$logicToLoad] = $this->loadedLogicComponents[$logicToLoad];
                continue;
            }

            $logicDependencies = array();

            if (is_array($logicDependencies) && !empty($logicDependencies))
            {
                $logicDependencies = $this->loadLogicComponents($logicDependencies);
            }

            $logicForReturn[$logicToLoad] = $this->loadLogicComponent($logicToLoad, $logicDependencies);
        }
        
        return $logicForReturn;
    }

    /**
     * Loads logic component, by passed component name.
     *
     * @param string $logicToLoad Logic component name to load.
     * @param LogicComponent[] $logicComponentDependencies Logic components that current logic component depends on.
     * @return LogicComponent Loaded logic component object.
     * @throws Exception If loaded component does not extends the LogicComponent class.
     */
    public function loadLogicComponent($logicToLoad, array $logicComponentDependencies)
    {
        $logicCompDir = $this->logicCompDir . $logicToLoad . '/';
        $logicCompConfigDir = $logicCompDir . 'config/';

        $this->logger->logDebug("Loading Logic component \"{$logicToLoad}\"");

        /** @noinspection PhpIncludeInspection */
        require_once $logicCompDir . $logicToLoad . '.php';

        $configObj = $this->loadComponentConfig($logicCompConfigDir, $logicToLoad);

        $className = $this->logicComponentNamespace . $this->getClassName($logicToLoad);

        $logicComponentObj = new $className($logicToLoad, $configObj, $this->db, $logicComponentDependencies);

        if (!$logicComponentObj instanceof LogicComponent)
            throw new Exception("Component \"$logicToLoad\" is not an instance of LogicComponent.");

        $logicComponentObj->init();

        $this->loadedLogicComponents[$logicToLoad] = $logicComponentObj;

        return $logicComponentObj;
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Load component language.
     *
     * @param string $langDir Language directory.
     * @param string $lang Current language.
     * @param string $compName Component name.
     * @return Language Language object.
     */
    private function loadComponentLang($langDir, $lang, $compName)
    {
        $langObj = new Language($compName);
        $langObj->loadLang($lang, $langDir);
        
        return $langObj;
    }

    /**
     * Loads component config.
     *
     * @param string $configDir Configuration directory.
     * @param string $configNamespace Configuration namespace.
     * @return Config Config object.
     */
    private function loadComponentConfig($configDir, $configNamespace)
    {
        $config = array();

        /** @noinspection PhpIncludeInspection */
        require $configDir . 'config.php';
        
        $configObj = new Config($configNamespace);
        $configObj->addMultipleConfigValues($config);
        
        return $configObj;
    }

    /**
     * Retrieves class name from component name.
     *
     * @param string $componentName Component name.
     * @return string Class name.
     */
    private function getClassName($componentName)
    {
        $separator = "_";
        $compNameArr = explode($separator, $componentName);
        $className = "";
        
        foreach ($compNameArr as $compNameArrElement)
            $className .= ucfirst($compNameArrElement);
        
        return $className;
    }

    //</editor-fold>
}
