<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Http;

use AlsendoOne\SDK\Exception\ConnectionException;
use AlsendoOne\SDK\Http\GuzzleHttpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class GuzzleHttpClientTest extends TestCase
{
    /** @var array<int, array<string, mixed>> */
    private array $captured = [];

    private GuzzleHttpClient $client;

    protected function setUp(): void
    {
        $this->captured = [];
    }

    public function testPostForwardsHeadersToGuzzle(): void
    {
        $this->client = $this->makeClient([new Psr7Response(200, [], '{}')]);

        $this->client->post('https://example.com/endpoint/', ['foo' => 'bar'], [
            'User-Agent' => 'AlsendoOneSDK/1.0.0 (PHP 7.4.33; Linux)',
            'X-Custom'   => 'value',
        ]);

        $this->assertCount(1, $this->captured);
        /** @var RequestInterface $request */
        $request = $this->captured[0]['request'];
        $this->assertSame('AlsendoOneSDK/1.0.0 (PHP 7.4.33; Linux)', $request->getHeaderLine('User-Agent'));
        $this->assertSame('value', $request->getHeaderLine('X-Custom'));
    }

    public function testGetForwardsHeadersToGuzzle(): void
    {
        $this->client = $this->makeClient([new Psr7Response(200, [], '{}')]);

        $this->client->get('https://example.com/endpoint/', ['q' => '1'], [
            'User-Agent' => 'AlsendoOneSDK/1.0.0 (PHP 7.4.33; Linux)',
        ]);

        $this->assertCount(1, $this->captured);
        /** @var RequestInterface $request */
        $request = $this->captured[0]['request'];
        $this->assertSame('AlsendoOneSDK/1.0.0 (PHP 7.4.33; Linux)', $request->getHeaderLine('User-Agent'));
    }

    public function testPostWithoutHeadersFallsBackToGuzzleDefault(): void
    {
        $this->client = $this->makeClient([new Psr7Response(200, [], '{}')]);

        $this->client->post('https://example.com/endpoint/', ['foo' => 'bar']);

        $this->assertCount(1, $this->captured);
        /** @var RequestInterface $request */
        $request = $this->captured[0]['request'];
        // SDK didn't set a custom UA on this path; Guzzle adds its own default.
        $this->assertStringStartsWith('GuzzleHttp/', $request->getHeaderLine('User-Agent'));
    }

    public function testPostWrapsGuzzleErrorInConnectionException(): void
    {
        $this->client = $this->makeClient([
            new ConnectException('boom', new Psr7Request('POST', 'https://example.com/')),
        ]);

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('HTTP request failed: boom');

        $this->client->post('https://example.com/endpoint/', []);
    }

    /**
     * @param array<int, mixed> $queue
     */
    private function makeClient(array $queue): GuzzleHttpClient
    {
        $mock = new MockHandler($queue);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($this->captured));

        return new GuzzleHttpClient(['handler' => $stack]);
    }
}
