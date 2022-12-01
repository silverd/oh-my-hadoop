<?php

/**
 * 数据源连接工厂
 *
 * @author 高灯数据中台 <tech-cx@goldentec.com>
 */

namespace Silverd\OhMyHadoop\Services\DBQuery;

class Factory
{
    const DRIVERS = [
        'Hive',
        'MySQL',
        'PostgreSQL',
        'ClickHouse',
        'MongoDB',
        'FlinkCDCKudu',
        'Impala',
        'Kudu',
    ];

    public static function make(string $driver, array $config)
    {
        $driver = str_replace('@', '', $driver);

        if (! in_array($driver, self::DRIVERS)) {
            throws('Invalid DB Driver');
        }

        $class = __NAMESPACE__ . '\Drivers\\' . \Str::studly($driver);

        return new $class($config);
    }
}
