<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Webhook;

/**
 * Verified payload of a push-tracking webhook.
 */
class PushTrackingNotification
{
    private string $orderNumber;
    private string $trackingNumber;
    private string $operator;
    /** @var PushTrackingStatus[] */
    private array $statuses;

    /**
     * @param PushTrackingStatus[] $statuses
     */
    private function __construct(
        string $orderNumber,
        string $trackingNumber,
        string $operator,
        array $statuses
    ) {
        $this->orderNumber = $orderNumber;
        $this->trackingNumber = $trackingNumber;
        $this->operator = $operator;
        $this->statuses = $statuses;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $statuses = [];
        foreach (($data['statuses'] ?? []) as $status) {
            if (is_array($status)) {
                $statuses[] = PushTrackingStatus::fromArray($status);
            }
        }

        return new self(
            (string) ($data['orderNumber'] ?? ''),
            (string) ($data['trackingNumber'] ?? ''),
            (string) ($data['operator'] ?? ''),
            $statuses
        );
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * Supplier code (e.g. "UPS", "DPD").
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Status changes, may be empty for keep-alive style notifications.
     *
     * @return PushTrackingStatus[]
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }
}
