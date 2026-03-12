<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class ValuationShipment
{
    private int $price;
    private int $priceGross;

    private function __construct(
        int $price,
        int $priceGross
    ) {
        $this->price = $price;
        $this->priceGross = $priceGross;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['price'] ?? 0),
            (int) ($data['price_gross'] ?? 0)
        );
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getPriceGross(): int
    {
        return $this->priceGross;
    }
}
