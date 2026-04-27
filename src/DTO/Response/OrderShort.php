<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

use AlsendoOne\SDK\DTO\Address;
use AlsendoOne\SDK\Type\Service;

class OrderShort
{
    private int $id;
    private Service $service;
    private string $serviceName;
    private string $waybillNumber;
    private ?string $pickupNumber;
    private string $trackingUrl;
    private string $status;
    private int $shipmentsCount;
    private string $content;
    private string $comment;
    private Address $receiver;
    private string $created;
    private ?string $delivered;
    private ?string $externalId;
    private string $supplier;

    private function __construct(
        int $id,
        Service $service,
        string $serviceName,
        string $waybillNumber,
        ?string $pickupNumber,
        string $trackingUrl,
        string $status,
        int $shipmentsCount,
        string $content,
        string $comment,
        Address $receiver,
        string $created,
        ?string $delivered,
        ?string $externalId,
        string $supplier
    ) {
        $this->id = $id;
        $this->service = $service;
        $this->serviceName = $serviceName;
        $this->waybillNumber = $waybillNumber;
        $this->pickupNumber = $pickupNumber;
        $this->trackingUrl = $trackingUrl;
        $this->status = $status;
        $this->shipmentsCount = $shipmentsCount;
        $this->content = $content;
        $this->comment = $comment;
        $this->receiver = $receiver;
        $this->created = $created;
        $this->delivered = $delivered;
        $this->externalId = $externalId;
        $this->supplier = $supplier;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['id'] ?? 0),
            Service::from((int) ($data['service_id'] ?? 0)),
            (string) ($data['service_name'] ?? ''),
            (string) ($data['waybill_number'] ?? ''),
            $data['pickup_number'] ?? null,
            (string) ($data['tracking_url'] ?? ''),
            (string) ($data['status'] ?? ''),
            (int) ($data['shipments_count'] ?? 0),
            (string) ($data['content'] ?? ''),
            (string) ($data['comment'] ?? ''),
            Address::fromArray($data['receiver'] ?? []),
            (string) ($data['created'] ?? ''),
            $data['delivered'] ?? null,
            isset($data['externalId']) && $data['externalId'] !== false ? (string) $data['externalId'] : null,
            (string) ($data['supplier'] ?? '')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service->value,
            'service_name' => $this->serviceName,
            'waybill_number' => $this->waybillNumber,
            'pickup_number' => $this->pickupNumber,
            'tracking_url' => $this->trackingUrl,
            'status' => $this->status,
            'shipments_count' => $this->shipmentsCount,
            'content' => $this->content,
            'comment' => $this->comment,
            'receiver' => $this->receiver->toArray(),
            'created' => $this->created,
            'delivered' => $this->delivered,
            'external_id' => $this->externalId,
            'supplier' => $this->supplier,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getWaybillNumber(): string
    {
        return $this->waybillNumber;
    }

    public function getPickupNumber(): ?string
    {
        return $this->pickupNumber;
    }

    public function getTrackingUrl(): string
    {
        return $this->trackingUrl;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getShipmentsCount(): int
    {
        return $this->shipmentsCount;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getReceiver(): Address
    {
        return $this->receiver;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    public function getDelivered(): ?string
    {
        return $this->delivered;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function getSupplier(): string
    {
        return $this->supplier;
    }
}
