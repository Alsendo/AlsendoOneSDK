<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class ValuationPrice
{
    private int $serviceId;
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
        int $serviceId,
        int $price,
        int $priceGross,
        array $options,
        array $shipments
    ) {
        $this->serviceId = $serviceId;
        $this->price = $price;
        $this->priceGross = $priceGross;
        $this->options = $options;
        $this->shipments = $shipments;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(int $serviceId, array $data): self
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
            $serviceId,
            (int) ($data['price'] ?? 0),
            (int) ($data['price_gross'] ?? 0),
            $options,
            $shipments
        );
    }

    public function getServiceId(): int
    {
        return $this->serviceId;
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
