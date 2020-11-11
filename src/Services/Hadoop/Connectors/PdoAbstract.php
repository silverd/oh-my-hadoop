<?php

namespace Silverd\LaravelHive\Services\Hadoop\Connectors;

class PdoAbstract extends DbAbstract
{
    protected $dbName;

    public function connect()
    {
        if (isset($this->connections[$this->dbName])) {
            return $this->connections[$this->dbName];
        }

        $host = "odbc=$this->dsn;host=$this->host;port=$this->port;schema=$this->dbName";

        $this->connections[$this->dbName] = new \PDO($host, $this->username, $this->password);

        return $this->connections[$this->dbName];
    }

    public function close()
    {
        foreach ($this->connections as $dbName => $conect) {
            unset($connect);
        }
    }

    public function fetchRow(string $sql, array $params = [])
    {
        $stmt = $this->connect()->prepare($sql);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAll(string $sql, array $params = [])
    {
        $stmt = $this->connect()->prepare($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
