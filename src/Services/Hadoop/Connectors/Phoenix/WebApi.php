<?php

namespace Silverd\OhMyHadoop\Services\Hadoop\Connectors\Phoenix;

use Silverd\OhMyHadoop\Services\Hadoop\Connectors\DbAbstract;

class WebApi extends DbAbstract
{
    public function connect()
    {
        // WebApi 无需连接句柄
    }

    public function close()
    {
        // WebApi 无需释放句柄
    }

    public function fetchRow(string $sql, array $params = [])
    {
        $params['sql'] = $sql;

        return $this->request('fetchone', $params);
    }

    public function fetchAll(string $sql, array $params = [])
    {
        $params['sql'] = $sql;

        return $this->request('fetchall', $params);
    }

    public function execute(string $sql, array $params = [])
    {
        $params['sql'] = $sql;

        return $this->request('execute', $params);
    }

    protected function request(string $method, array $params = [], array $headers = [])
    {
        $params['db'] = $this->dbName;

        $headers += [
            'Accept' => 'application/json',
        ];

        $url = $this->config['url'] .  '/' . $method;

        $response = \Http::withHeaders($headers)->post($url, $params);

        $respJson = json_decode($response, true);

        if (! $respJson) {
            throws('PhoenixWebApiFailed: ' . $response);
        }

        if (! isset($respJson['data'])) {
            throws('PhoenixWebApiFailed: ' . $response);
        }

        if ($respJson['code'] == -1) {
            throws('PhoenixWebApiError: ' . $respJson['message']);
        }

        return $respJson['data'] ?? null;
    }
}
