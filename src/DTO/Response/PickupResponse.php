<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class PickupResponse
{
    private string $foreignCourierId;

    private function __construct(string $foreignCourierId)
    {
        $this->foreignCourierId = $foreignCourierId;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['foreign_courier_id'] ?? '')
        );
    }

    public function getForeignCourierId(): string
    {
        return $this->foreignCourierId;
    }
}
