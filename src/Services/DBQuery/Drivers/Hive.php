<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

use Silverd\OhMyHadoop\Services\DBQuery\Factory;
use Silverd\OhMyHadoop\Services\Hadoop\Connectors\Hive\Odbc as HiveOdbc;

class Hive extends AbstractDriver
{
    // 无身份认证
    const NO_AUTH = 0;

    // Kerberos 认证
    const KERBEROS = 1;

    // 用户名认证
    const USERNAME = 2;

    // 用户名密码认证
    const PASSWORD = 3;

    // 验证模式
    const AUTH_MECHS = [
        self::NO_AUTH,
        self::KERBEROS,
        self::USERNAME,
        self::PASSWORD
    ];

    public function validate()
    {
        \Validator::make($this->config, [
            'host'               => 'required|string',
            'port'               => 'required|int',
            'authMech'           => 'required|int|in:' . implode(',', self::AUTH_MECHS),
            'krbFQDN'            => 'required_if:type,' . self::KERBEROS . '|string',
            'krbRealm'           => 'required_if:type,' . self::KERBEROS . '|string',
            'krbAuthType'        => 'required_if:type,' . self::KERBEROS . '|int',
            'username'           => 'required_if:type,' . self::USERNAME . ',' . self::PASSWORD . '|string',
            'password'           => 'required_if:type,' . self::PASSWORD . '|string',
            'metaStore'          => 'nullable|array',
            'metaStore.host'     => 'string',
            'metaStore.port'     => 'int',
            'metaStore.username' => 'string',
            'metaStore.password' => 'string',
            'metaStore.db'       => 'string',
        ])->validate();

        $this->databases();
    }

    public function connection(string $db = '')
    {
        $this->config['dsn'] = $this->config['dsn'] ?? 'HiveOnCDH';

        return new HiveOdbc($this->config);
    }

    // 执行 DDL 语句
    public function query(string $db, string $sql)
    {
        return $this->connection()->selectDb($db)->execute($sql);
    }

    // 执行 DML 语句
    public function select(string $db, string $sql)
    {
        return $this->connection()->selectDb($db)->fetchAll($sql);
    }

    // 获取所有数据库
    public function databases()
    {
        // 直接读元数据
        if ($this->config['metaStore'] ?? []) {
            return $this->databasesByMS();
        }

        $dbs = $this->select('', 'SHOW DATABASES');

        return \Arr::pluck($dbs, 'database_name');
    }

    // 获取所有数据表
    public function tables(string $db)
    {
        // 直接读元数据
        if ($this->config['metaStore'] ?? []) {
            return $this->tablesByMS($db);
        }

        $result = $this->select($db, 'SHOW TABLES');

        return \Arr::pluck($result, 'tab_name');
    }

    public function allFields(array $reader)
    {
        // 直接读元数据
        if ($this->config['metaStore'] ?? []) {
            return $this->allFieldsByMS($reader);
        }

        $result = $this->select($reader['database'], 'DESCRIBE ' . $reader['table']);

        $fields = $partitions = [];

        $startPartitionField = 0;

        foreach ($result as $field) {

            $field['col_name'] = trim($field['col_name']);

            if (! $field['col_name']) {
                continue;
            }

            // 当字段名为 # Partition Information 时，后面的都是分区字段
            if (in_array($field['col_name'], ['# Partition Information'])) {
                $startPartitionField = 1;
                continue;
            }

            if (strpos($field['col_name'], '#') === 0) {
                continue;
            }

            preg_match('/^[\w]*/', $field['data_type'] ?? '' , $type);

            $one = [
                'name'    => $field['col_name'],
                'type'    => $type[0] ?? '',
                // 坑：Hive ODBC 对 DESCRIBE 的结果「字段注释」超过255会自动截断
                // 如遇中文则会出现乱码，导致 JSON 输出时报错 Malformed UTF-8 characters, possibly incorrectly encoded
                'comment' => removeUnknownChar($field['comment']),
                'extra'   => '',
            ];

            // 普通字段
            if (! $startPartitionField) {
                $fields[$field['col_name']] = $one;
            }
            // 分区字段
            else {
                $partitions[$field['col_name']] = $one;
                if (isset($fields[$field['col_name']])) {
                    unset($fields[$field['col_name']]);
                }
            }
        }

        return [
            array_values($fields),
            array_values($partitions),
        ];
    }

