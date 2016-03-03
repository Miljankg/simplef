<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/24/2016
 * Time: 12:07 AM
 */

namespace Console\Core\Parameters\ScriptParams;

use \Exception;

class ScriptParams
{
    private $action;
    private $value;

    public function __construct(array $arguments)
    {
        if (!isset($arguments[1]))
            throw new Exception('No action specified');

        if (!isset($arguments[2]))
            throw new Exception('No value specified');

        $this->action = $arguments[1];
        $this->value = $arguments[2];
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function askForUserInput($question, $allowedAnswers = array())
    {
        $stop = false;
        $count = 0;
        $maxTries = 3;

        while (!$stop && $count < $maxTries)
        {
            $count++;

            echo $question . ' ';
            $handle = fopen ("php://stdin","r");
            $line = fgets($handle);
            fclose($handle);
            echo PHP_EOL;

            $line = trim($line, " \t\n\r\0\x0B");

            if (empty($allowedAnswers) || in_array($line, $allowedAnswers))
                $stop = true;
            else
                echo 'Please enter one of these answers: ' . implode(', ', $allowedAnswers) . PHP_EOL . PHP_EOL;
        }

        if ($maxTries == $count)
            throw new Exception('To many wrong answers.');

        return $line;
    }
}