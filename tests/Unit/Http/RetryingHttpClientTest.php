<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Http;

use AlsendoOne\SDK\Exception\ConnectionException;
use AlsendoOne\SDK\Http\HttpClientInterface;
use AlsendoOne\SDK\Http\Response;
use AlsendoOne\SDK\Http\RetryingHttpClient;
use PHPUnit\Framework\TestCase;

class RetryingHttpClientTest extends TestCase
{
    public function testReturnsResponseOnFirstSuccess(): void
    {
        $inner = $this->createMock(HttpClientInterface::class);
        $inner->expects($this->once())
            ->method('post')
            ->willReturn(new Response(200, '{"status":200}'));

        $client = new RetryingHttpClient($inner, 2, 0);

        $this->assertSame(200, $client->post('https://api.example.com/x/', [])->getHttpStatusCode());
    }

    public function testRetriesOnConnectionExceptionAndSucceeds(): void
    {
        $inner = $this->createMock(HttpClientInterface::class);
        $inner->expects($this->exactly(3))
            ->method('post')
            ->willReturnOnConsecutiveCalls(
                $this->throwException(new ConnectionException('timeout')),
                $this->throwException(new ConnectionException('timeout')),
                new Response(200, '{"status":200}')
            );

        $client = new RetryingHttpClient($inner, 2, 0);

        $this->assertSame(200, $client->post('https://api.example.com/x/', [])->getHttpStatusCode());
    }

    public function testGivesUpAfterMaxRetries(): void
    {
        $inner = $this->createMock(HttpClientInterface::class);
        $inner->expects($this->exactly(3))
            ->method('post')
            ->willThrowException(new ConnectionException('down'));

        $client = new RetryingHttpClient($inner, 2, 0);

        $this->expectException(ConnectionException::class);
        $client->post('https://api.example.com/x/', []);
    }

    public function testDoesNotRetryApiLevelErrors(): void
    {
        // An error envelope is a valid HTTP response — it must pass through untouched.
        $inner = $this->createMock(HttpClientInterface::class);
        $inner->expects($this->once())
            ->method('post')
            ->willReturn(new Response(200, '{"status":400,"message":"Order not found."}'));

        $client = new RetryingHttpClient($inner, 5, 0);

        $this->assertSame(400, $client->post('https://api.example.com/x/', [])->getApiStatus());
    }
}
