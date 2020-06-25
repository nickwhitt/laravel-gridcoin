<?php

namespace NickWhitt\Gridcoin;

use GuzzleHttp\Client as GuzzleHttpClient;
use Ramsey\Uuid\Uuid;

class RpcClient
{
    const VER = '2.0';

    protected $client;
    protected $response;

    public function __construct(array $config=[])
    {
        $this->client = new GuzzleHttpClient($config);
    }

    public function getinfo()
    {
        return $this->request('getinfo');
    }

    public function getBestBlockHash()
    {
        return $this->request('getbestblockhash');
    }

    public function getBlockCount()
    {
        return $this->request('getblockcount');
    }

    public function getblockhash(int $index)
    {
        return $this->request('getblockhash', [$index]);
    }

    public function getblock($hash)
    {
        return $this->request('getblock', [$hash]);
    }

    public function getTransaction($hash)
    {
        return $this->request('gettransaction', [$hash]);
    }

    public function request($method, array $params=[])
    {
        $this->response = $this->client->post('/', ['http_errors' => false, 'json' => [
            'jsonrpc' => static::VER,
            'method' => $method,
            'params' => $params,
            'id' => Uuid::uuid1()->toString(),
        ]]);

        if ($this->response->getStatusCode() == 401) {
            throw new \Exception('Invalid RPC credentials');
        }

        return json_decode($this->response->getBody());
    }
}
