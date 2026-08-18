<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Webhook;

/**
 * Normalized status change delivered by a push-tracking webhook.
 */
class PushTrackingStatus
{
    private string $status;
    private string $updatedAt;
    /** @var PushTrackingOperatorStatus[] */
    private array $operatorStatuses;

    /**
     * @param PushTrackingOperatorStatus[] $operatorStatuses
     */
    private function __construct(string $status, string $updatedAt, array $operatorStatuses)
    {
        $this->status = $status;
        $this->updatedAt = $updatedAt;
        $this->operatorStatuses = $operatorStatuses;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $operatorStatuses = [];
        foreach (($data['trackingOperatorStatuses'] ?? []) as $operatorStatus) {
            if (is_array($operatorStatus)) {
                $operatorStatuses[] = PushTrackingOperatorStatus::fromArray($operatorStatus);
            }
        }

        return new self(
            (string) ($data['status'] ?? ''),
            (string) ($data['updatedAt'] ?? ''),
            $operatorStatuses
        );
    }

    /**
     * Normalized status (e.g. "ON_THE_WAY", "DELIVERED", "RETURNED").
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Timestamp in Y-m-d\TH:i:s format without an offset, in Europe/Warsaw time.
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @return PushTrackingOperatorStatus[]
     */
    public function getOperatorStatuses(): array
    {
        return $this->operatorStatuses;
    }
}
