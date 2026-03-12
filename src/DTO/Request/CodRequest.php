<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Request;

class CodRequest
{
    private int $amount;
    private ?string $currency;

    public function __construct(int $amount, ?string $currency = null)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function create(int $amount, ?string $currency = null): self
    {
        return new self($amount, $currency);
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'amount' => $this->amount,
            'currency' => $this->currency,
        ], static function ($value): bool {
            return $value !== null;
        });
    }
}
