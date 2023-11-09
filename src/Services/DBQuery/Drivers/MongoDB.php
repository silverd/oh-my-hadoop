<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

class MongoDB extends AbstractDriver
{
    public function validate()
    {
        \Validator::make($this->config, [
            'host'     => 'required|string',
            'port'     => 'required|int',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
        ])->validate();

        $this->databases();
    }

    public function connection(string $db = '')
    {
        $connName = md5(json_encode($this->config));

        $config = [
            'driver'   => 'mongodb',
            'host'     => $this->config['host'],
            'port'     => $this->config['port'],
            'username' => $this->config['username'] ?? null,
            'password' => $this->config['password'] ?? null,
            'database' => $db,
            'options' => [
                'appname' => 'mongo',
                'authSource' => $this->config['auth_source'] ?? 'admin',
            ],
        ];

        \Config::set('database.connections.' . $connName, $config);

        return \DB::connection($connName);
    }

    public function query(string $db, string $sql)
    {
        return $this->connection($db)->statement($sql);
    }

    public function select(string $db, string $table)
    {
        $result = $this->connection($db)->collection($table)->get();

        $return = [];

        foreach ($result as $valueObj) {
            $return[] = (array) $valueObj;
        }

        return $return;
    }

    public function databases()
    {
        $dbs = $this->connection('default')->getMongoClient()->listDatabases();

        $res = [];

        foreach ($dbs as $db) {
            $res[] = $db->__debugInfo();
        }

        return array_column($res, 'name');
    }

    public function tables(string $db)
    {
        $tables = $this->connection($db)->getMongoDB()->listCollections();

        $res = [];

        foreach ($tables as $table) {
            $res[] = $table->__debugInfo();
        }

        return array_column($res, 'name');
    }

    public function fields(array $reader)
    {
        // 取第一条的结构
        $result = $this->connection($reader['database'])
            ->collection($reader['table'])
            ->orderBy('_id', 'ASC')
            ->first();

        $includeId = $reader['include_id'] ?? true;

        $fields = [];

        foreach ($result ?? [] as $k => $v) {

            // 不返回 mongo 自有 id 字段（因为不是 int）
            if ( ! $includeId && $k == '_id') {
                continue;
            }

            $fields[] = [
                'name'  => $k,
                'type'  => gettype($v),
                'is_pk' => $k == '_id'
            ];
        }

        return $fields;
    }

    public function createDatabase(string $db)
    {
        // do nothing
    }

    // 获取数据表大小
    public function capacity(array $reader)
    {
        $result = $this->connection($reader['database'])
            ->getMongoDB()
            ->command([
                'collStats' => $reader['table'],
                'scale'     => 1,
            ]);

        $size = 0;
        if ($result) {
            $result = $result->toArray()[0];
            $size = isset($result->totalSize) ? $result->totalSize : (($result->storageSize ?? 0) + ($result->totalIndexSize ?? 0));
        }

        return $size;
    }

    public function count(array $reader)
    {
        $data = $this->select($reader['database'], "SELECT COUNT(1) AS cnt FROM `{$reader['database']}`.`{$reader['table']}`");

        return (int) $data[0]['cnt'];
    }
}
