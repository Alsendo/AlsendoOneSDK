<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Request;

class CustomsDataRequest
{
    private string $description;
    private int $quantity;
    private int $unitPrice;
    private int $weight;
    private ?string $hsCode;
    private ?string $countryOfOrigin;

    public function __construct(
        string $description,
        int $quantity,
        int $unitPrice,
        int $weight,
        ?string $hsCode = null,
        ?string $countryOfOrigin = null
    ) {
        $this->description = $description;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->weight = $weight;
        $this->hsCode = $hsCode;
        $this->countryOfOrigin = $countryOfOrigin;
    }

    public static function create(
        string $description,
        int $quantity,
        int $unitPrice,
        int $weight,
        ?string $hsCode = null,
        ?string $countryOfOrigin = null
    ): self {
        return new self($description, $quantity, $unitPrice, $weight, $hsCode, $countryOfOrigin);
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function setUnitPrice(int $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function setHsCode(?string $hsCode): self
    {
        $this->hsCode = $hsCode;

        return $this;
    }

    public function setCountryOfOrigin(?string $countryOfOrigin): self
    {
        $this->countryOfOrigin = $countryOfOrigin;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'weight' => $this->weight,
            'hs_code' => $this->hsCode,
            'country_of_origin' => $this->countryOfOrigin,
        ], static function ($value): bool {
            return $value !== null;
        });
    }
}
