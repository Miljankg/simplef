<?php

namespace Core\Database;

use \Exception;
use \PDO;
use \PDOException;

class DB
{
    public $dbh = null;
    private $dbhs = array();

    private $queryTypes     = array('table', 'scalar', 'non-select');
    private $connTypes      = array('oracle', 'mysql');

    public function __construct(array $configData) 
    {		
        $dbCredFields = array(
            'db_user',
            'db_name',
            'db_host',
            'db_pass',
            'db_type'
        );
        
        $dbCredVals = array();
        
        foreach ($configData as $dbIndex => $dbConfigData) {
            
            foreach ($dbCredFields as $dbCredField) {
                
                if (!isset($dbConfigData[$dbCredField])) {
                    
                    throw new Exception("Database: $dbIndex => There is no field: $dbCredField");
                    
                }
                
                $dbCredVals[$dbIndex][$dbCredField] = $dbConfigData[$dbCredField];
                
            }
            
        }

        foreach ($dbCredVals as $dbIndex => $dbParams)
        {
            $hndl = $this->connect(
                        $dbParams['db_host'], 
                        $dbParams['db_name'], 
                        $dbParams['db_user'], 
                        $dbParams['db_pass'], 
                        $dbParams['db_type']
                    );                        
            
            $this->dbhs[$dbIndex] = $hndl;
        }        				
    }

    public function connect($host, $db, $user, $pass, $type)
    {
        if (!in_array($type, $this->connTypes))
        {
            throw new Exception("Invalid DB connection type $type.");
        }
        
        try
        {
            $dbh = null;
            
            switch ($type)
            {
                case 'oracle':
                    $dbh = $this->ConnectToOracle($host, $db, $user, $pass);
                    break;
                case 'mysql':
                    $dbh = $this->ConnectToMysql($host, $db, $user, $pass);
                    break;
            }            

            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            print "Database connection failed! Error: " . $e->getMessage() . "<br/>";
            exit;
        }
        
        return $dbh;
    }   
    
    private function ConnectToMysql($host, $db, $user, $pass)
    { 
        return new PDO("mysql:host=$host; dbname=$db;charset=utf8", $user, $pass);
    }
    
    private function ConnectToOracle($server, $dbName, $user, $pass)
    {
        $db = "oci:dbname=//$server/$dbName;charset=AL32UTF8"; //charset=AL32UTF8 for utf-8 characters from DB.

        return new PDO($db,$user,$pass);        
    }

    private function GetDbInstance($dbIndex) {                
        
        if ($dbIndex !== null) {
            
            if (isset($this->dbhs[$dbIndex])) {
                
                $db = $this->dbhs[$dbIndex];
                
            } else {
                
                throw new Exception("No $dbIndex instance defined.");
                
            }
        } else {
            
            throw new Exception("DB Index cannot be null.");
            
        }
        
        return $db;
    }

    public function ExecuteQry($query, $dbIndex = null)
    {
        $db = $this->GetDbInstance($dbIndex);
        
        try
        {
            $handler = $db->prepare($query);            

            $handler->execute();
        }
        catch (Exception $e)
        {
            throw new Exception("Error while executing: [ $query ]. Error: [ " . $e->getMessage() . " ]");
        }

        return $handler;
    }    
    
    public function ExecuteDirectQuery($query, $type, $dbIndex = null)
    {
        if (empty($query))
        {
            throw new Exception("Invalid direct query: $query");
        }
        
        if (!in_array($type, $this->queryTypes))
        {
            throw new Exception("Invalid query type [ $type ]");
        }
        
        $handler = $this->ExecuteQry($query, $dbIndex);
        
        switch ($type)
        {
            case 'table':
                return $handler->fetchAll(PDO::FETCH_ASSOC);
            case 'scalar':
                return $handler->fetch(PDO::FETCH_COLUMN);
            default:
                break;
        }                
    }
}