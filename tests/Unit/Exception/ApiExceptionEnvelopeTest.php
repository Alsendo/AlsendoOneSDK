<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Exception;

use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Http\Response;
use PHPUnit\Framework\TestCase;

class ApiExceptionEnvelopeTest extends TestCase
{
    public function testUsesEnvelopeMessageWhenPresent(): void
    {
        $response = new Response(200, '{"status":400,"message":"Order not found.","response":[]}');

        $exception = new ApiException($response);

        $this->assertSame('Order not found.', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
    }

    public function testDescribesNonEnvelopeBody(): void
    {
        $response = new Response(200, '<!doctype html><html><body>Maintenance</body></html>');

        $exception = new ApiException($response);

        $this->assertStringContainsString('Unexpected API response without a valid envelope (HTTP 200)', $exception->getMessage());
        $this->assertStringContainsString('<!doctype html>', $exception->getMessage());
    }

    public function testTruncatesLongNonEnvelopeBody(): void
    {
        $response = new Response(502, str_repeat('x', 5000));

        $exception = new ApiException($response);

        $this->assertLessThan(300, strlen($exception->getMessage()));
        $this->assertStringContainsString('HTTP 502', $exception->getMessage());
    }

    public function testKeepsEmptyMessageForEmptyBody(): void
    {
        $response = new Response(200, '');

        $exception = new ApiException($response);

        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }
}
