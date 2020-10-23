<?php

namespace Silverd\LaravelHive\Services\Hadoop\Connectors\Presto;

use Clouding\Presto\Presto;
use Silverd\LaravelHive\Services\Hadoop\Connectors\DbAbstract;

/**
 * presto查询引擎
 *
 * @see https://packagist.org/packages/clouding/presto-client-php
 */
class Http extends DbAbstract
{
    protected $dbName;

    public function __destruct()
    {
        $this->close();
    }

    // 建立数据库链接
    public function connect()
    {
        if (isset($this->connections[$this->dbName])) {
            return $this->connections[$this->dbName];
        }

        $presto = new Presto();

        $presto->addConnection([
            'host'    => $this->host . ':' . $this->port,
            'catalog' => $this->username,
            'schema'  => $this->dbName,
        ], $this->dbName);

        $this->connections[$this->dbName] = $presto->connection($this->dbName);

        return $this->connections[$this->dbName];
    }

    // 关闭数库函数
    public function close()
    {
        $this->connections = [];
    }

    public function fetchRow(string $sql)
    {
        return $this->fetchAll()[0] ?? [];
    }

    public function fetchAll(string $sql)
    {
        return $this->connect()->query($sql)->getAssoc();
    }
}
