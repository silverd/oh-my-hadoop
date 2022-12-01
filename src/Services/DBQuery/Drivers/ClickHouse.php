<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

class ClickHouse extends AbstractDriver
{
    protected $driver = 'clickhouse';

    const TABLE_ENGINES = [
        'MergeTree',
        'ReplacingMergeTree',
        'SummingMergeTree',
        'AggregatingMergeTree',
        'CollapsingMergeTree',
        'VersionedCollapsingMergeTree',
        'GraphiteMergeTree',
        'TinyLog',
        'StripeLog',
        'Log',
        'Kafka',
        'MySQL',
        'ODBC',
        'JDBC',
        'HDFS',
        'Distributed',
        'MaterializedView',
        'Dictionary',
        'Merge',
        'File',
        'Null',
        'Set',
        'Join',
        'URL',
        'View',
        'Memory',
        'Buffer'
    ];

    public function __construct(array $config)
    {
        if (isset($config['driver'])) {
            $this->driver = $config['driver'];
            unset($config['driver']);
        }

        parent::__construct($config);
    }

    public function validate()
    {
        \Validator::make($this->config, [
            'host'            => 'required|string',
            'port'            => 'required|int',
            'username'        => 'nullable|string',
            'password'        => 'nullable|string',
            'tcp_port'        => 'nullable|int',
            'cluster_name'    => 'nullable|string',
            'timeout_query'   => 'required|int',
            'timeout_connect' => 'required|int'
        ])->validate();

        $this->databases();
    }

    // 返回是否为ck集群连接
    protected function isCluster(): bool
    {
        return $this->driver == 'cluster_clickhouse';
    }

    public function connection(string $db = '')
    {
        $connName = md5(json_encode($this->config));

        $config = [
            'host'            => $this->config['host'],
            'port'            => $this->config['port'],
            'username'        => $this->config['username'] ?? '',
            'password'        => $this->config['password'] ?? '',
            'timeout_query'   => (int) $this->config['timeout_query'],
            'timeout_connect' => (int) $this->config['timeout_connect'],
            'tcp_port'        => $this->config['tcp_port'] ?? '',
            'cluster_name'    => $this->config['cluster_name'] ?? '',
            'database'        => $db,
            'driver'          => $this->driver,
        ];

        \Config::set('database.connections.' . $connName, $config);

        return $this->isCluster() ? \CkCluster::getClusterByKey($connName) : \DB::connection($connName);
    }

    public function query(string $db, string $sql)
    {
        $connection = $this->connection();

        return $this->isCluster() ? $connection->activeClient()->write($sql) : $connection->getClient()->write($sql);
    }

    public function select(string $db, string $sql)
    {
        $connection = $this->connection();

        $result = $this->isCluster() ? $connection->activeClient()->select($sql)->rows() : $connection->getClient()->select($sql)->rows();

        $return = [];

        foreach ($result as $valueObj) {
            $return[] = (array) $valueObj;
        }

        return $return;
    }

    public function databases()
    {
        $dbs = $this->select('', 'SHOW DATABASES');

        return \Arr::pluck($dbs, 'name');
    }

    public function tables(string $db)
    {
        $tables = $this->select($db, 'SHOW TABLES');

        return \Arr::pluck($tables, 'name');
    }

    public function fields(array $reader)
    {
        $result = $this->select($reader['database'], 'DESCRIBE ' . $reader['database'] . '.' . $reader['database']);

        $fields = [];

        foreach ($result as $field) {
            $fields[] = [
                'name'    => $field['name'],
                'type'    => $field['type'],
                'comment' => $field['comment'],
                'extra'   => '',
            ];
        }

        return $fields;
    }

    public function createDatabase(string $db)
    {
        $this->query('', "CREATE DATABASE IF NOT EXISTS {$db}");
    }

    public function capacity(array $reader)
    {
        $sql = <<<SQL
            SELECT
                `database`,
                `table`,
                sum(data_uncompressed_bytes) AS `bytes_size`
            FROM `system`.`parts`
            WHERE `active`=1 AND `database` = '{$reader['database']}' AND `table` = '{$reader['table']}'
            GROUP BY
                `database`,
                `table`
            ORDER BY `bytes_size` DESC
        SQL;

        $data = $this->select($reader['database'], $sql);

        return $data[0]['bytes_size'] ?? 0;
    }

    public function count(array $reader)
    {
        $data = $this->select($reader['database'], "SELECT COUNT() AS cnt FROM `{$reader['database']}`.`{$reader['table']}`");

        return (int) $data[0]['cnt'];
    }

}
