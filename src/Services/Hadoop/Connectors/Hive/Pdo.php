<?php

namespace Silverd\LaravelHive\Services\Hadoop\Connectors\Hive;

use Silverd\LaravelHive\Services\Hadoop\Connectors\PdoAbstract;

class Pdo extends PdoAbstract
{
    public function connect()
    {
        $conn = parent::connect();

        // @see https://stackoverflow.com/questions/43112868/avoid-printing-table-name-in-column-name-while-using-beeline
        // @see https://cwiki.apache.org/confluence/display/Hive/Configuration+Properties
        $conn->query('SET hive.resultset.use.unique.column.names=false');

        return $this->connections[$this->dbName];
    }
}
