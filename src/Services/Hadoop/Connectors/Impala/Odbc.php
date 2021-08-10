<?php

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors\Impala;

use Silverd\OhMyHadoop\Services\Hadoop\Connectors\OdbcAbstract;

class Odbc extends OdbcAbstract
{
    public function getDsnStrs()
    {
        return array_filter([
            'KrbFQDN'     => $this->config['krbFQDN'] ?? null,
            'KrbRealm'    => $this->config['krbRealm'] ?? null,
            'KrbAuthType' => $this->config['krbAuthType'] ?? null,
        ]);
    }
}
