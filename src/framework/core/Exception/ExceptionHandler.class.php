<?php

namespace Core\Exception;

use Core\Logging\Logger;

/**
 * Provides API for exception handling.
 *
 * @author Miljan Pantic
 */
class ExceptionHandler {

    private static $isCli = false;
    
    /* Interface functions */
    
    /**
     * Handles passed exception.
     * 
     * @param \Exception $ex Exception to be handled.
     */
    public static function handleException(\Exception $ex) {
        
        $exText = ExceptionHandler::getExceptionString($ex);
        
        ExceptionHandler::outputExceptionText($exText);
    }
        
    
    /**
     * Sets is CLI internal variable of ExceptionHandler.
     * 
     * @param bool $isCli Is CLI or not.
     */
    public static function setIsCli($isCli) {
        
        ExceptionHandler::$isCli = $isCli;
        
    }
    
    /***********************/
    
    /* Internal functions */
    
    /**
     * Generates string from Exception object.
     * 
     * @param \Exception $ex Exception to get string from.
     * @return string Generated string from Exception.
     */
    private static function getExceptionString(\Exception $ex) {
        
        $exText = "Message: " . $ex->getMessage() . "\n";
        $exText .= "Code:   " . $ex->getCode() . "\n";
        $exText .= "File:   " . $ex->getFile() . "\n";
        $exText .= "Line:   " . $ex->getLine() . "\n";
        $exText .= "Trace:\n\n" . $ex->getTraceAsString() . "\n";
        
        $previous = $ex->getPrevious();
        
        if ($previous != null) {
            
            $exText .= "\n\n==== Previous Exception: ====\n\n" 
                    . ExceptionHandler::getExceptionString($previous);
            
        }
        
        return $exText;
        
    }
    
    /**
     * Outputs text to the output and / or logger (if logger is set).
     * 
     * @param string $exText Exception string.
     */
    private static function outputExceptionText($exText) {
        
        if (!Logger::isSetUpDone()) {
            
            $exText .= "\n\nWARNING: Logger is not set and this is not written to the log file";                        
            
        } else {
            
            Logger::logError($exText);
            
        }
                
        if (!ExceptionHandler::$isCli) {
            
            $exText = ExceptionHandler::prepTextForBrowser($exText);
            
        }
        
        die($exText);
        
    }        
    
    /**
     * Converts string for output for browser.
     * 
     * @param string $exText string to be converted.
     * @return string Converted string.
     */
    private static function prepTextForBrowser($exText) {
        
        return str_replace(array("\n", ""), array("<br/>", ""), $exText);
        
    }

    /**********************/
}
