<?php

return [

    'hive' => [
        'handler' => Silverd\OhMyHadoop\Services\Hadoop\Connectors\Hive\Odbc::class,
        'with' => [
            'dsn'      => env('HADOOP_HIVE_DSN'),
            'host'     => env('HADOOP_HIVE_HOST'),
            'port'     => env('HADOOP_HIVE_PORT'),
            'username' => env('HADOOP_HIVE_USERNAME'),
            'password' => env('HADOOP_HIVE_PASSWORD'),
            'timeout'  => env('HADOOP_HIVE_TIMEOUT'),
        ],
    ],

    'impala' => [
        'handler' => Silverd\OhMyHadoop\Services\Hadoop\Connectors\Impala\Odbc::class,
        'with' => [
            'dsn'      => env('HADOOP_IMPALA_DSN'),
            'host'     => env('HADOOP_IMPALA_HOST'),
            'port'     => env('HADOOP_IMPALA_PORT'),
            'username' => env('HADOOP_IMPALA_USERNAME'),
            'password' => env('HADOOP_IMPALA_PASSWORD'),
            'timeout'  => env('HADOOP_IMPALA_TIMEOUT'),
        ],
    ],

    'phoenix' => [
        'handler' => Silverd\OhMyHadoop\Services\Hadoop\Connectors\Phoenix\WebApi::class,
        'with' => [
            'url'      => env('HADOOP_PHOENIX_WEBAPI_URL'),
            'username' => env('HADOOP_PHOENIX_WEBAPI_USERNAME'),
            'password' => env('HADOOP_PHOENIX_WEBAPI_PASSWORD'),
        ],
    ],

];
