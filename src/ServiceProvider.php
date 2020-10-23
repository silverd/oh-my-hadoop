<?php

namespace Silverd\LaravelHive;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->app->singleton('hadoop.impala', function ($app) {
            $config = $app['config']['laravel-hive']['impala'];
            return new $config['handler']($config['with']);
        });

        $this->app->singleton('hadoop.hive', function ($app) {
            $config = $app['config']['laravel-hive']['hive'];
            return new $config['handler']($config['with']);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config' => base_path() . '/config',
            ], 'laravel-hive');
        }
    }

    public function register()
    {

    }
}
