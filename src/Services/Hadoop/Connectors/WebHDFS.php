<?php

// @see https://packagist.org/packages/simpleenergy/php-webhdfs

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors;

class WebHDFS
{
    protected $config;
    protected $client;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        $isDebug = config('app.debug', false);

        if (! $this->client) {
            $this->client = new WebHDFS(
                $this->config['host'],
                $this->config['port'],
                $this->config['user'],
                $this->config['nn_http_host'],
                $this->config['nn_http_port'],
                $isDebug
            );
        }

        return $this->client;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array($this->connect(), $arguments);
    }
}
