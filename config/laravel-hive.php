<?php

return [

    'hive' => [
        'handler' => Silverd\LaravelHive\Services\Hadoop\Connectors\Hive\Odbc::class,
        'with' => [
            'dsn'         => env('HADOOP_HIVE_DSN'),
            'host'        => env('HADOOP_HIVE_HOST'),
            'port'        => env('HADOOP_HIVE_PORT'),
            'authMech'    => env('HADOOP_HIVE_AUTHMECH'),
            'username'    => env('HADOOP_HIVE_USERNAME'),
            'password'    => env('HADOOP_HIVE_PASSWORD'),
            'timeout'     => env('HADOOP_HIVE_TIMEOUT'),
            'krbFQDN'     => env('HADOOP_HIVE_KRB_FQDN'),
            'krbRealm'    => env('HADOOP_HIVE_KRB_REALM'),
            'krbAuthType' => env('HADOOP_HIVE_KRB_AUTH_TYPE'),
        ],
    ],

    'impala' => [
        'handler' => Silverd\LaravelHive\Services\Hadoop\Connectors\Impala\Odbc::class,
        'with' => [
            'dsn'         => env('HADOOP_IMPALA_DSN'),
            'host'        => env('HADOOP_IMPALA_HOST'),
            'port'        => env('HADOOP_IMPALA_PORT'),
            'authMech'    => env('HADOOP_IMPALA_AUTHMECH'),
            'username'    => env('HADOOP_IMPALA_USERNAME'),
            'password'    => env('HADOOP_IMPALA_PASSWORD'),
            'timeout'     => env('HADOOP_IMPALA_TIMEOUT'),
            'krbFQDN'     => env('HADOOP_IMPALA_KRB_FQDN'),
            'krbRealm'    => env('HADOOP_IMPALA_KRB_REALM'),
            'krbAuthType' => env('HADOOP_IMPALA_KRB_AUTH_TYPE'),
        ],
    ],

];
