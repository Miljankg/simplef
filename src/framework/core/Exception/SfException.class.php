<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/18/2016
 * Time: 4:55 AM
 */

namespace Core\Exception;

use \Exception;


class SfException extends Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $message = "SF ERROR => [ " . $message . " ] ";

        parent::__construct($message, $code, $previous);
    }
}