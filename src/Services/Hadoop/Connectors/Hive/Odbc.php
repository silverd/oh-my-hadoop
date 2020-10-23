<?php

namespace Silverd\LaravelHive\Services\Hadoop\Connectors\Hive;

use Silverd\LaravelHive\Services\Hadoop\Connectors\OdbcAbstract;

class Odbc extends OdbcAbstract
{
    public function connect()
    {
        $conn = parent::connect();

        // @see https://stackoverflow.com/questions/43112868/avoid-printing-table-name-in-column-name-while-using-beeline
        // @see https://cwiki.apache.org/confluence/display/Hive/Configuration+Properties
        odbc_exec($conn, 'SET hive.resultset.use.unique.column.names=false');

        return $conn;
    }
}
