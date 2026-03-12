<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

use AlsendoOne\SDK\DTO\Address;
use AlsendoOne\SDK\Enum\Service;

class Order
{
    private int $id;
    private string $supplier;
    private Service $service;
    private string $serviceName;
    private string $waybillNumber;
    private ?string $pickup;
    private ?string $pickupNumber;
    private string $trackingUrl;
    private string $status;
    private int $shipmentsCount;
    /** @var Shipment[] */
    private array $shipments;
    private string $content;
    private string $comment;
    private Address $sender;
    private Address $receiver;
    private string $created;
    private ?string $delivered;
    private int $price;
    private int $priceVat;
    private int $priceGross;
    /** @var int|false */
    private $cod;
    private ?string $codCurrency;
    /** @var int|false */
    private $declarationValue;
    private ?string $externalId;

    /**
     * @param Shipment[] $shipments
     * @param int|false $cod
     * @param int|false $declarationValue
     */
    private function __construct(
        int $id,
        string $supplier,
        Service $service,
        string $serviceName,
        string $waybillNumber,
        ?string $pickup,
        ?string $pickupNumber,
        string $trackingUrl,
        string $status,
        int $shipmentsCount,
        array $shipments,
        string $content,
        string $comment,
        Address $sender,
        Address $receiver,
        string $created,
        ?string $delivered,
        int $price,
        int $priceVat,
        int $priceGross,
        $cod,
        ?string $codCurrency,
        $declarationValue,
        ?string $externalId
    ) {
        $this->id = $id;
        $this->supplier = $supplier;
        $this->service = $service;
        $this->serviceName = $serviceName;
        $this->waybillNumber = $waybillNumber;
        $this->pickup = $pickup;
        $this->pickupNumber = $pickupNumber;
        $this->trackingUrl = $trackingUrl;
        $this->status = $status;
        $this->shipmentsCount = $shipmentsCount;
        $this->shipments = $shipments;
        $this->content = $content;
        $this->comment = $comment;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->created = $created;
        $this->delivered = $delivered;
        $this->price = $price;
        $this->priceVat = $priceVat;
        $this->priceGross = $priceGross;
        $this->cod = $cod;
        $this->codCurrency = $codCurrency;
        $this->declarationValue = $declarationValue;
        $this->externalId = $externalId;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $shipments = [];
        foreach (($data['shipments'] ?? []) as $shipmentData) {
            $shipments[] = Shipment::fromArray($shipmentData);
        }

        $cod = $data['cod'] ?? false;
        $declarationValue = $data['declaration_value'] ?? false;

        return new self(
            (int) ($data['id'] ?? 0),
            (string) ($data['supplier'] ?? ''),
            Service::from((int) ($data['service_id'] ?? 0)),
            (string) ($data['service_name'] ?? ''),
            (string) ($data['waybill_number'] ?? ''),
            $data['pickup'] ?? null,
            $data['pickup_number'] ?? null,
            (string) ($data['tracking_url'] ?? ''),
            (string) ($data['status'] ?? ''),
            (int) ($data['shipments_count'] ?? 0),
            $shipments,
            (string) ($data['content'] ?? ''),
            (string) ($data['comment'] ?? ''),
            Address::fromArray($data['sender'] ?? []),
            Address::fromArray($data['receiver'] ?? []),
            (string) ($data['created'] ?? ''),
            $data['delivered'] ?? null,
            (int) ($data['price'] ?? 0),
            (int) ($data['price_var'] ?? 0),
            (int) ($data['price_gross'] ?? 0),
            $cod !== false ? (int) $cod : false,
            $data['cod_currency'] ?? null,
            $declarationValue !== false ? (int) $declarationValue : false,
            $data['externalId'] ?? null
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSupplier(): string
    {
        return $this->supplier;
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

    public function getPickup(): ?string
    {
        return $this->pickup;
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

    /**
     * @return Shipment[]
     */
    public function getShipments(): array
    {
        return $this->shipments;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getSender(): Address
    {
        return $this->sender;
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

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getPriceVat(): int
    {
        return $this->priceVat;
    }

    public function getPriceGross(): int
    {
        return $this->priceGross;
    }

    /**
     * @return int|false
     */
    public function getCod()
    {
        return $this->cod;
    }

    public function getCodCurrency(): ?string
    {
        return $this->codCurrency;
    }

    /**
     * @return int|false
     */
    public function getDeclarationValue()
    {
        return $this->declarationValue;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
