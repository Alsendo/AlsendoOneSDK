<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class Shipment
{
    private string $shipmentTypeCode;
    private int $weight;
    private int $weightBillable;
    private int $length;
    private int $width;
    private int $height;
    private string $content;
    private string $comment;
    private string $waybillNumber;
    private bool $isNstd;
    private int $price;
    private int $priceVat;
    private int $priceGross;

    private function __construct(
        string $shipmentTypeCode,
        int $weight,
        int $weightBillable,
        int $length,
        int $width,
        int $height,
        string $content,
        string $comment,
        string $waybillNumber,
        bool $isNstd,
        int $price,
        int $priceVat,
        int $priceGross
    ) {
        $this->shipmentTypeCode = $shipmentTypeCode;
        $this->weight = $weight;
        $this->weightBillable = $weightBillable;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->content = $content;
        $this->comment = $comment;
        $this->waybillNumber = $waybillNumber;
        $this->isNstd = $isNstd;
        $this->price = $price;
        $this->priceVat = $priceVat;
        $this->priceGross = $priceGross;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['shipment_type_code'] ?? ''),
            (int) ($data['weight'] ?? 0),
            (int) ($data['weight_billable'] ?? 0),
            (int) ($data['length'] ?? 0),
            (int) ($data['width'] ?? 0),
            (int) ($data['height'] ?? 0),
            (string) ($data['content'] ?? ''),
            (string) ($data['comment'] ?? ''),
            (string) ($data['waybill_number'] ?? ''),
            (bool) ($data['is_nstd'] ?? false),
            (int) ($data['price'] ?? 0),
            (int) ($data['price_vat'] ?? 0),
            (int) ($data['price_gross'] ?? 0)
        );
    }

    public function getShipmentTypeCode(): string
    {
        return $this->shipmentTypeCode;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getWeightBillable(): int
    {
        return $this->weightBillable;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getWaybillNumber(): string
    {
        return $this->waybillNumber;
    }

    public function isNstd(): bool
    {
        return $this->isNstd;
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
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'shipment_type_code' => $this->shipmentTypeCode,
            'weight' => $this->weight,
            'weight_billable' => $this->weightBillable,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'content' => $this->content,
            'comment' => $this->comment,
            'waybill_number' => $this->waybillNumber,
            'is_nstd' => $this->isNstd,
            'price' => $this->price,
            'price_vat' => $this->priceVat,
            'price_gross' => $this->priceGross,
        ];
    }
}
