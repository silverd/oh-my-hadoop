<?php

namespace Silverd\LaravelHive\Services\Hadoop\Connectors;

abstract class DbAbstract
{
    protected $config = [];

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

    // 建立数据库链接
    abstract public function connect();

    // 关闭数库函数
    abstract public function close();

    // 执行SQL语句函数
    abstract public function fetchAll(string $sql);

    // 设置数据库
    public function selectDb(string $dbName)
    {
        $this->dbName = $dbName;

        $this->connect();

        return $this;
    }
}
