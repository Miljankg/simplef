<?php

namespace Framework\Core\Database;

interface IDB
{
    /**
     * Executes queries that returns table results.
     *
     * @param $query
     * @return mixed
     */
    function ExecuteTableQuery($query);

    /**
     * Executes queries that returns scalar results.
     *
     * @param $query
     * @return mixed
     */
    function ExecuteScalarQuery($query);

    /**
     * Executes queries that returns no results.
     *
     * @param $query
     * @return mixed
     */
    function ExecuteNonQuery($query);

}