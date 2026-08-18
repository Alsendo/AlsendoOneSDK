<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Http;

use AlsendoOne\SDK\ApaczkaClient;
use AlsendoOne\SDK\Exception\ConnectionException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleHttpClient implements HttpClientInterface
{
    private Client $client;

    /**
     * @param array<string, mixed> $config Guzzle client config options
     */
    public function __construct(array $config = [])
    {
        $defaults = [
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => [
                'User-Agent' => sprintf('AlsendoOneSDK/%s PHP/%s', ApaczkaClient::VERSION, PHP_VERSION),
            ],
        ];

        $this->client = new Client(array_replace_recursive($defaults, $config));
    }

    public function post(string $url, array $formParams): Response
    {
        try {
            $response = $this->client->post($url, [
                'form_params' => $formParams,
            ]);

            return new Response(
                $response->getStatusCode(),
                (string) $response->getBody()
            );
        } catch (GuzzleException $e) {
            throw new ConnectionException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function get(string $url, array $queryParams): Response
    {
        try {
            $response = $this->client->get($url, [
                'query' => $queryParams,
            ]);

            return new Response(
                $response->getStatusCode(),
                (string) $response->getBody()
            );
        } catch (GuzzleException $e) {
            throw new ConnectionException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
