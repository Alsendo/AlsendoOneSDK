<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Http;

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
            'timeout' => 120,
            'connect_timeout' => 10,
        ];

        $this->client = new Client(array_merge($defaults, $config));
    }

    public function post(string $url, array $formParams, array $headers = []): Response
    {
        try {
            $options = ['form_params' => $formParams];
            if ($headers !== []) {
                $options['headers'] = $headers;
            }

            $response = $this->client->post($url, $options);

            return new Response(
                $response->getStatusCode(),
                (string) $response->getBody()
            );
        } catch (GuzzleException $e) {
            throw new ConnectionException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function get(string $url, array $queryParams, array $headers = []): Response
    {
        try {
            $options = ['query' => $queryParams];
            if ($headers !== []) {
                $options['headers'] = $headers;
            }

            $response = $this->client->get($url, $options);

            return new Response(
                $response->getStatusCode(),
                (string) $response->getBody()
            );
        } catch (GuzzleException $e) {
            throw new ConnectionException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
