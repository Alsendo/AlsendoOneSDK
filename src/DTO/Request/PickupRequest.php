<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Request;

class PickupRequest
{
    private string $type;
    private ?string $date;
    private ?string $hoursFrom;
    private ?string $hoursTo;

    public function __construct(
        string $type,
        ?string $date = null,
        ?string $hoursFrom = null,
        ?string $hoursTo = null
    ) {
        $this->type = $type;
        $this->date = $date;
        $this->hoursFrom = $hoursFrom;
        $this->hoursTo = $hoursTo;
    }

    public static function create(
        string $type,
        ?string $date = null,
        ?string $hoursFrom = null,
        ?string $hoursTo = null
    ): self {
        return new self($type, $date, $hoursFrom, $hoursTo);
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function setHoursFrom(?string $hoursFrom): self
    {
        $this->hoursFrom = $hoursFrom;

        return $this;
    }

    public function setHoursTo(?string $hoursTo): self
    {
        $this->hoursTo = $hoursTo;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'date' => $this->date,
            'hours_from' => $this->hoursFrom,
            'hours_to' => $this->hoursTo,
        ], static function ($value): bool {
            return $value !== null;
        });
    }
}
