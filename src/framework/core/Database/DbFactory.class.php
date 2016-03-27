<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 3/26/2016
 * Time: 9:45 PM
 */

namespace Framework\Core\Database;

use \PDO;
use \Exception;

class DbFactory implements IDbFactory
{
    //<editor-fold desc="Members">

    private $dbInstances = array();

    private $connTypes = array('oracle', 'mysql');

    //</editor-fold>

    //<editor-fold desc="Constructions">

    public function __construct(array $configData)
    {
        $dbCredFields = array(
            'db_user',
            'db_name',
            'db_host',
            'db_pass',
            'db_type'
        );

        $dbCredValues = array();

        foreach ($configData as $dbIndex => $dbConfigData)
        {
            if (!in_array($dbConfigData['db_type'], $this->connTypes))
            {
                throw new Exception("Invalid DB connection type {$dbConfigData['db_type']} for DB $dbIndex.");
            }

            foreach ($dbCredFields as $dbCredField)
            {
                if (!isset($dbConfigData[$dbCredField]))
                {
                    throw new Exception("Database: $dbIndex => There is no field: $dbCredField");
                }

                $dbCredValues[$dbIndex][$dbCredField] = $dbConfigData[$dbCredField];
            }
        }

        foreach ($dbCredValues as $dbIndex => $dbParams)
        {
            $connectionParameters = array();

            if (isset($dbParams['persistent_connection']) &&
                $dbParams['persistent_connection'] == true)
                $connectionParameters[PDO::ATTR_PERSISTENT] = true;

            $dbInstance = null;

            switch ($dbParams['db_type'])
            {
                case 'oracle':
                    $dbInstance = new OracleDb($dbParams['db_host'], $dbParams['db_name'], $dbParams['db_user'], $dbParams['db_pass'], $connectionParameters);
                    break;
                case 'mysql':
                    $dbInstance = new MysqlDb($dbParams['db_host'], $dbParams['db_name'], $dbParams['db_user'], $dbParams['db_pass'], $connectionParameters);
                    break;
                default:
                    throw new \Exception("Invalid db type {$dbParams['db_type']}.");
                    break;
            }

            $this->dbInstances[$dbIndex] = $dbInstance;
        }
    }

    //</editor-fold>

    //<editor-fold desc="IDbFactory functions">

    /**
     * Returns db instance by connection name.
     *
     * @param string $dbIndex Name of the db to return.
     * @return IDB
     * @throws Exception If requested connection does not exists.
     */
    public function GetDbInstance($dbIndex)
    {
        if ($dbIndex !== null)
        {
            if (isset($this->dbInstances[$dbIndex]))
            {
                $db = $this->dbInstances[$dbIndex];
            }
            else
            {
                throw new Exception("No $dbIndex instance defined.");
            }
        }
        else
        {
            throw new Exception("DB Index cannot be null.");
        }

        return $db;
    }

    //</editor-fold>
}