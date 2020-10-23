本扩展用于 Laravel 连接 Hive/Impala 数仓

### 如何使用

```
composer require silverd/laravel-hive:dev-master
php artisan vendor:publish --tag laravel-hive
```

### 本地开发

```
cd ~/home/wwwroot/
git clone git@github.com:silverd/laravel-hive.git

cd ~/homw/wwwroot/sample_prcd oject
composer config repositories.silverd/laravel-hive path ~/home/wwwroot/laravel-hive
composer require silverd/laravel-hive:dev-master -vvv
```
