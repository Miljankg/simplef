<?php

namespace Core\Components;

use Core\Configuration\Config;
use Core\Lang\Language;
use Core\Database\DB;

/**
 * API For loading Output Component and their logic dependencies.
 *
 * @author Miljan Pantic
 */
class SFComponentLoader {
    
    private $outputComponentsDir = "";
    private $outputComponentsTplDir = "";
    private $configuredOutputComponents = "";
    private $tplEngine = null;    
    private $outputComponentNamespace = '';
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
    
    public function __construct(
            $outputComponentsDir, 
            $outputComponentsTplDir, 
            $configuredOutputComponents,
            $outputComponentNamespace,
            $logicComponentNamespace,
            \Smarty $tplEngine,
            $configType,
            $currLang,
            $wrapComponents,            
            $logicCompDir,
            $outCompLogic,
            DB $db = null,
            $commonComponents,
            $currPageName) {
        
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
        
    }
    
    /**
     * Loads passed list of output components.
     * 
     * @param array $outputComponentsToLoad
     * @param string $currLang
     * @return array Array of component contents
     * @throws \Exception If some component is not an instance of the OutputComponent class
     */
    public function loadOutputComponents(array $outputComponentsToLoad) {
        
        $componentContents = array();
        
        $ocConfigVals = array(
            'template',
            'css',
            'js'
        );

        foreach ($this->commonComponents as $component => $exceptionPage) {

            if ($this->currPageName != $exceptionPage)
                $outputComponentsToLoad[] = $component;

        }
        
        foreach ($outputComponentsToLoad as $outComponentName) {                        
            
            if (!isset($this->configuredOutputComponents[$outComponentName])) {
                
                throw new \Exception("Output component \"$outComponentName\" is not configured, but it is listed in the pages dependencies.");
                
            }
            
            $ocData = array();
            
            foreach ($ocConfigVals as $ocConfigVal) {
                
                $ocData[$ocConfigVal] = true;
                
                if (isset($this->configuredOutputComponents[$outComponentName][$ocConfigVal]) &&
                    !$this->configuredOutputComponents[$outComponentName][$ocConfigVal]
                        ) {
                    
                    $ocData[$ocConfigVal] = false;
                    
                }
                
            }
            
            $componentDir = $this->outputComponentsDir . $outComponentName . '/';
            
            $componentPhpFile = $componentDir . $outComponentName . '.php';
            $componentConfigDir = $componentDir . 'config/';
            $componentLangDir = $componentDir . 'lang/';
            
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
            
            $logicComponents = $this->loadLogicComponents($outComponentName);
                                    
            $className = $this->outputComponentNamespace . $this->getClassName($outComponentName);
            
            $outputComponent = new $className($outComponentName, $configObj, $langObj, $logicComponents);
            
            if (!$outputComponent instanceof OutputComponent) {
                
                throw new \Exception("Component \"$outputComponent\" is not an instance of OutputComponent.");
                
            }
            
            if ($ocData['template']) {
                            
                $outputComponent->enableTplLoading(
                        $this->tplEngine, 
                        $this->outputComponentsTplDir . $outComponentName . '/' . $outComponentName . '.tpl'
                    );
            
            }
            
            $this->tplEngine->assign('configOutComp', $configObj);
            $this->tplEngine->assign('langOutComp', $langObj);
            
            $componentContent = $outputComponent->runComponentLogic();                        
            
            if ($componentContent != null) {
                
                if ($this->wrapComponents) {
                    
                    $componentContent = "<div id=\"{$outComponentName}\">\n{$componentContent}\n</div>";
                    
                }
                
                $componentContents[$outComponentName]['content'] = $componentContent;                
                $componentContents[$outComponentName]['css'] = $ocData['js'];
                $componentContents[$outComponentName]['js'] = $ocData['css'];
                
            }
            
        }                
        
        return $componentContents;
        
    }
    
    private function loadLogicComponents($compName) {
        
        if (!isset($this->outCompLogic[$compName]) || 
                !is_array($this->outCompLogic[$compName])) {
            
            return array();
            
        }            
        
        $logicForReturn = array();
        
        $logicToLoad = $this->outCompLogic[$compName];
        
        foreach ($logicToLoad as $logic) {
            
            if (array_key_exists($logic, $this->loadedLogicComponents)) {
                
                $logicForReturn[$logic] = $this->loadedLogicComponents[$logic];
                
                continue;
                
            }
            
            $logicCompDir = $this->logicCompDir . $logic . '/';
            $logicCompConfigDir = $logicCompDir . 'config/';
            
            require_once $logicCompDir . $logic . '.php';
            
            $configObj = $this->loadComponentConfig($logicCompConfigDir, $logic);
            
            $className = $this->logicComponentNamespace . $this->getClassName($logic);
            
            $logicComponentObj = new $className($logic, $configObj, $this->db);
            
            if (!$logicComponentObj instanceof LogicComponent) {
                
                throw new \Exception("Component \"$logic\" is not an instance of LogicComponent.");
                
            }
            
            $logicComponentObj->init();
            
            $this->loadedLogicComponents[$logic] = $logicComponentObj;
            $logicForReturn[$logic] = $logicComponentObj;
            
        }
        
        return $logicForReturn;
        
    }
    
    private function loadComponentLang($langDir, $lang, $compName) {
        
        $langObj = new Language($compName);
        
        $langObj->loadLang($lang, $langDir);
        
        return $langObj;        
        
    }
    
    private function loadComponentConfig($configDir, $configNamespace) {        
        
        $config = array();
        
        require $configDir . 'config.php';
        
        $configObj = new Config($configNamespace);
        
        $configObj->addMultipleConfigValues($config);
        
        return $configObj;
        
    }
    
    private function getClassName($componentName) {
        
        $separator = "_";
        
        $compNameArr = explode($separator, $componentName);
        
        $className = "";
        
        foreach ($compNameArr as $compNameArrElement) {
            
            $className .= ucfirst($compNameArrElement);
            
        }
        
        return $className;
        
    }
    
}
