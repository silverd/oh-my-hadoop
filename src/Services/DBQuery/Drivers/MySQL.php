<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

class MySQL extends AbstractDriver
{
    public function validate()
    {
        \Validator::make($this->config, [
            'host'     => 'required|string',
            'port'     => 'required|int',
            'username' => 'required|string',
            'password' => 'required|string',
        ])->validate();

        $this->databases();
    }

    public function connection(string $db = '')
    {
        $connName = md5(json_encode($this->config));

        $config = [
            'host'      => $this->config['host'],
            'port'      => $this->config['port'],
            'username'  => $this->config['username'],
            'password'  => $this->config['password'],
            'database'  => $db,
            'driver'    => 'mysql',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                // 默认开启模拟 SQL 预处理
                \PDO::ATTR_EMULATE_PREPARES => true,
            ],
        ];

        \Config::set('database.connections.' . $connName, $config);

        return \DB::connection($connName);
    }

    public function query(string $db, string $sql)
    {
        return $this->connection($db)->statement($sql);
    }

    public function select(string $db, string $sql)
    {
        $result = $this->connection($db)->select($sql);

        $return = [];

        foreach ($result as $valueObj) {
            $return[] = (array) $valueObj;
        }

        return $return;
    }

    // 库列表
    public function databases()
    {
        $dbs = $this->select('', 'SHOW DATABASES');

        return \Arr::pluck($dbs, 'Database');
    }

    // 表列表
    public function tables(string $db)
    {
        $tables = [];

        $result = $this->select($db, 'SHOW TABLES');

        foreach ($result as $table) {
            $tables[] = current($table);
        }

        return $tables;
    }

    // 字段列表
    public function fields(array $reader)
    {
        $sql = <<<SQL
            SELECT * FROM `information_schema`.`columns`
            WHERE table_schema = '{$reader['database']}' AND table_name = '{$reader['table']}'
        SQL;

        $result = self::select('information_schema', $sql);

        $fields = [];

        foreach ($result as $field) {
            $fields[] = [
                'name'        => $field['COLUMN_NAME'],
                'type'        => $field['DATA_TYPE'],
                'sort'        => $field['ORDINAL_POSITION'],
                'length'      => $field['NUMERIC_PRECISION'] ?: $field['CHARACTER_MAXIMUM_LENGTH'] ?: 0,
                'precision'   => $field['NUMERIC_SCALE'] ?: 0,
                'is_nullable' => $field['IS_NULLABLE'] == 'YES' ? 1 : 0,
                'is_pk'       => $field['COLUMN_KEY'] ? 1 : 0,
                'comment'     => $field['COLUMN_COMMENT'],
                'extra'       => $field['EXTRA'],
            ];
        }

        return $fields;
    }

    // 建库
    public function createDatabase(string $database)
    {
        $this->query('', 'CREATE DATABASE IF NOT EXISTS ' . $database);
    }

    // 容量
    public function capacity(array $reader)
    {
        $sql = <<<SQL
            SELECT
                (`data_length` + `index_length`) AS 'bytes_size'
            FROM information_schema.TABLES
            WHERE `TABLE_SCHEMA` = '{$reader['database']}' AND `TABLE_NAME` = '{$reader['table']}'
        SQL;

        $data = $this->select($reader['database'], $sql);

        return $data[0]['bytes_size'] ?? 0;
    }

    public function count(array $reader)
    {
        $data = $this->select($reader['database'], "SELECT COUNT(1) AS cnt FROM `{$reader['database']}`.`{$reader['table']}`");

        return (int) $data[0]['cnt'];
    }
}
