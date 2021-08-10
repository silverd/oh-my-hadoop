<?php

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors\Hive;

use Silverd\OhMyHadoop\Services\Hadoop\Connectors\OdbcAbstract;

class Odbc extends OdbcAbstract
{
    public function connect()
    {
        $conn = parent::connect();

        // @see https://stackoverflow.com/questions/43112868/avoid-printing-table-name-in-column-name-while-using-beeline
        // @see https://cwiki.apache.org/confluence/display/Hive/Configuration+Properties
        odbc_exec($conn, 'SET hive.resultset.use.unique.column.names=false');

        return $conn;
    }

    public function getDsnStrs()
    {
        return array_filter([
            'KrbHostFQDN' => $this->config['krbFQDN'] ?? null,
            'KrbRealm'    => $this->config['krbRealm'] ?? null,
            'KrbAuthType' => $this->config['krbAuthType'] ?? null,
        ]);
    }
}
