<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class PickupHoursDay
{
    private string $date;
    /** @var array<string, mixed> */
    private array $services;

    /**
     * @param array<string, mixed> $services
     */
    private function __construct(
        string $date,
        array $services
    ) {
        $this->date = $date;
        $this->services = $services;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['date'] ?? ''),
            $data['services'] ?? []
        );
    }

    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return array<string, mixed>
     */
    public function getServices(): array
    {
        return $this->services;
    }
}
