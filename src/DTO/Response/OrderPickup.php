<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class OrderPickup
{
    private string $type;
    private string $date;
    private string $hoursFrom;
    private string $hoursTo;
    private ?string $addressId;

    private function __construct(
        string $type,
        string $date,
        string $hoursFrom,
        string $hoursTo,
        ?string $addressId
    ) {
        $this->type = $type;
        $this->date = $date;
        $this->hoursFrom = $hoursFrom;
        $this->hoursTo = $hoursTo;
        $this->addressId = $addressId;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $addressId = $data['address_id'] ?? null;

        return new self(
            (string) ($data['type'] ?? ''),
            (string) ($data['date'] ?? ''),
            (string) ($data['hours_from'] ?? ''),
            (string) ($data['hours_to'] ?? ''),
            $addressId === null ? null : (string) $addressId
        );
    }

    /**
     * Pickup type (e.g. "COURIER", "SELF", "BOX_MACHINE").
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getHoursFrom(): string
    {
        return $this->hoursFrom;
    }

    public function getHoursTo(): string
    {
        return $this->hoursTo;
    }

    public function getAddressId(): ?string
    {
        return $this->addressId;
    }
}
