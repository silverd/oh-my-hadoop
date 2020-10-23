<?php

namespace Silverd\LaravelHive\Services\Hadoop\Connectors;

abstract class DbAbstract
{
    protected $dsn;
    protected $host;
    protected $port;
    protected $username;
    protected $timeout;

    protected $dbName = 'default';

    protected $connections = [];

    public function __construct(array $config)
    {
        $this->dsn      = $config['dsn'];
        $this->host     = $config['host'];
        $this->port     = $config['port'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->timeout  = $config['timeout'] ?? 0;
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
