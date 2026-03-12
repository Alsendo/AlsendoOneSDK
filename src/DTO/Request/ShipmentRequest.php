<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Request;

class ShipmentRequest
{
    private string $shipmentTypeCode;
    private int $weight;
    private int $length;
    private int $width;
    private int $height;
    private ?string $content;
    private ?string $comment;
    /** @var CustomsDataRequest[]|null */
    private ?array $customsData;

    /**
     * @param CustomsDataRequest[]|null $customsData
     */
    public function __construct(
        string $shipmentTypeCode,
        int $weight,
        int $length,
        int $width,
        int $height,
        ?string $content = null,
        ?string $comment = null,
        ?array $customsData = null
    ) {
        $this->shipmentTypeCode = $shipmentTypeCode;
        $this->weight = $weight;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->content = $content;
        $this->comment = $comment;
        $this->customsData = $customsData;
    }

    public static function create(
        string $shipmentTypeCode,
        int $weight,
        int $length,
        int $width,
        int $height
    ): self {
        return new self($shipmentTypeCode, $weight, $length, $width, $height);
    }

    public function setShipmentTypeCode(string $shipmentTypeCode): self
    {
        $this->shipmentTypeCode = $shipmentTypeCode;

        return $this;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

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

    /**
     * @param CustomsDataRequest[]|null $customsData
     */
    public function setCustomsData(?array $customsData): self
    {
        $this->customsData = $customsData;

        return $this;
    }

    public function addCustomsData(CustomsDataRequest $customsData): self
    {
        if ($this->customsData === null) {
            $this->customsData = [];
        }
        $this->customsData[] = $customsData;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'shipment_type_code' => $this->shipmentTypeCode,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'content' => $this->content,
            'comment' => $this->comment,
            'customs_data' => $this->customsData !== null
                ? array_map(static function (CustomsDataRequest $item): array {
                    return $item->toArray();
                }, $this->customsData)
                : null,
        ];

        return array_filter($data, static function ($value): bool {
            return $value !== null;
        });
    }
}
