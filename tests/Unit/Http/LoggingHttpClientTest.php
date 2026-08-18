<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Http;

use AlsendoOne\SDK\Exception\ConnectionException;
use AlsendoOne\SDK\Http\HttpClientInterface;
use AlsendoOne\SDK\Http\LoggingHttpClient;
use AlsendoOne\SDK\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;

class LoggingHttpClientTest extends TestCase
{
    /** @var array<int, array{level: string, message: string, context: array<string, mixed>}> */
    private array $records = [];

    private function logger(): AbstractLogger
    {
        $records = &$this->records;

        return new class ($records) extends AbstractLogger {
            /** @var array<int, array{level: string, message: string, context: array<string, mixed>}> */
            private array $records;

            /** @param array<int, array{level: string, message: string, context: array<string, mixed>}> $records */
            public function __construct(array &$records)
            {
                $this->records = &$records;
            }

            public function log($level, $message, array $context = []): void
            {
                $this->records[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
            }
        };
    }

    public function testLogsSuccessfulRequestWithoutParams(): void
    {
        $inner = $this->createMock(HttpClientInterface::class);
        $inner->method('post')->willReturn(new Response(200, '{"status":200}'));

        $client = new LoggingHttpClient($inner, $this->logger());
        $client->post('https://api.example.com/order_send/', ['app_id' => 'secretish', 'request' => '{"x":1}']);

        $this->assertCount(1, $this->records);
        $record = $this->records[0];
        $this->assertSame('info', $record['level']);
        $this->assertSame('https://api.example.com/order_send/', $record['context']['url']);
        $this->assertSame(200, $record['context']['api_status']);
        $this->assertArrayHasKey('duration_ms', $record['context']);

        // Credentials and payload must never be logged.
        $this->assertStringNotContainsString('secretish', json_encode($record));
    }

    public function testLogsAndRethrowsConnectionFailure(): void
    {
        $inner = $this->createMock(HttpClientInterface::class);
        $inner->method('post')->willThrowException(new ConnectionException('timeout'));

        $client = new LoggingHttpClient($inner, $this->logger());

        try {
            $client->post('https://api.example.com/orders/', []);
            $this->fail('Expected ConnectionException');
        } catch (ConnectionException $e) {
            $this->assertSame('timeout', $e->getMessage());
        }

        $this->assertCount(1, $this->records);
        $this->assertSame('error', $this->records[0]['level']);
        $this->assertSame('timeout', $this->records[0]['context']['error']);
    }
}
