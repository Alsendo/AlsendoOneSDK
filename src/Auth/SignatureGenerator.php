<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Auth;

class SignatureGenerator
{
    private string $appId;
    private string $appSecret;

    public function __construct(string $appId, string $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * Generate HMAC-SHA256 signature for API request.
     *
     * @param string $route API route with trailing slash (e.g. "service_structure/")
     * @param string $jsonRequest JSON-encoded request payload
     * @param int $expires Unix timestamp when signature expires
     */
    public function generate(string $route, string $jsonRequest, int $expires): string
    {
        $stringToSign = sprintf('%s:%s:%s:%s', $this->appId, $route, $jsonRequest, $expires);
        return hash_hmac('sha256', $stringToSign, $this->appSecret);
    }

    public function getAppId(): string
    {
        return $this->appId;
    }
}
