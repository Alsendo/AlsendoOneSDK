<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class DispatchCodeResponse
{
    private int $orderId;
    private ?string $dispatchCode;

    private function __construct(
        int $orderId,
        ?string $dispatchCode
    ) {
        $this->orderId = $orderId;
        $this->dispatchCode = $dispatchCode;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dispatchCode = $data['dispatch_code'] ?? null;

        return new self(
            (int) ($data['order_id'] ?? 0),
            $dispatchCode === null ? null : (string) $dispatchCode
        );
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getDispatchCode(): ?string
    {
        return $this->dispatchCode;
    }
}