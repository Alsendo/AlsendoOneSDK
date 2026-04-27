<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

use AlsendoOne\SDK\DTO\PointAddress;

class AccessPoint
{
    private string $type;
    private string $subtype;
    private string $name;
    private string $foreignAddressId;
    private PointAddress $address;
    private ?string $imageUrl;
    /** @var mixed */
    private $openHours;
    private bool $optionCod;
    private bool $optionSend;
    private bool $optionDeliver;
    private string $additionalInfo;
    private int $distance;

    /**
     * @param mixed $openHours
     */
    private function __construct(
        string $type,
        string $subtype,
        string $name,
        string $foreignAddressId,
        PointAddress $address,
        ?string $imageUrl,
        $openHours,
        bool $optionCod,
        bool $optionSend,
        bool $optionDeliver,
        string $additionalInfo,
        int $distance
    ) {
        $this->type = $type;
        $this->subtype = $subtype;
        $this->name = $name;
        $this->foreignAddressId = $foreignAddressId;
        $this->address = $address;
        $this->imageUrl = $imageUrl;
        $this->openHours = $openHours;
        $this->optionCod = $optionCod;
        $this->optionSend = $optionSend;
        $this->optionDeliver = $optionDeliver;
        $this->additionalInfo = $additionalInfo;
        $this->distance = $distance;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['type'] ?? ''),
            (string) ($data['subtype'] ?? ''),
            (string) ($data['name'] ?? ''),
            (string) ($data['foreign_address_id'] ?? ''),
            PointAddress::fromArray($data['address'] ?? []),
            isset($data['image_url']) ? (string) $data['image_url'] : null,
            $data['open_hours'] ?? null,
            (bool) ($data['option_cod'] ?? false),
            (bool) ($data['option_send'] ?? false),
            (bool) ($data['option_deliver'] ?? false),
            (string) ($data['additional_info'] ?? ''),
            (int) ($data['distance'] ?? 0)
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubtype(): string
    {
        return $this->subtype;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getForeignAddressId(): string
    {
        return $this->foreignAddressId;
    }

    public function getAddress(): PointAddress
    {
        return $this->address;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @return mixed
     */
    public function getOpenHours()
    {
        return $this->openHours;
    }

    public function isOptionCod(): bool
    {
        return $this->optionCod;
    }

    public function isOptionSend(): bool
    {
        return $this->optionSend;
    }

    public function isOptionDeliver(): bool
    {
        return $this->optionDeliver;
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'subtype' => $this->subtype,
            'name' => $this->name,
            'foreign_address_id' => $this->foreignAddressId,
            'address' => $this->address->toArray(),
            'image_url' => $this->imageUrl,
            'open_hours' => $this->openHours,
            'option_cod' => $this->optionCod,
            'option_send' => $this->optionSend,
            'option_deliver' => $this->optionDeliver,
            'additional_info' => $this->additionalInfo,
            'distance' => $this->distance,
        ];
    }
}
