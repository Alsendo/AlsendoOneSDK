<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class TrackingEvent
{
    private string $status;
    private string $statusOriginal;
    private string $description;
    private string $place;
    private string $updatedAt;

    private function __construct(
        string $status,
        string $statusOriginal,
        string $description,
        string $place,
        string $updatedAt
    ) {
        $this->status = $status;
        $this->statusOriginal = $statusOriginal;
        $this->description = $description;
        $this->place = $place;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['status'] ?? ''),
            (string) ($data['status_original'] ?? ''),
            (string) ($data['description'] ?? ''),
            (string) ($data['place'] ?? ''),
            (string) ($data['updated_at'] ?? '')
        );
    }

    /**
     * Normalized status code (e.g. "SENT", "IN_TRANSIT", "DELIVERED").
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Original carrier status text.
     */
    public function getStatusOriginal(): string
    {
        return $this->statusOriginal;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * Event timestamp as returned by the API (observed as ISO 8601 with timezone).
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
