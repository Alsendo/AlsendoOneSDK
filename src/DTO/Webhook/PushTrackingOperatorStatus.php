<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Webhook;

/**
 * Carrier-level tracking event nested in a push-tracking status change.
 */
class PushTrackingOperatorStatus
{
    private string $status;
    private string $statusDetailed;
    private string $place;
    private string $description;
    private string $updatedAt;

    private function __construct(
        string $status,
        string $statusDetailed,
        string $place,
        string $description,
        string $updatedAt
    ) {
        $this->status = $status;
        $this->statusDetailed = $statusDetailed;
        $this->place = $place;
        $this->description = $description;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['status'] ?? ''),
            (string) ($data['statusDetailed'] ?? ''),
            (string) ($data['place'] ?? ''),
            (string) ($data['description'] ?? ''),
            (string) ($data['updatedAt'] ?? '')
        );
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusDetailed(): string
    {
        return $this->statusDetailed;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Timestamp in Y-m-d\TH:i:s format without an offset, in Europe/Warsaw time.
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
