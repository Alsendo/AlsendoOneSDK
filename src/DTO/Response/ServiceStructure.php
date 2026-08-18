<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class ServiceStructure
{
    /** @var array<int|string, array<string, mixed>> */
    private array $services;
    /** @var array<string, ServiceOption> */
    private array $options;
    /** @var array<string, PackageType> */
    private array $packageTypes;
    /** @var string[] */
    private array $pointsTypes;
    /** @var array<string, PickupType> */
    private array $pickupTypes;
    /** @var array<string, UnitType> */
    private array $unitTypes;

    /**
     * @param array<int|string, array<string, mixed>> $services
     * @param array<string, ServiceOption> $options
     * @param array<string, PackageType> $packageTypes
     * @param string[] $pointsTypes
     * @param array<string, PickupType> $pickupTypes
     * @param array<string, UnitType> $unitTypes
     */
    private function __construct(
        array $services,
        array $options,
        array $packageTypes,
        array $pointsTypes,
        array $pickupTypes,
        array $unitTypes
    ) {
        $this->services = $services;
        $this->options = $options;
        $this->packageTypes = $packageTypes;
        $this->pointsTypes = $pointsTypes;
        $this->pickupTypes = $pickupTypes;
        $this->unitTypes = $unitTypes;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $options = [];
        foreach (($data['options'] ?? []) as $key => $option) {
            $options[(string) $key] = ServiceOption::fromArray($option);
        }

        $packageTypes = [];
        foreach (($data['package_type'] ?? []) as $key => $packageType) {
            $packageTypes[(string) $key] = PackageType::fromArray($packageType);
        }

        $pickupTypes = [];
        foreach (($data['pickup_type'] ?? []) as $key => $pickupType) {
            $pickupTypes[(string) $key] = PickupType::fromArray($pickupType);
        }

        $unitTypes = [];
        foreach (($data['unit_type'] ?? []) as $key => $unitType) {
            $unitTypes[(string) $key] = UnitType::fromArray($unitType);
        }

        return new self(
            (array) ($data['services'] ?? []),
            $options,
            $packageTypes,
            array_values((array) ($data['points_type'] ?? [])),
            $pickupTypes,
            $unitTypes
        );
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return array<string, ServiceOption>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array<string, PackageType>
     */
    public function getPackageTypes(): array
    {
        return $this->packageTypes;
    }

    /**
     * @return string[]
     */
    public function getPointsTypes(): array
    {
        return $this->pointsTypes;
    }

    /**
     * @return array<string, PickupType>
     */
    public function getPickupTypes(): array
    {
        return $this->pickupTypes;
    }

    /**
     * @return array<string, UnitType>
     */
    public function getUnitTypes(): array
    {
        return $this->unitTypes;
    }
}
