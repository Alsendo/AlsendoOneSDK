<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Exception;

use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Http\Response;
use PHPUnit\Framework\TestCase;

class ApiExceptionTest extends TestCase
{
    public function testExceptionContainsResponse(): void
    {
        $body = json_encode([
            'status' => 400,
            'message' => 'Invalid order',
            'response' => ['errors' => ['Missing service_id']],
        ]);

        $response = new Response(200, $body);
        $exception = new ApiException($response);

        $this->assertSame('Invalid order', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($response, $exception->getResponse());
        $this->assertSame(['errors' => ['Missing service_id']], $exception->getResponseData());
    }
}
