<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Http;

use AlsendoOne\SDK\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testSuccessResponse(): void
    {
        $body = json_encode([
            'status' => 200,
            'message' => 'OK',
            'response' => ['order_id' => 123],
        ]);

        $response = new Response(200, $body);

        $this->assertTrue($response->isSuccess());
        $this->assertSame(200, $response->getApiStatus());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame(['order_id' => 123], $response->getResponseData());
        $this->assertSame(200, $response->getHttpStatusCode());
    }

    public function testErrorResponse(): void
    {
        $body = json_encode([
            'status' => 400,
            'message' => 'Validation error',
            'response' => [],
        ]);

        $response = new Response(200, $body);

        $this->assertFalse($response->isSuccess());
        $this->assertSame(400, $response->getApiStatus());
        $this->assertSame('Validation error', $response->getMessage());
    }

    public function testInvalidJsonReturnsEmptyArray(): void
    {
        $response = new Response(200, 'not json');

        $this->assertSame([], $response->toArray());
        $this->assertSame(0, $response->getApiStatus());
    }

    public function testGetBodyReturnsRawBody(): void
    {
        $body = '{"status":200,"message":"OK","response":{}}';
        $response = new Response(200, $body);

        $this->assertSame($body, $response->getBody());
    }

    public function testResponseDataDefaultsToEmptyArray(): void
    {
        $body = json_encode(['status' => 200, 'message' => 'OK']);
        $response = new Response(200, $body);

        $this->assertSame([], $response->getResponseData());
    }
}
