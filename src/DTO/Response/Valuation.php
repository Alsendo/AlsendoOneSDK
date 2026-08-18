<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

use AlsendoOne\SDK\Type\Service;

class Valuation
{
    /** @var array<int, ValuationPrice> */
    private array $priceTable;

    /**
     * @param array<int, ValuationPrice> $priceTable
     */
    private function __construct(array $priceTable)
    {
        $this->priceTable = $priceTable;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $priceTable = [];

        $rawTable = $data['price_table'] ?? [];
        if (is_array($rawTable)) {
            foreach ($rawTable as $serviceId => $entry) {
                $priceTable[(int) $serviceId] = ValuationPrice::fromArray((int) $serviceId, $entry);
            }
        }

        return new self($priceTable);
    }

    /**
     * @return array<int, ValuationPrice>
     */
    public function getPriceTable(): array
    {
        return $this->priceTable;
    }

    public function getPriceForService(Service $service): ?ValuationPrice
    {
        return $this->priceTable[$service->value] ?? null;
    }
}
