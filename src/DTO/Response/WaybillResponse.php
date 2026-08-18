<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class WaybillResponse
{
    private string $waybill;
    private string $type;

    private function __construct(
        string $waybill,
        string $type
    ) {
        $this->waybill = $waybill;
        $this->type = $type;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['waybill'] ?? ''),
            (string) ($data['type'] ?? '')
        );
    }

    public function getWaybill(): string
    {
        return $this->waybill;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
