<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO;

class Address
{
    private ?string $name;
    private ?string $contactPerson;
    private ?string $email;
    private ?string $phone;
    private ?string $line1;
    private ?string $line2;
    private ?string $postalCode;
    private ?string $city;
    private ?string $countryCode;
    private ?string $foreignAddressId;
    private ?string $stateCode;
    private ?bool $isResidential;

    public function __construct(
        ?string $name = null,
        ?string $contactPerson = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $line1 = null,
        ?string $line2 = null,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $countryCode = null,
        ?string $foreignAddressId = null,
        ?string $stateCode = null,
        ?bool $isResidential = null
    ) {
        $this->name = $name;
        $this->contactPerson = $contactPerson;
        $this->email = $email;
        $this->phone = $phone;
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->countryCode = $countryCode;
        $this->foreignAddressId = $foreignAddressId;
        $this->stateCode = $stateCode;
        $this->isResidential = $isResidential;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['name']) ? (string) $data['name'] : null,
            isset($data['contact_person']) ? (string) $data['contact_person'] : null,
            isset($data['email']) ? (string) $data['email'] : null,
            isset($data['phone']) ? (string) $data['phone'] : null,
            isset($data['line1']) ? (string) $data['line1'] : null,
            isset($data['line2']) ? (string) $data['line2'] : null,
            isset($data['postal_code']) ? (string) $data['postal_code'] : null,
            isset($data['city']) ? (string) $data['city'] : null,
            isset($data['country_code']) ? (string) $data['country_code'] : null,
            isset($data['foreign_address_id']) ? (string) $data['foreign_address_id'] : null,
            isset($data['state_code']) ? (string) $data['state_code'] : null,
            isset($data['is_residential']) ? (bool) $data['is_residential'] : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'contact_person' => $this->contactPerson,
            'email' => $this->email,
            'phone' => $this->phone,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'postal_code' => $this->postalCode,
            'city' => $this->city,
            'country_code' => $this->countryCode,
            'foreign_address_id' => $this->foreignAddressId,
            'state_code' => $this->stateCode,
            'is_residential' => $this->isResidential,
        ], static function ($value): bool {
            return $value !== null;
        });
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getForeignAddressId(): ?string
    {
        return $this->foreignAddressId;
    }

    /**
     * State/region code (max 10 chars) — required by some destinations, e.g. the US.
     */
    public function getStateCode(): ?string
    {
        return $this->stateCode;
    }

    public function getIsResidential(): ?bool
    {
        return $this->isResidential;
    }
}
