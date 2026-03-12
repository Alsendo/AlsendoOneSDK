<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class TurnInResponse
{
    private string $turnIn;

    private function __construct(string $turnIn)
    {
        $this->turnIn = $turnIn;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['turn_in'] ?? '')
        );
    }

    public function getTurnIn(): string
    {
        return $this->turnIn;
    }
}
