<?php

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors;

abstract class DbAbstract
{
    protected $config;
    protected $dbName = 'default';
    protected $connections = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function __destruct()
    {
        $this->close();
    }

    abstract public function connect();

    abstract public function close();

    abstract public function fetchRow(string $sql, array $params = []);

    abstract public function fetchAll(string $sql, array $params = []);

    public function selectDb(string $dbName)
    {
        $this->dbName = $dbName;

        $this->connect();

        return $this;
    }

    public function buildDsnStr(array $dsnStrs)
    {
        $dsnStrs = array_filter($dsnStrs, function ($value) {
            return ! is_null($value);
        });

        $dsnStr = '';

        foreach ($dsnStrs as $key => $value) {
            $dsnStr .= $key . '=' . $value . ';';
        }

        return $dsnStr;
    }
}
