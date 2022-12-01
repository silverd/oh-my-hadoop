<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

class Kudu extends AbstractDriver
{
    public function validate()
    {
        \Validator::make($this->config, [
            'urls' => 'required|string',
        ])->validate();
    }

    public function connection(string $db = '')
    {
        // do nothing
    }

    // 执行 DDL 语句
    public function query(string $db, string $sql)
    {
        // do nothing
    }

    // 执行 DML 语句
    public function select(string $db, string $sql)
    {
        // do nothing
    }

    // 获取所有数据库
    public function databases()
    {
        // do nothing
    }

    // 获取所有数据表
    public function tables(string $db)
    {
        // do nothing
    }

    public function fields(array $reader)
    {
        // do nothing
    }

    // 建库
    public function createDatabase(string $database)
    {
        // do nothing
    }

    public function capacity(array $reader)
    {
        return 0;
    }

    public function count(array $reader)
    {
        return 0;
    }
}
