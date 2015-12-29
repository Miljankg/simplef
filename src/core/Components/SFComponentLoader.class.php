<?php

namespace Core\Components;

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
    private $outputComponentNamespace = 'Components\\Output\\';
    
    public function __construct(
            $outputComponentsDir, 
            $outputComponentsTplDir, 
            $configuredOutputComponents,
            \Smarty $tplEngine) {
        
        $this->outputComponentsDir = $outputComponentsDir;
        $this->outputComponentsTplDir = $outputComponentsTplDir;
        $this->configuredOutputComponents = $configuredOutputComponents;
        $this->tplEngine = $tplEngine;
        
    }
    
    /**
     * Loads passed list of output components.
     * 
     * @param array $outputComponentsToLoad
     * @return array Array of component contents
     * @throws \Exception If some component is not an instance of the OutputComponent class
     */
    public function loadOutputComponents(array $outputComponentsToLoad) {
        
        $componentContents = array();
        
        foreach ($outputComponentsToLoad as $outComponentName) {
            
            $componentPhpFile = $this->outputComponentsDir . $outComponentName . '/' . $outComponentName . '.php';
            
            require_once $componentPhpFile;
            
            $className = $this->outputComponentNamespace . $this->getClassName($outComponentName);
            
            $outputComponent = new $className($outComponentName);
            
            if (!$outputComponent instanceof OutputComponent) {
                
                throw new \Exception("Component \"$outputComponent\" is not an instance of OutputComponent.");
                
            }
            
            // Condition this!
            $outputComponent->enableTplLoading(
                    $this->tplEngine, 
                    $this->outputComponentsTplDir . $outComponentName . '.tpl'
                    );
            
            $componentContent = $outputComponent->runComponentLogic();
            
            if ($componentContent != null) {
                
                $componentContents[$outComponentName] = $componentContent;                
                
            }
            
        }
        
        return $componentContents;
        
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
