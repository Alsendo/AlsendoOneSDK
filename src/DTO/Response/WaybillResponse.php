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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $raw = $this->waybill;
        return [
            'type' => $this->type,
            'size_bytes' => strlen(base64_decode($raw) ?: $raw),
            'waybill_base64_preview' => substr($raw, 0, 60) . (strlen($raw) > 60 ? '...[truncated]' : ''),
        ];
    }
}
