<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class PackageType
{
    private string $type;
    private string $desc;

    private function __construct(
        string $type,
        string $desc
    ) {
        $this->type = $type;
        $this->desc = $desc;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['type'] ?? ''),
            (string) ($data['desc'] ?? '')
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }
}
