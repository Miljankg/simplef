<?php

namespace Core\CLI;

/**
 * Provides API for CLI
 *
 * @author Miljan Pantic
 */
class CLIUtils {
    
    /**
     * Is invocation from CLI or browser. 
     * 
     * @return bool is invocation CLI or not 
     */
    public static function isCli() {
        
        return php_sapi_name() == 'cli' ;
        
    }
    
}
