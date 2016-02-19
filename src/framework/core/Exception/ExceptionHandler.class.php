<?php

namespace Core\Exception;

use Core\Logging\ILogger;
use Core\URLUtils\URL;
use \Exception;

/**
 * Provides API for exception handling.
 *
 * @author Miljan Pantic
 */
class ExceptionHandler implements IExceptionHandler
{
    //<editor-fold desc="Members">

    /** @var bool */
    private static $isCli = false;

    /** @var bool */
    private static $showErrorPage = false;

    /** @var string */
    private static $errorPageUrl = "";

    /** @var string */
    private static $newLine = PHP_EOL;

    /** @var ILogger */
    private static $logger = null;

    private static $logLevel = "ALL";

    private static $systemExceptionType = "Exception";

    //</editor-fold>

    //<editor-fold desc="IException Handler functions">

    /**
     * Handles passed exception.
     *
     * @param Exception $ex Exception to be handled.
     */
    public static function handleException(Exception $ex)
    {
        $exText = ExceptionHandler::getExceptionString($ex);

        $exText = ExceptionHandler::saveToLogFile($exText, get_class($ex));

        if (!ExceptionHandler::$showErrorPage || empty(ExceptionHandler::$errorPageUrl))
            ExceptionHandler::outputExceptionText($exText);
        else
            URL::redirect(ExceptionHandler::$errorPageUrl);
    }

    /**
     * Sets logger instance.
     *
     * @param ILogger $loggerObj Logger instance.
     * @throws Exception If Logger is null.
     */
    public static function setLogger(ILogger $loggerObj)
    {
        if ($loggerObj == null)
            throw new Exception("Logger cannot be null.");

        ExceptionHandler::$logger = $loggerObj;
    }

    /**
     * Initializes some of the ExceptionHandler parameters. If this function is not called
     * default values are used.
     *
     * @param bool $isCli Should exception output be parsed for CLI or not.
     * @param bool $showErrorPage Should error page be shown or error text.
     * @param string $errorPageUrl Url of the error page to redirect to.
     * @param string $logLevel Log level.
     * @param string $systemExceptionType System exception type.
     */
    public static function setParams($isCli, $showErrorPage, $errorPageUrl, $logLevel, $systemExceptionType)
    {
        ExceptionHandler::$isCli = $isCli;
        ExceptionHandler::$showErrorPage = $showErrorPage;
        ExceptionHandler::$errorPageUrl = $errorPageUrl;
        ExceptionHandler::$logLevel = $logLevel;
        ExceptionHandler::$systemExceptionType = $systemExceptionType;
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    /**
     * Generates string from Exception object.
     *
     * @param \Exception $ex Exception to get string from.
     * @return string Generated string from Exception.
     */
    private static function getExceptionString(\Exception $ex)
    {
        $nl = ExceptionHandler::$newLine;
        $dnl = $nl . $nl;

        $exText = "Message: " . $ex->getMessage() . $nl;
        $exText .= "Code:   " . $ex->getCode() . $nl;
        $exText .= "File:   " . $ex->getFile() . $nl;
        $exText .= "Line:   " . $ex->getLine() . $nl;
        $exText .= "Trace:$dnl" . $ex->getTraceAsString() . $nl;

        $previous = $ex->getPrevious();

        if ($previous != null)
        {
            $exText .= "$dnl==== Previous Exception: ====$dnl"
                . ExceptionHandler::getExceptionString($previous);
        }

        return $exText;
    }

    /**
     * Outputs text to the output and / or logs an error text (if logger is set).
     *
     * It can also redirect to the error page if right parameter is set in the init function.
     *
     * @param string $exText Exception string.
     * @return bool False if code is not outputted properly.
     */
    private static function outputExceptionText($exText)
    {
        if (!ExceptionHandler::$isCli)
            $exText = ExceptionHandler::prepTextForBrowser($exText);

        die($exText);
    }

    /**
     * Logs error to log file.
     *
     * @param string $exText Exception string.
     * @param string $exType Exception type.
     * @return string Since error text can be adjusted if the logger is not set, new text is returned so it can be used further.
     */
    private static function saveToLogFile($exText, $exType)
    {
        $nl = ExceptionHandler::$newLine;

        $exTypeNameArr = explode("\\", $exType);
        $exTypeName = end($exTypeNameArr);

        if (ExceptionHandler::$logger === null)
            $exText .= "{$nl}WARNING: Logger is not set and this is not written to the log file";
        else
        {
            switch (ExceptionHandler::$logLevel)
            {
                case LOG_LEVEL_ALL:
                    ExceptionHandler::$logger->logError($exText);
                    break;

                default:
                    $exTypesToLog = explode(",", ExceptionHandler::$logLevel);

                    if (in_array($exTypeName, $exTypesToLog))
                        ExceptionHandler::$logger->logError($exText);
                    break;
            }
        }

        return $exText;
    }

    /**
     * Converts string for output for browser.
     *
     * @param string $exText string to be converted.
     * @return string Converted string.
     */
    private static function prepTextForBrowser($exText)
    {
        return str_replace(array("\n", ""), array("<br/>", ""), $exText);
    }

    //</editor-fold>
}
