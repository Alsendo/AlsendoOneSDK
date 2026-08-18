<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class Shipment
{
    private string $shipmentTypeCode;
    /** Weight in kilograms. */
    private float $weight;
    private float $weightBillable;
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
        float $weight,
        float $weightBillable,
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
            (float) ($data['weight'] ?? 0),
            (float) ($data['weight_billable'] ?? 0),
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

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getWeightBillable(): float
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

    /**
     * Net price in grosze (1 PLN = 100 groszy).
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * Shipment-level VAT as serialized under the "price_vat" key.
     */
    public function getPriceVat(): int
    {
        return $this->priceVat;
    }

    /**
     * Gross price in grosze (1 PLN = 100 groszy).
     */
    public function getPriceGross(): int
    {
        return $this->priceGross;
    }
}
