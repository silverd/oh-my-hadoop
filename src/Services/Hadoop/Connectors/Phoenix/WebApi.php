<?php

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors;

use Silverd\OhMyHadoop\Services\Hadoop\Connectors\DbAbstract;

class WebApi extends DbAbstract
{
    public function connect()
    {
        // 无需连接
    }

    public function close()
    {
        // 无需释放
    }

    public function fetchRow(string $sql, array $params = [])
    {
        return $this->request('fetchone', $sql);
    }

    public function fetchAll(string $sql, array $params = [])
    {
        return $this->request('fetchall', $sql);
    }

    public function execute(string $sql, array $params = [])
    {
        return $this->request('execute', $sql);
    }

    protected function request(string $method, array $params = [], array $headers = [])
    {
        $headers += [
            'Accept' => 'application/json',
        ];

        $url = $this->config['url'] .  '/' . $method;

        $response = \Http::withHeaders($headers)->post($url, $params);

        $respJson = json_decode($response, true);

        if (! $respJson) {
            throws('PhoenixWebApiFailed: ' . $response);
        }

        if ($respJson['code'] == -1) {
            throws('PhoenixWebApiError: ' . $respJson['message']);
        }

        return $respJson;
    }
}
