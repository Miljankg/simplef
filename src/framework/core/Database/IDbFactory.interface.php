<?php

namespace Framework\Core\Database;

interface IDbFactory
{
    /**
     * Returns database instance.
     *
     * @param $dbIndex
     * @return IDB
     */
    public function GetDbInstance($dbIndex);
}