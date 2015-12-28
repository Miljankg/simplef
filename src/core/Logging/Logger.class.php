<?php

namespace Core\Logging;

use Core\IO as IO;

/**
 * Provides API for logging into log files.
 *
 * @author Miljan Pantic
 */
class Logger {
        
    private static $logFile = null;
    private static $timeFormat = "[ Y-m-d H:i:s ]";
    private static $newLine = "\n";
    private static $isSet = false;
    
    private static $prefixError = "== ERROR ==";
    private static $prefixInfo = "== INFO ==";
    private static $prefixWarning = "== WARNING ==";
    
    /* Interface functions */
    
    /**
     * Log error to log file.
     * 
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public static function logError($text) {        
        
        Logger::saveToLogFile(Logger::getPrefix("error") . $text);
        
    }
    
    /**
     * Log Info to log file.
     * 
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public static function logInfo($text) {        
        
        Logger::saveToLogFile(Logger::getPrefix("info") . $text);
        
    }
    
    /**
     * Log Warning to log file.
     * 
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public static function logWarning($text) {        
        
        Logger::saveToLogFile(Logger::getPrefix("warning") . $text);
        
    }
    
    /**
     * Sets log file path.
     * 
     * @param string $logFilePath Log file path.
     */
    public static function setLogFile($logFilePath) {
        
        Logger::$logFile = $logFilePath;
                
        Logger::$isSet = true;
        
    }
    
    /**
     * Sets timestamp format.
     * 
     * @param string $format Format to set.
     */
    public static function setTimestampFormat($format) {
        
        Logger::$timeFormat = $format;
        
    }
    
    /**
     * Sets new line char.
     * 
     * @param string $newLine New line char.
     */
    public static function setNewLine($newLine) {
        
        Logger::$newLine = $newLine;
        
    }
    
    /**
     * Says if logger is set up or not.
     * 
     * @return bool Is Logger all set-up or not.
     */
    public static function isSetUpDone() {
        
        return Logger::$isSet;
        
    }
    
    /**
     * Writes to a log file.
     * 
     * @param string $file Log file to write to.
     * @param string $text Text to write to the log file.
     */
    private static function writeToLogFile($file, $text) {                
        
        $entry = Logger::convertTextToLogEntry($text);
        
        IO\File::writeToFile($file, $entry, true);
        
    }
    
    /**********************/
    
    /* Internal functions */        
    
    /**
     * Returns prefix of selected type.
     * 
     * @param string $prefixType Prefix type to return (error, info, warning).
     * @return string Parsed prefix.
     * @throws Exception If passed prefix type is not supported.
     */
    private static function getPrefix($prefixType) {
        
        $prefix = "";
        
        switch ($prefixType) {
            
            case "error" :
                $prefix = Logger::$prefixError;
                break;
            case "info" :
                $prefix = Logger::$prefixInfo;
                break;
            case "warning" : 
                $prefix = Logger::$prefixWarning;
                break;
            default : 
                throw new Exception("Unsupported prefix type $prefixType.");                
        }
        
        return $prefix . "\n";
    }
    
    /**
     * Saves to log file.
     * 
     * @param string $text Text to save.
     * @throws \Exception If log file path is not set.
     */
    private static function saveToLogFile($text) {
        
        if (Logger::$logFile == null) {
            
            throw new \Exception("Log file path is not set.");
            
        }
        
        Logger::writeToLogFile(Logger::$logFile, $text);
    }
    
    /**
     * Gets timestamp for the log entry.
     * 
     * @return string Timestamp for log entry.
     */
    private static function getTimestamp() {                        
        
        $timestamp = new \DateTime();
        
        return $timestamp->format(Logger::$timeFormat);
        
    }
    
    /**
     * Converts passed text to the log entry.
     * 
     * @param string $textToConvert Text to convert to the log entry.
     * @return string Log entry.
     */
    private static function convertTextToLogEntry($textToConvert) {
        
        $timeComponent = Logger::getTimestamp() . " ";
        
        $logEntryFormat = "$timeComponent %s";         
        
        $numOfSpaces = strlen($timeComponent) + 2; // + two chars of \n                
        
        $entry = sprintf(
                $logEntryFormat, 
                str_replace("\n", str_pad("\n", $numOfSpaces), $textToConvert)
                );
        
        return "\n" . $entry;
    }
    
    /**********************/
    
}
