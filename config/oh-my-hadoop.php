<?php

return [

    'hive' => [
        'handler' => Silverd\OhMyHadoop\Services\Hadoop\Connectors\Hive\Odbc::class,
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
        'handler' => Silverd\OhMyHadoop\Services\Hadoop\Connectors\Impala\Odbc::class,
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


    'phoenix' => [
        'handler' => Silverd\OhMyHadoop\Services\Hadoop\Connectors\Phoenix\WebApi::class,
        'with' => [
            'url'      => env('HADOOP_PHOENIX_WEBAPI_URL'),
            'username' => env('HADOOP_PHOENIX_WEBAPI_USERNAME'),
            'password' => env('HADOOP_PHOENIX_WEBAPI_PASSWORD'),
        ],
    ],

    'ssh_bridge' => [
        'host'    => env('NCDH_SSH_BRIDGE_HOST'),
        'user'    => env('NCDH_SSH_BRIDGE_USER'),
        'prv_key' => env('NCDH_SSH_BRIDGE_PRVKEY'),
    ],

    'hdfs' => [
        'handler' => Silverd\OhMyHadoop\Services\Hadoop\Connectors\WebHDFS::class,
        'with' => [
            'host'              => env('HADOOP_HDFS_HOST'),
            'port'              => env('HADOOP_HDFS_PORT'),
            'user'              => env('HADOOP_HDFS_USER'),
            'nn_rpc_host'       => env('HADOOP_HDFS_NN_RPC_HOST'),
            'nn_rpc_port'       => env('HADOOP_HDFS_NN_RPC_PROT'),
            'mask_aes_key_path' => env('HADOOP_HDFS_MASK_AES_KEY_PATH'),
        ],
    ]

];
