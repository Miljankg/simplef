<?php

namespace Framework\Core\Database;

use \PDO;

class OracleDb extends DB
{
    public function connect($host, $db, $user, $pass, array $attributes)
    {
        return new PDO("oci:dbname=//$host/$db;charset=AL32UTF8",$user,$pass, $attributes);
    }
}