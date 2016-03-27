<?php

namespace Framework\Core\Database;

use \Exception;
use \PDO;
use \PDOException;

abstract class DB implements IDB
{
    //<editor-fold desc="Members">

    private $dbh = null;

    //</editor-fold>

    //<editor-fold desc="Constructors">

    public function __construct($host, $db, $user, $pass, array $attributes)
    {
        try
        {
            $this->dbh = $this->connect($host, $db, $user, $pass, $attributes);

            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            print "Database connection failed! Error: " . $e->getMessage() . "<br/>";
            exit;
        }
    }

    //</editor-fold>

    //<editor-fold desc="Internal functions">

    private function ExecuteQry($query)
    {
        if (empty($query))
        {
            throw new Exception("Invalid direct query: $query");
        }
        
        try
        {
            $handler = $this->dbh->prepare($query);

            $handler->execute();
        }
        catch (Exception $e)
        {
            throw new Exception("Error while executing: [ $query ]. Error: [ " . $e->getMessage() . " ]");
        }

        return $handler;
    }

    //</editor-fold>

    //<editor-fold desc="IDB functions">

    protected abstract function connect($host, $db, $user, $pass, array $attributes);

    public function ExecuteTableQuery($query)
    {
        $handler = $this->ExecuteQry($query);

        return $handler->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ExecuteScalarQuery($query)
    {
        $handler = $this->ExecuteQry($query);

        return $handler->fetch(PDO::FETCH_COLUMN);
    }

    public function ExecuteNonQuery($query)
    {
        $this->ExecuteQry($query);
    }

    //</editor-fold>
}