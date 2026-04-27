<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO;

class PointAddress
{
    private ?string $line1;
    private ?string $line2;
    private ?string $street;
    private ?string $houseNumber;
    private ?string $flatNumber;
    private ?string $postalCode;
    private ?string $city;
    private ?string $stateCode;
    private ?string $countryCode;
    private ?float $longitude;
    private ?float $latitude;

    private function __construct(
        ?string $line1,
        ?string $line2,
        ?string $street,
        ?string $houseNumber,
        ?string $flatNumber,
        ?string $postalCode,
        ?string $city,
        ?string $stateCode,
        ?string $countryCode,
        ?float $longitude,
        ?float $latitude
    ) {
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->flatNumber = $flatNumber;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->stateCode = $stateCode;
        $this->countryCode = $countryCode;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['line1']) ? (string) $data['line1'] : null,
            isset($data['line2']) ? (string) $data['line2'] : null,
            isset($data['street']) ? (string) $data['street'] : null,
            isset($data['house_number']) ? (string) $data['house_number'] : null,
            isset($data['flat_number']) ? (string) $data['flat_number'] : null,
            isset($data['postal_code']) ? (string) $data['postal_code'] : null,
            isset($data['city']) ? (string) $data['city'] : null,
            isset($data['state_code']) ? (string) $data['state_code'] : null,
            isset($data['country_code']) ? (string) $data['country_code'] : null,
            isset($data['longitude']) ? (float) $data['longitude'] : null,
            isset($data['latitude']) ? (float) $data['latitude'] : null
        );
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function getFlatNumber(): ?string
    {
        return $this->flatNumber;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStateCode(): ?string
    {
        return $this->stateCode;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'line1' => $this->line1,
            'line2' => $this->line2,
            'street' => $this->street,
            'house_number' => $this->houseNumber,
            'flat_number' => $this->flatNumber,
            'postal_code' => $this->postalCode,
            'city' => $this->city,
            'state_code' => $this->stateCode,
            'country_code' => $this->countryCode,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        ], static fn ($v): bool => $v !== null);
    }
}
