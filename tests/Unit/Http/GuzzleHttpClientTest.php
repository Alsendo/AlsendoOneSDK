<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Http;

use AlsendoOne\SDK\ApaczkaClient;
use AlsendoOne\SDK\Http\GuzzleHttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as Psr7Response;
use PHPUnit\Framework\TestCase;

class GuzzleHttpClientTest extends TestCase
{
    public function testSendsSdkUserAgentByDefault(): void
    {
        $history = [];
        $client = $this->createClientWithHistory($history);

        $client->post('https://api.example.com/api/v2/service_structure/', ['app_id' => 'x']);

        $userAgent = $history[0]['request']->getHeaderLine('User-Agent');
        $this->assertSame(
            sprintf('AlsendoOneSDK/%s PHP/%s', ApaczkaClient::version(), PHP_VERSION),
            $userAgent
        );
    }

    public function testUserAgentCanBeOverriddenByConfig(): void
    {
        $history = [];
        $client = $this->createClientWithHistory($history, [
            'headers' => ['User-Agent' => 'MyApp/2.0'],
        ]);

        $client->post('https://api.example.com/api/v2/service_structure/', ['app_id' => 'x']);

        $this->assertSame('MyApp/2.0', $history[0]['request']->getHeaderLine('User-Agent'));
    }

    /**
     * @param array<int, array{request: \Psr\Http\Message\RequestInterface}> $history
     * @param array<string, mixed> $config
     */
    private function createClientWithHistory(array &$history, array $config = []): GuzzleHttpClient
    {
        $mock = new MockHandler([
            new Psr7Response(200, [], '{"status":200,"message":"","response":{}}'),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        return new GuzzleHttpClient(array_merge(['handler' => $stack], $config));
    }
}
