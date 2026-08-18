<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Request;

use AlsendoOne\SDK\DTO\Address;
use AlsendoOne\SDK\Enum\Service;

class OrderRequest
{
    private ?Service $service = null;
    private ?Address $senderAddress = null;
    private ?Address $receiverAddress = null;
    /** @var ShipmentRequest[] */
    private array $shipments = [];
    private ?PickupRequest $pickup = null;
    private ?NotificationRequest $notification = null;
    private ?CodRequest $cod = null;
    /** @var array<string, mixed> */
    private array $options = [];
    private ?int $shipmentValue = null;
    private ?string $content = null;
    private ?string $comment = null;
    private ?bool $isZebra = null;
    private ?string $pushTrackingUrl = null;

    public static function create(): self
    {
        return new self();
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function setSenderAddress(?Address $senderAddress): self
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    public function setReceiverAddress(?Address $receiverAddress): self
    {
        $this->receiverAddress = $receiverAddress;

        return $this;
    }

    /**
     * @param ShipmentRequest[] $shipments
     */
    public function setShipments(array $shipments): self
    {
        $this->shipments = $shipments;

        return $this;
    }

    public function addShipment(ShipmentRequest $shipment): self
    {
        $this->shipments[] = $shipment;

        return $this;
    }

    public function setPickup(?PickupRequest $pickup): self
    {
        $this->pickup = $pickup;

        return $this;
    }

    public function setNotification(?NotificationRequest $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    public function setCod(?CodRequest $cod): self
    {
        $this->cod = $cod;

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function setOption(string $name, $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function setShipmentValue(?int $shipmentValue): self
    {
        $this->shipmentValue = $shipmentValue;

        return $this;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function setIsZebra(?bool $isZebra): self
    {
        $this->isZebra = $isZebra;

        return $this;
    }

    public function setPushTrackingUrl(?string $pushTrackingUrl): self
    {
        $this->pushTrackingUrl = $pushTrackingUrl;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'service_id' => $this->service?->value,
            'address' => ($this->senderAddress !== null || $this->receiverAddress !== null)
                ? array_filter([
                    'sender' => $this->senderAddress !== null ? $this->senderAddress->toArray() : null,
                    'receiver' => $this->receiverAddress !== null ? $this->receiverAddress->toArray() : null,
                ], static function ($value): bool {
                    return $value !== null;
                })
                : null,
            'shipment' => !empty($this->shipments)
                ? array_map(static function (ShipmentRequest $s): array {
                    return $s->toArray();
                }, $this->shipments)
                : null,
            'pickup' => $this->pickup !== null ? $this->pickup->toArray() : null,
            'notification' => $this->notification !== null ? $this->notification->toArray() : null,
            'cod' => $this->cod !== null ? $this->cod->toArray() : null,
            'option' => !empty($this->options) ? $this->options : null,
            'shipment_value' => $this->shipmentValue,
            'content' => $this->content,
            'comment' => $this->comment,
            'is_zebra' => $this->isZebra,
            'push_tracking_url' => $this->pushTrackingUrl,
        ];

        return array_filter($data, static function ($value): bool {
            return $value !== null;
        });
    }
}
