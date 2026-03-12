<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Http;

class Response
{
    private int $httpStatusCode;
    private string $body;
    /** @var array<string, mixed>|null */
    private ?array $decoded = null;

    public function __construct(int $httpStatusCode, string $body)
    {
        $this->httpStatusCode = $httpStatusCode;
        $this->body = $body;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->decoded === null) {
            $this->decoded = json_decode($this->body, true) ?? [];
        }
        return $this->decoded;
    }

    public function getApiStatus(): int
    {
        return (int) ($this->toArray()['status'] ?? 0);
    }

    public function getMessage(): string
    {
        return (string) ($this->toArray()['message'] ?? '');
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        $data = $this->toArray()['response'] ?? [];
        return is_array($data) ? $data : [];
    }

    public function isSuccess(): bool
    {
        return $this->getApiStatus() === 200;
    }
}
