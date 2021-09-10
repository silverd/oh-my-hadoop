<?php

// @see https://packagist.org/packages/simpleenergy/php-webhdfs

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors;

use org\apache\hadoop\WebHDFS as WebHDFSClient;

class WebHDFS
{
    protected $config;
    protected $client;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function client()
    {
        $isDebug = config('app.debug', false);

        if (! $this->client) {
            $this->client = new WebHDFSClient(
                $this->config['host'],
                $this->config['port'],
                $this->config['user'],
                $this->config['nn_rpc_host'],
                $this->config['nn_rpc_port'],
                $isDebug
            );
        }

        return $this->client;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->client(), $method], $arguments);
    }

    public function getMaskAesUser()
    {
        $response = $this->client()->open($this->config['mask_aes_key_path']);

        // 解析配置文件
        $config = parse_ini_string($response);

        $users = explode(',', $config['user_passwd'] ?: []);

        // 返回第一个可用的
        return $users[0] ?? '';
    }
}
