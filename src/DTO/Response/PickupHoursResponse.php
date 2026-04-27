<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class PickupHoursResponse
{
    private string $postalCode;
    /** @var PickupHoursDay[] */
    private array $hours;

    /**
     * @param PickupHoursDay[] $hours
     */
    private function __construct(
        string $postalCode,
        array $hours
    ) {
        $this->postalCode = $postalCode;
        $this->hours = $hours;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $hours = [];

        $rawHours = $data['hours'] ?? [];
        if (is_array($rawHours)) {
            foreach ($rawHours as $date => $entry) {
                $hours[$date] = PickupHoursDay::fromArray($entry);
            }
        }

        return new self(
            (string) ($data['postal_code'] ?? ''),
            $hours
        );
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @return PickupHoursDay[]
     */
    public function getHours(): array
    {
        return $this->hours;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $hours = [];
        foreach ($this->hours as $date => $day) {
            $hours[$date] = $day->toArray();
        }
        return [
            'postal_code' => $this->postalCode,
            'hours' => $hours,
        ];
    }
}
