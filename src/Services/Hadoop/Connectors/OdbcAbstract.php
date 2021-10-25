<?php

// @see https://www.cdata.com/kb/tech/hive-odbc-php.rst

namespace Silverd\LaravelHive\Services\Hadoop\Connectors;

class OdbcAbstract extends DbAbstract
{
    protected $dbName;

    public function connect()
    {
        if (isset($this->connections[$this->dbName])) {
            return $this->connections[$this->dbName];
        }

        $host = "dsn=$this->dsn;host=$this->host;port=$this->port;schema=$this->dbName;sockettimeout=$this->timeout";

        $this->connections[$this->dbName] = odbc_connect($host, $this->username, $this->password);

        return $this->connections[$this->dbName];
    }

    public function close()
    {
        foreach ($this->connections as $dbName => $connect) {
            odbc_close($connect);
        }
    }

    public function fetchRow(string $sql)
    {
        $result = odbc_exec($this->connect(), $sql);

        return odbc_fetch_array($result);
    }

    public function fetchAll(string $sql)
    {
        $data = [];

        $result = odbc_exec($this->connect(), $sql);

        while ($row = odbc_fetch_array($result)) {
            $data[] = $row;
        }

        return $data;
    }

    public function execute(string $sql, array $params = [])
    {
        $stmt = odbc_prepare($this->connect(), $sql);

        return odbc_execute($stmt, $params);
    }

    // @see https://stackoverflow.com/questions/13503223/odbc-exec-vs-odbc-excute
    public function exec(string $sql)
    {
        return odbc_exec($this->connect(), $sql);
    }
    
    // 执行服务端命令
    public function execCommand(string $command)
    {
        odbc_exec($this->connect(), $command);

        return $this;
    }
}
