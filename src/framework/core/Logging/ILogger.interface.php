<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/18/2016
 * Time: 3:01 AM
 */

namespace Core\Logging;

use \Exception;

interface ILogger
{
    /**
     * Log error to log file.
     *
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public function logError($text);

    /**
     * Log Info to log file.
     *
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public function logInfo($text);

    /**
     * Log Warning to log file.
     *
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public function logWarning($text);

    /**
     * Log Debug entry to log file.
     *
     * @throws Exception If logger is not setted up.
     * @param string $text Text to log.
     */
    public  function logDebug($text);
}