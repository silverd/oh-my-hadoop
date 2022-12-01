<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

class PostgreSQL extends AbstractDriver
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
            'host'     => $this->config['host'],
            'port'     => $this->config['port'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'database' => $db,
            'driver'   => 'pgsql',
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

    public function databases()
    {
        $dbs = $this->select('postgres', 'SELECT datname FROM pg_database');

        return \Arr::pluck($dbs, 'datname');
    }

    public function schemas()
    {
        $schemas = $this->select('postgres', 'SELECT nspname FROM pg_namespace');

        return \Arr::pluck($schemas, 'nspname');
    }

    public function tables(string $db, string $schema = 'public')
    {
        $tables = $this->select($db, "SELECT tablename FROM pg_tables WHERE schemaname='" . $schema . "'");

        return \Arr::pluck($tables, 'tablename');
    }

    public function fields(array $reader)
    {
        $schema = $reader['schema'] ?? 'public';

        $sql = <<<SQL
           SELECT
              col.table_schema ,
              col.table_name ,
              col.ordinal_position,
              col.column_name ,
              col.data_type ,
              col.character_maximum_length,
              col.ordinal_position,
              col.numeric_precision,
              col.numeric_scale,
              col.is_nullable,
              col.column_default,
              des.description
            FROM
              information_schema.columns col LEFT JOIN pg_description des
                ON col.table_name::regclass = des.objoid
              AND col.ordinal_position = des.objsubid
            WHERE
              table_schema = '{$schema}'
              AND table_name = '{$reader['table']}'
            ORDER BY
              ordinal_position
        SQL;

        $indexSql = <<<SQL
            select pg_attribute.attname as colname,pg_type.typname as typename,pg_constraint.conname as pk_name from
            pg_constraint  inner join pg_class
            on pg_constraint.conrelid = pg_class.oid
            inner join pg_attribute on pg_attribute.attrelid = pg_class.oid
            and  pg_attribute.attnum = pg_constraint.conkey[1]
            inner join pg_type on pg_type.oid = pg_attribute.atttypid
            where pg_class.relname = '{$reader['table']}'
            and pg_constraint.contype = 'p';
        SQL;

        $result = $this->select($reader['database'], $sql);

        $indexes = $this->select($reader['database'], $indexSql);
        $indexes = $indexes ? array_column($indexes, 'colname') : [];

        $fields = [];

        foreach ($result as $field) {
            $fields[] = [
                'name'        => $field['column_name'],
                'type'        => $field['data_type'],
                'sort'        => $field['ordinal_position'],
                'length'      => $field['character_maximum_length'] ?: $field['character_maximum_length'] ?: 0,
                'precision'   => $field['numeric_precision'] ?: 0,
                'is_nullable' => $field['is_nullable'] == 'YES' ? 1 : 0,
                'is_pk'       => in_array($field['table_name'], $indexes) ? 1 : 0,
                'comment'     => $field['description'],
                'extra'       => preg_match('/nextval\((.*)+::regclass\)/', $field['column_default']) ? 'auto_increment' : '',
            ];
        }

        return $fields;
    }

    public function createDatabase(string $db)
    {
        $this->query('', 'CREATE DATABASE ' . $db);
    }

    public function capacity(array $reader)
    {
        $sql = <<<SQL
            SELECT
                pg_total_relation_size('"' || table_schema || '"."' || table_name || '"') AS bytes_size
            FROM information_schema.tables
            WHERE table_schema = '{$reader['schema']}' AND table_name = '{$reader['table']}'
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
