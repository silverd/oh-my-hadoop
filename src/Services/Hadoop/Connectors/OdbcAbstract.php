<?php

// @see https://www.cdata.com/kb/tech/hive-odbc-php.rst

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors;

abstract class OdbcAbstract extends DbAbstract
{
    protected $dbName;

    public function connect()
    {
        if (isset($this->connections[$this->dbName])) {
            return $this->connections[$this->dbName];
        }

        $dsns = [
            'dsn'           => $this->config['dsn'] ?? '',
            'HOST'          => $this->config['host'] ?? '',
            'PORT'          => $this->config['port'] ?? '',
            'SocketTimeout' => $this->config['timeout'] ?? 0,
            'AuthMech'      => $this->config['authMech'] ?? 0,
            'Schema'        => $this->dbName,
        ];

        if (! $dsns['dsn'] || ! $dsns['HOST']) {
            throw new \Exception('ODBC DSN 和 HOST 必填');
        }

        $dsnStr = $this->buildDsnStr($dsns + $this->getDsnStrs());

        $this->connections[$this->dbName] = odbc_connect(
            $dsnStr,
            $this->config['username'] ?? '',
            $this->config['password'] ?? '',
        );

        return $this->connections[$this->dbName];
    }

    abstract function getDsnStrs();

    public function close()
    {
        foreach ($this->connections as $dbName => $connect) {
            odbc_close($connect);
        }
    }

    public function fetchRow(string $sql, array $params = [])
    {
        $stmt = $this->execute($sql, $params);

        return odbc_fetch_array($stmt);
    }

    public function fetchAll(string $sql, array $params = [])
    {
        $data = [];

        $stmt = $this->execute($sql, $params);

        while ($row = odbc_fetch_array($stmt)) {
            $data[] = $row;
        }

        return $data;
    }

    public function execute(string $sql, array $params = [])
    {
        \Log::channel('hadoop_odbc')->info($sql, $params);

        $stmt = odbc_prepare($this->connect(), $sql);

        odbc_execute($stmt, $params);

        return $stmt;
    }

    // @see https://stackoverflow.com/questions/13503223/odbc-exec-vs-odbc-excute
    public function exec(string $sql)
    {
        \Log::channel('hadoop_odbc')->info($sql);

        return odbc_exec($this->connect(), $sql);
    }
}
