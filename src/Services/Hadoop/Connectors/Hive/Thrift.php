<?php

// @see https://packagist.org/packages/automattic/php-thrift-sql

namespace Silverd\LaravelHive\Services\Hadoop\Connectors\Hive;

use Silverd\LaravelHive\Services\Hadoop\Connectors\DbAbstract;

class Thrift extends DbAbstract
{
    protected $dbName;

    // 建立数据库链接
    public function connect()
    {
        if (isset($this->connections[$this->dbName])) {
            return $this->connections[$this->dbName];
        }

        $conn = new \ThriftSQL\Hive($this->host, $this->port, $this->username, $this->password, $this->timeout ?: 86400);

        // 切换数据库
        $conn->connect()->query('USE `' . $this->dbName . '`');

        return $this->connections[$this->dbName] = $conn;
    }

    // 关闭数库函数
    public function close()
    {
        foreach ($this->connections as $dbName => $connect) {
            $connect->disconnect();
        }
    }

    public function fetchRow(string $sql)
    {
        return $this->fetchAll()[0] ?? [];
    }

    // 执行SQL语句函数
    public function fetchAll(string $sql)
    {
        // If the result set is small and it would be easier to load all of it into PHP memory
        // the queryAndFetchAll() method can be used which will
        // return a plain numeric multidimensional array of the full result set.
        return $this->connect()->connect()->queryAndFetchAll($sql);
    }
}
