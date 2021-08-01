<?php

namespace Silverd\OhMyHadoop;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $services = [
            'hive',
            'impala',
            'phoenix',
        ];

        foreach ($services as $svc) {
            $this->app->singleton('hadoop.' . $svc, function ($app) use ($svc) {
                $config = $app['config']['oh-my-hadoop'][$svc];
                return new $config['handler']($config['with']);
            });
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config' => base_path() . '/config',
            ], 'oh-my-hadoop');
        }
    }

    public function register()
    {

    }
}
