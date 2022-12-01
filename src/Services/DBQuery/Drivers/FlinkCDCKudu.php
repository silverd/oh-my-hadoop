<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

class FlinkCDCKudu extends AbstractDriver
{
    public function validate()
    {
        \Validator::make($this->config, [
            'url'     => 'required|string',
        ])->validate();
    }

    public function connection(string $db = '')
    {
        return [];
    }

    public function query(string $db, string $sql)
    {
        return [];
    }

    public function select(string $db, string $sql)
    {
        return [];
    }

    // 库列表
    public function databases()
    {
        return [];
    }

    // 表列表
    public function tables(string $db)
    {
        return [];
    }

    // 字段列表
    public function fields(array $reader)
    {
        return [];
    }

    // 建库
    public function createDatabase(string $database)
    {
        // do nothing
    }

    // 容量
    public function capacity(array $reader)
    {
        return 0;
    }

    // 容量
    public function count(array $reader)
    {
        return 0;
    }
}
