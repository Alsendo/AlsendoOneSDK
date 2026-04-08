<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

use AlsendoOne\SDK\Type\Service;

class ValuationPrice
{
    private Service $service;
    private int $price;
    private int $priceGross;
    /** @var ValuationOption[] */
    private array $options;
    /** @var ValuationShipment[] */
    private array $shipments;

    /**
     * @param ValuationOption[] $options
     * @param ValuationShipment[] $shipments
     */
    private function __construct(
        Service $service,
        int $price,
        int $priceGross,
        array $options,
        array $shipments
    ) {
        $this->service = $service;
        $this->price = $price;
        $this->priceGross = $priceGross;
        $this->options = $options;
        $this->shipments = $shipments;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(Service $service, array $data): self
    {
        $options = [];
        if (isset($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $option) {
                $options[] = ValuationOption::fromArray($option);
            }
        }

        $shipments = [];
        if (isset($data['shipments']) && is_array($data['shipments'])) {
            foreach ($data['shipments'] as $shipment) {
                $shipments[] = ValuationShipment::fromArray($shipment);
            }
        }

        return new self(
            $service,
            (int) ($data['price'] ?? 0),
            (int) ($data['price_gross'] ?? 0),
            $options,
            $shipments
        );
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getPriceGross(): int
    {
        return $this->priceGross;
    }

    /**
     * @return ValuationOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return ValuationShipment[]
     */
    public function getShipments(): array
    {
        return $this->shipments;
    }
}
