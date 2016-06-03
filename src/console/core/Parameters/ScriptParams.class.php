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
    private $scriptParameters = array();

    public function __construct(array $arguments, $consoleConfig)
    {
        $scriptParameters = $this->parseScriptParameters($arguments);

        if (!isset($scriptParameters['object']))
            throw new Exception('No object specified');

        if (!isset($scriptParameters['action']))
            throw new Exception('No action specified');

        $this->action = $scriptParameters['object'];
        $this->value = $scriptParameters['action'];

        $expectedParameters = array();

        if (isset($consoleConfig[$this->action]['allowed_values'][$this->value]))
            $expectedParameters = $consoleConfig[$this->action]['allowed_values'][$this->value];

        foreach ($expectedParameters as $parameter)
        {
            if (!isset($scriptParameters[$parameter]))
            {
                if (array_key_exists('do-not-ask', $scriptParameters))
                    throw new Exception("Parameter $parameter is not set.");
            }
        }

        $this->scriptParameters = $scriptParameters;
    }

    public function parseScriptParameters(array $argv)
    {
        $previousKey = null;

        $parsedParameters = array();

        foreach ($argv as $key => $value)
        {
            if ($key < 1)
                continue;

            if (substr( $value, 0, 2 ) === "--")
            {
                $previousKey = substr( $value, 2, strlen($value) );

                $parsedParameters[$previousKey] = "";
            }
            else
            {
                $parsedParameters[$previousKey] = $value;
            }
        }

        return $parsedParameters;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function askForUserInput($question, $allowedAnswers = array(), $paramToCheck = "")
    {
        if ($paramToCheck !== "")
        {
            if (isset($this->scriptParameters[$paramToCheck]))
                return $this->scriptParameters[$paramToCheck];
        }

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

    public function askYesNo($question)
    {
        if (array_key_exists('all-yes', $this->scriptParameters))
        {
            return 'yes';
        }

        return $this->askForUserInput($question, array('yes', 'no'));
    }
}