<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class ServiceOption
{
    private string $type;
    private string $name;
    private string $desc;

    private function __construct(
        string $type,
        string $name,
        string $desc
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->desc = $desc;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['type'] ?? ''),
            (string) ($data['name'] ?? ''),
            (string) ($data['desc'] ?? '')
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }
}