    public function fields(array $reader)
    {
        $result = $this->allFields($reader);

        return array_unique($result[0], SORT_REGULAR);
    }

    // 建库
    public function createDatabase(string $database)
    {
        $this->query('', 'CREATE DATABASE IF NOT EXISTS ' . $database);
    }

    public function capacity(array $reader)
    {
        $data = $this->select($reader['database'], "SHOW TBLPROPERTIES `{$reader['database']}`.`{$reader['table']}`('totalSize')");

        $data = is_array($data[0]) ? ($data[0]['prpt_name'] ?? 0) : ($data[0]->prpt_name ?? 0);

        return (int) $data;
    }

    public function count(array $reader)
    {
        $data = $this->select($reader['database'], "SELECT COUNT(1) AS cnt FROM `{$reader['database']}`.`{$reader['table']}`");

        return (int) $data[0]['cnt'];
    }

    public function partitions(array $reader)
    {
        $result = $this->allFields($reader);

        return $result[1];
    }

    protected function databasesByMS()
    {
        $sql = <<<SQL
            SELECT `NAME` FROM `DBS`
        SQL;

        $result = Factory::make('MySQL', $this->config['metaStore'])->select($this->config['metaStore']['db'], $sql);

        return \Arr::pluck($result, 'NAME');
    }

    protected function tablesByMS(string $db)
    {
        $sql = <<<SQL
            SELECT
                `TBLS`.`TBL_NAME`
            FROM `TBLS`
            INNER JOIN `DBS` ON `DBS`.`DB_ID` = `TBLS`.`DB_ID`
            WHERE `DBS`.`NAME` = '{$db}'
        SQL;

        $result = Factory::make('MySQL', $this->config['metaStore'])->select($this->config['metaStore']['db'], $sql);

        return \Arr::pluck($result, 'TBL_NAME');
    }

    protected function allFieldsByMS(array $reader)
    {
        $sql = <<<SQL
            SELECT
                `COLUMNS_V2`.`COLUMN_NAME`,
                `COLUMNS_V2`.`TYPE_NAME`,
                `COLUMNS_V2`.`COMMENT`
            FROM `COLUMNS_V2`
            INNER JOIN `SDS` ON `COLUMNS_V2`.`CD_ID` = `SDS`.`CD_ID`
            INNER JOIN `TBLS` ON `TBLS`.`SD_ID` = `SDS`.`SD_ID`
            INNER JOIN `DBS` ON `DBS`.`DB_ID` = `TBLS`.`DB_ID`
            WHERE `DBS`.`NAME` = '{$reader['database']}' AND `TBLS`.`TBL_NAME` = '{$reader['table']}'
            ORDER BY `COLUMNS_V2`.`INTEGER_IDX` ASC
        SQL;

        $fields = Factory::make('MySQL', $this->config['metaStore'])->select($this->config['metaStore']['db'], $sql);

        $sql = <<<SQL
            SELECT
                `pt`.PKEY_NAME AS `COLUMN_NAME`,
                `pt`.PKEY_TYPE AS `TYPE_NAME`,
                `pt`.PKEY_COMMENT AS `COMMENT`
            FROM `PARTITION_KEYS` AS `pt`
            INNER JOIN `TBLS` ON `TBLS`.`TBL_ID` = `pt`.`TBL_ID`
            INNER JOIN `DBS` ON `DBS`.`DB_ID` = `TBLS`.`DB_ID`
            WHERE `DBS`.`NAME` = '{$reader['database']}' AND `TBLS`.`TBL_NAME` = '{$reader['table']}'
            ORDER BY `pt`.`INTEGER_IDX` ASC
        SQL;

        $partitions = Factory::make('MySQL', $this->config['metaStore'])->select($this->config['metaStore']['db'], $sql);

        $cast = function ($one) {
            preg_match('/^[\w]*/', $one['TYPE_NAME'], $type);
            return [
                'name'    => $one['COLUMN_NAME'],
                'type'    => $type[0] ?? '',
                'comment' => $one['COMMENT'] ?? '',
                'extra'   => '',
            ];
        };

        return [
            array_map($cast, $fields),
            array_map($cast, $partitions),
        ];
    }
}
