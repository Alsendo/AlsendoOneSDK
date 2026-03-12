<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Auth;

use AlsendoOne\SDK\Auth\SignatureGenerator;
use PHPUnit\Framework\TestCase;

class SignatureGeneratorTest extends TestCase
{
    private SignatureGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new SignatureGenerator('test_app_id', 'test_app_secret');
    }

    public function testGenerateReturnsHmacSha256Hash(): void
    {
        $signature = $this->generator->generate('service_structure/', '{}', 1700000000);

        // Verify it's a valid hex hash (64 chars for SHA-256)
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $signature);
    }

    public function testGenerateIsConsistentForSameInput(): void
    {
        $sig1 = $this->generator->generate('orders/', '{"page":1}', 1700000000);
        $sig2 = $this->generator->generate('orders/', '{"page":1}', 1700000000);

        $this->assertSame($sig1, $sig2);
    }

    public function testGenerateProducesCorrectSignature(): void
    {
        // Manually compute expected: hash_hmac('sha256', 'test_app_id:service_structure/:{}:1700000000', 'test_app_secret')
        $expected = hash_hmac('sha256', 'test_app_id:service_structure/:{}:1700000000', 'test_app_secret');

        $signature = $this->generator->generate('service_structure/', '{}', 1700000000);

        $this->assertSame($expected, $signature);
    }

    public function testDifferentRouteProducesDifferentSignature(): void
    {
        $sig1 = $this->generator->generate('orders/', '{}', 1700000000);
        $sig2 = $this->generator->generate('service_structure/', '{}', 1700000000);

        $this->assertNotSame($sig1, $sig2);
    }

    public function testDifferentExpiresProducesDifferentSignature(): void
    {
        $sig1 = $this->generator->generate('orders/', '{}', 1700000000);
        $sig2 = $this->generator->generate('orders/', '{}', 1700000001);

        $this->assertNotSame($sig1, $sig2);
    }

    public function testDifferentPayloadProducesDifferentSignature(): void
    {
        $sig1 = $this->generator->generate('orders/', '{"page":1}', 1700000000);
        $sig2 = $this->generator->generate('orders/', '{"page":2}', 1700000000);

        $this->assertNotSame($sig1, $sig2);
    }

    public function testTrailingSlashMatters(): void
    {
        $sig1 = $this->generator->generate('orders/', '{}', 1700000000);
        $sig2 = $this->generator->generate('orders', '{}', 1700000000);

        $this->assertNotSame($sig1, $sig2);
    }

    public function testGetAppIdReturnsConfiguredId(): void
    {
        $this->assertSame('test_app_id', $this->generator->getAppId());
    }
}
