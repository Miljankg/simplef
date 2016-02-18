<?php

namespace Core\Logging;

use Core\IO\File;
use \Exception;

/**
 * Provides API for logging into log files.
 *
 * @author Miljan Pantic
 */
class Logger implements ILogger
{
    //<editor-fold desc="Members">

    /** @var ILogger */
    private static $instance = null;

    private $logFile = null;
    private $timeFormat = "[ Y-m-d H:i:s ]";
    private $newLine = "\n";
    private $isDebug = false;

    private $prefixError = "== ERROR ==";
    private $prefixInfo = "== INFO ==";
    private $prefixWarning = "== WARNING ==";
    private $prefixDebug = "== DEBUG ==";

    //</editor-fold>

    //<editor-fold desc="ILogger functions">

    /**
     * Retrieves logger instance
     *
     * @return ILogger Instance of the logger.
     * @throws Exception If the logger instance is not set.
     */
    public static function getInstance()
    {
        if (Logger::$instance == null)
            throw new Exception("Logger instance is not set.");

        return Logger::$instance;
    }

    /**
     * Sets logger instance.
     *
     * @param string $logFile Log file path.
     * @param string $newLine New line char.
     * @param string $timestampFormat Timestamp format.
     * @param bool $isDebug Is debug mode on or off.
     * @throws Exception If instance already exists.
     */
    public static function setInstance($logFile, $newLine, $timestampFormat, $isDebug)
    {
        if (Logger::$instance != null)
            throw new Exception("Logger instance is already set.");

        Logger::$instance = new Logger($logFile, $newLine, $timestampFormat, $isDebug);
    }

    /**
     * Log error to log file.
     *
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public function logError($text)
    {
        $this->saveToLogFile(Logger::getPrefix("error") . $text);
    }

    /**
     * Log Info to log file.
     *
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public function logInfo($text)
    {
        $this->saveToLogFile(Logger::getPrefix("info") . $text);
    }

    /**
     * Log Warning to log file.
     *
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public function logWarning($text)
    {
        $this->saveToLogFile(Logger::getPrefix("warning") . $text);
    }

    /**
     * Log Debug entry to log file, if debug mode is on.
     *
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public function logDebug($text)
    {
        if ($this->isDebug)
            $this->saveToLogFile(Logger::getPrefix("debug") . $text);
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Constructor.
     *
     * @param string $logFile Log file path.
     * @param string $newLine New line char.
     * @param string $timestampFormat Timestamp format.
     * @param bool $isDebug Is debug mode on or off.
     */
    private function __construct($logFile, $newLine, $timestampFormat, $isDebug)
    {
        $this->logFile = $logFile;
        $this->newLine = $newLine;
        $this->timeFormat = $timestampFormat;
        $this->isDebug = $isDebug;
    }

    /**
     * Writes to a log file.
     *
     * @param string $file Log file to write to.
     * @param string $text Text to write to the log file.
     */
    private function writeToLogFile($file, $text)
    {
        $entry = $this->convertTextToLogEntry($text);

        File::writeToFile($file, $entry, true);
    }

    /**
     * Returns prefix of selected type.
     *
     * @param string $prefixType Prefix type to return (error, info, warning).
     * @return string Parsed prefix.
     * @throws Exception If passed prefix type is not supported.
     */
    private function getPrefix($prefixType)
    {
        switch ($prefixType) {

            case "error" :
                $prefix = $this->prefixError;
                break;
            case "info" :
                $prefix = $this->prefixInfo;
                break;
            case "warning" :
                $prefix = $this->prefixWarning;
                break;
            case "debug" :
                $prefix = $this->prefixDebug;
                break;
            default :
                throw new Exception("Unsupported prefix type $prefixType.");
        }

        return $prefix . $this->newLine;
    }

    /**
     * Saves to log file.
     *
     * @param string $text Text to save.
     * @throws \Exception If log file path is not set.
     */
    private function saveToLogFile($text)
    {
        if ($this->logFile == null)
            throw new \Exception("Log file path is not set.");

        $this->writeToLogFile($this->logFile, $text);
    }

    /**
     * Gets timestamp for the log entry.
     *
     * @return string Timestamp for log entry.
     */
    private function getTimestamp()
    {
        $timestamp = new \DateTime();

        return $timestamp->format($this->timeFormat);
    }

    /**
     * Converts passed text to the log entry.
     *
     * @param string $textToConvert Text to convert to the log entry.
     * @return string Log entry.
     */
    private function convertTextToLogEntry($textToConvert)
    {
        $nl = $this->newLine;

        $timeComponent = $this->getTimestamp() . " ";

        $logEntryFormat = "$timeComponent %s";

        $numOfSpaces = strlen($timeComponent) + 2; // + two chars of \n                

        // new line char hardcoded because of trace parsing from the exception strings
        $entry = sprintf(
            $logEntryFormat,
            str_replace("\n", str_pad("\n", $numOfSpaces), $textToConvert)
        );

        return $entry . $nl . $nl;
    }

    //</editor-fold>
}
