<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

use Silverd\OhMyHadoop\Services\Hadoop\Connectors\Impala\Odbc as ImpalaOdbc;

class Impala extends Hive
{
    public function connection(string $db = '')
    {
        $this->config['dsn'] = $this->config['dsn'] ?? 'ImpalaOnCDH';

        return new ImpalaOdbc($this->config);
    }

    // 获取所有数据库
    public function databases()
    {
        $dbs = $this->select('', 'SHOW DATABASES');

        return \Arr::pluck($dbs, 'name');
    }

    // 获取所有数据表
    public function tables(string $db)
    {
        $result = $this->select($db, 'SHOW TABLES');

        return \Arr::pluck($result, 'name');
    }

    public function allFields(array $reader)
    {
        $result = $this->select($reader['database'], 'DESCRIBE FORMATTED ' . $reader['table']);

        $fields     = [];
        $partitions = [];

        $startPartitionField = 0;

        foreach ($result as $field) {

            $field['name'] = trim($field['name']);

            if (! $field['name']) {
                continue;
            }

            // 当字段名为 # Partition Information 时，后面的都是分区字段
            if (in_array($field['name'], ['# Partition Information'])) {
                $startPartitionField = 1;
                continue;
            }

            if (in_array($field['name'], ['# Detailed Table Information'])) {
                break;
            }

            if (strpos($field['name'], '#') === 0) {
                continue;
            }

            $one = [
                'name'    => $field['name'],
                'type'    => $field['type'],
                'comment' => $field['comment'],
                'extra'   => '',
            ];

            // 普通字段
            if (! $startPartitionField) {
                $fields[$field['name']] = $one;
            }
            // 分区字段
            else {
                $partitions[$field['name']] = $one;
                if (isset($fields[$field['name']])) {
                    unset($fields[$field['name']]);
                }
            }
        }

        return [
            array_values($fields),
            array_values($partitions),
        ];
    }

    public function count(array $reader)
    {
        $data = $this->select($reader['database'], "SELECT COUNT(1) AS cnt FROM `{$reader['database']}`.`{$reader['table']}`");

        return (int) $data[0]['cnt'];
    }
}
