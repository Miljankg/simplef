<?php

namespace Console\Core\Action;

use Console\Core\Config\IConfig;
use Console\Core\Parameters\ScriptParams\ScriptParams;

class DbConnectionOperation extends Operation
{
    private $databaseConnectionFields;

    protected function init($name, $value, $index, IConfig $config, array $allowedValues, array $consoleConfig, ScriptParams $scriptParams)
    {
        parent::init($name, $value, $index, $config, $allowedValues, $consoleConfig, $scriptParams);

        $this->databaseConnectionFields = $this->consoleConfig['db_fields'];
    }

    /**
     * Should display the value if preview_value is passed.
     *
     * @return mixed
     */
    protected function previewValue()
    {
        if ($this->previewValueValue != $this->value)
            return false;

        $dbConnections = $this->config->get('db_config');

        if (empty($dbConnections))
            return "No DB Connections defined.";

        $question = "Enter db connection name to filter the output or press enter for complete list: ";

        $answer = $this->scriptParams->askForUserInput($question);

        $dbConnections = $this->config->get('db_config');

        if (empty($dbConnections))
            return "No DB Connections defined.";

        if (!isset($dbConnections[$answer]))
            throw new \Exception("There is not db connection named \"$answer\".");

        $outputArray = array();

        if (!empty($answer))
        {
            $outputArray[$answer] = $dbConnections[$answer];
        }
        else
        {
            $outputArray = $dbConnections;
        }



        $str = "";

        foreach ($outputArray as $dbConnectionName => $dbConnectionConfig)
        {
            if (!empty($str))
            {
                $str .= "\n";
            }

            $str .= "Connection: $dbConnectionName\n\n";

            foreach ($this->databaseConnectionFields as $field)
            {
                if (isset($dbConnectionConfig[$field]))
                    $str .= "=> $field = {$dbConnectionConfig[$field]}\n";
            }
        }

        return $str;
    }

    private function addDbConnection($dbConnectionName)
    {
        $dbConnections = $this->config->get('db_config');

        if (isset($dbConnections[$dbConnectionName]))
            throw new \Exception("Db connection \"$dbConnections\" already exists.");

        $dbConnections[$dbConnectionName] = array();

        foreach ($this->databaseConnectionFields as $field)
        {
            $question = "Enter $field: ";

            $answer = $this->scriptParams->askForUserInput($question);

            $dbConnections[$dbConnectionName][$field] = $answer;
        }

        $this->config->set('db_config', $dbConnections);

        define('DB_' . strtoupper($dbConnectionName), $dbConnectionName);

        return "Db connection \"$dbConnectionName\" successfully added.";
    }

    private function removeDbConnection($dbConnectionName)
    {
        $dbConnections = $this->config->get('db_config');

        if (!isset($dbConnections[$dbConnectionName]))
            throw new \Exception("Db connection \"$dbConnections\" does not exists.");

        $areYouSure = "Are you sure that you want to remove db connection \"$dbConnectionName\" (yes|no)?";

        $sure = $this->scriptParams->askForUserInput($areYouSure, array('yes', 'no'));

        if ($sure == 'no')
            return "Giving up on removing db connection.";

        $dbConnections[$dbConnectionName] = array();

        unset($dbConnections[$dbConnectionName]);

        $this->config->set('db_config', $dbConnections);

        $constant = 'DB_' . strtoupper($dbConnectionName);

        if (defined($constant))
            $this->config->queueConstantForRemoval($constant);

        return "Db connection \"$dbConnectionName\" successfully removed.";
    }

    private function updateDbConnection($dbConnectionName)
    {
        $dbConnections = $this->config->get('db_config');

        if (!isset($dbConnections[$dbConnectionName]))
            throw new \Exception("Db connection \"$dbConnections\" does not exists.");

        $fieldsStr = $this->arrToStr($this->databaseConnectionFields);

        $question = "Enter field that you want to update ($fieldsStr): ";

        $fieldToUpdate = $this->scriptParams->askForUserInput($question, $this->databaseConnectionFields);

        $question = "Enter the value of the field \"$fieldToUpdate\" to update to: ";

        $value = $this->scriptParams->askForUserInput($question);

        $dbConnections[$dbConnectionName][$fieldToUpdate] = $value;

        $this->config->set('db_config', $dbConnections);

        return "Db connection \"$dbConnectionName\" updated to $fieldToUpdate => $value.";
    }

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     */
    public function perform()
    {
        $previewValue = $this->previewValue();

        if ($previewValue !== false)
            return $previewValue;

        $output = "";

        $dbConnectionName = $this->scriptParams->askForUserInput("Enter connection name: ");

        if (empty($dbConnectionName))
            throw new \Exception("Connection name cannot be empty");

        switch ($this->value)
        {
            case 'add':
                $output = $this->addDbConnection($dbConnectionName);
                break;
            case 'remove':
                $output = $this->removeDbConnection($dbConnectionName);
                break;
            case 'update':
                $output = $this->updateDbConnection($dbConnectionName);
                break;
        }

        return $output;
    }
}