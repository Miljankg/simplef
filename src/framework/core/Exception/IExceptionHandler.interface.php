<?php

namespace Core\Exception;

use \Exception;
use Core\Logging\ILogger;

interface IExceptionHandler
{
    /**
     * Initializes some of the ExceptionHandler parameters. If this function is not called
     * default values are used.
     *
     * @param bool $isCli Should exception output be parsed for CLI or not.
     * @param bool $showErrorPage Should error page be shown or error text.
     * @param string $errorPageUrl Url of the error page to redirect to.
     * @param string $logLevel Log level.
     * @param string $systemExceptionType System exception type.
     * @return
     */
    public static function setParams($isCli, $showErrorPage, $errorPageUrl, $logLevel, $systemExceptionType);

    /**
     * Sets logger instance.
     *
     * @param ILogger $loggerObj Logger instance.
     */
    public static function setLogger(ILogger $loggerObj);

    /**
     * Handles passed exception.
     *
     * @param Exception $ex Exception to be handled.
     */
    public static function handleException(Exception $ex);
}