<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

/**
 * Result of customer_register/ — API credentials provisioned for the
 * newly created customer account.
 */
class CustomerRegisterResponse
{
    private string $appId;
    private string $appSecret;

    private function __construct(string $appId, string $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['app_id'] ?? ''),
            (string) ($data['app_secret'] ?? '')
        );
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }
}
