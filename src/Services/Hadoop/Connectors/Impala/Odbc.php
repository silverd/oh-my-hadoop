<?php

namespace Silverd\LaravelHive\Services\Hadoop\Connectors\Impala;

use Silverd\LaravelHive\Services\Hadoop\Connectors\OdbcAbstract;

class Odbc extends OdbcAbstract
{
    public function getDsnStrs()
    {
        return [
            'KrbFQDN'     => $this->config['krbFQDN'] ?? null,
            'KrbRealm'    => $this->config['krbRealm'] ?? null,
            'KrbAuthType' => $this->config['krbAuthType'] ?? null,
        ];
    }
}
