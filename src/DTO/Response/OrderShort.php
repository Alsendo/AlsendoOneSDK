<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

use AlsendoOne\SDK\DTO\Address;
use AlsendoOne\SDK\Type\Service;

class OrderShort
{
    private int $id;
    private int $serviceId;
    private ?Service $service;
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
        int $serviceId,
        ?Service $service,
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
        $this->serviceId = $serviceId;
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
            (int) ($data['service_id'] ?? 0),
            Service::tryFrom((int) ($data['service_id'] ?? 0)),
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
            is_string($data['delivered'] ?? null) ? $data['delivered'] : null,
            // The API serializes externalId as boolean false when absent (CBL-only field).
            is_string($data['externalId'] ?? null) ? $data['externalId'] : null,
            (string) ($data['supplier'] ?? '')
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Raw service id as returned by the API.
     */
    public function getServiceId(): int
    {
        return $this->serviceId;
    }

    /**
     * Typed service enum, or null when the API returns a service id
     * not (yet) known to {@see Service}.
     */
    public function getService(): ?Service
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
