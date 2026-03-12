<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class ValuationOption
{
    private int $optionId;
    private string $name;
    private int $price;
    private int $priceGross;

    private function __construct(
        int $optionId,
        string $name,
        int $price,
        int $priceGross
    ) {
        $this->optionId = $optionId;
        $this->name = $name;
        $this->price = $price;
        $this->priceGross = $priceGross;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['option_id'] ?? 0),
            (string) ($data['name'] ?? ''),
            (int) ($data['price'] ?? 0),
            (int) ($data['price_gross'] ?? 0)
        );
    }

    public function getOptionId(): int
    {
        return $this->optionId;
    }

    public function getName(): string
    {
        return $this->name;
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
