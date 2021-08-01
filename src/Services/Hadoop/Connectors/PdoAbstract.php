<?php

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors;

abstract class PdoAbstract extends DbAbstract
{
    protected $dbName;

    public function connect()
    {
        if (isset($this->connections[$this->dbName])) {
            return $this->connections[$this->dbName];
        }

        $dsns = [
            'odbc=' . $this->config['dsn'],
            'host=' . $this->config['host'],
            'port=' . $this->config['port'],
            'schema=' . $this->dbName,
        ];

        $this->connections[$this->dbName] = new \PDO(implode(';', $dsns), $this->config['username'], $this->config['password']);

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
