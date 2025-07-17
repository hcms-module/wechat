<?php

namespace App\Application\Wechat\Service\Work;

use App\Exception\ErrorException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

abstract class AbstractWorkObject
{
    function __construct(protected AccessTokenAwareClient $client, protected string $corp_id)
    {

    }

    /**
     * @param string $uri
     * @param array  $query
     * @return array
     * @throws ErrorException
     */
    public function get(string $uri, array $query = []): array
    {
        try {
            return $this->client->get($uri, $query)
                ->toArray();
        } catch (Throwable $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    /**
     * @param string $uri
     * @param array  $query
     * @return array
     * @throws ErrorException
     */
    public function postJson(string $uri, array $query = []): array
    {
        try {
            return $this->client->postJson($uri, $query)
                ->toArray();
        } catch (Throwable $e) {
            throw new ErrorException($e->getMessage());
        }
    }
}