本扩展用于 Laravel 连接 Hive/Impala/Hbase/Phoenix 数仓

### 如何使用？

请先参照 `ODBC.md` 配置 `ODBC for Hive/Impala` 驱动和 `DSN` 数据源，然后再使用本扩展。

```
composer require silverd/oh-my-hadoop:dev-master
php artisan vendor:publish --tag oh-my-hadoop
```

在项目根目录的 `.env` 文件中增加以下配置：

```
HADOOP_IMPALA_DSN=ImpalaOnCDH
HADOOP_IMPALA_HOST=10.35.3.4
HADOOP_IMPALA_PORT=21050
HADOOP_IMPALA_AUTHMECH=1
HADOOP_IMPALA_KRB_FQDN=qa-worker-1
HADOOP_IMPALA_KRB_REALM=CX-DMP.COM
HADOOP_IMPALA_KRB_AUTH_TYPE=2
HADOOP_IMPALA_USERNAME=
HADOOP_IMPALA_PASSWORD=

HADOOP_HIVE_DSN=HiveOnCDH
HADOOP_HIVE_HOST=10.35.3.2
HADOOP_HIVE_PORT=10000
HADOOP_HIVE_AUTHMECH=1
HADOOP_HIVE_KRB_FQDN=qa-master-1
HADOOP_HIVE_KRB_REALM=CX-DMP.COM
HADOOP_HIVE_KRB_AUTH_TYPE=2
HADOOP_HIVE_USERNAME=
HADOOP_HIVE_PASSWORD=
```

其中 `ImpalaOnCDH` 和 `HiveOnCDH` 为在 `/etc/odbc.ini` 中配置的数据源名称。

调用方法：

```php
$db  = 'kbb';
$sql = 'SELECT * FROM `table` LIMIT 1';

$a = app('hadoop.impala')->selectDb($db)->fetchAll($sql);
$b = app('hadoop.impala')->selectDb($db)->fetchRow($sql);
$c = app('hadoop.hive')->selectDb($db)->fetchAll($sql);
$d = app('hadoop.hive')->selectDb($db)->fetchRow($sql);
```

### 本地二次开发本扩展

```
cd ~/home/wwwroot/
git clone git@github.com:silverd/oh-my-hadoop.git

cd ~/homw/wwwroot/sample_project
rm -rf vendor/silverd/oh-my-hadoop
composer config repositories.silverd/oh-my-hadoop path ~/home/wwwroot/oh-my-hadoop
composer require silverd/oh-my-hadoop:dev-master -vvv
```
