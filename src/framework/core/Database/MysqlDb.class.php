<?php

namespace Framework\Core\Database;

use \PDO;

class MysqlDb extends DB
{
    public function connect($host, $db, $user, $pass, array $attributes)
    {
        return new PDO("mysql:host=$host; dbname=$db;charset=utf8", $user, $pass, $attributes);
    }
}