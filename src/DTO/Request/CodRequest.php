<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Request;

class CodRequest
{
    private int $amount;
    private ?string $currency;
    private ?string $bankAccount;
    private ?int $bankAccountId;

    public function __construct(
        int $amount,
        ?string $currency = null,
        ?string $bankAccount = null,
        ?int $bankAccountId = null
    ) {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->bankAccount = $bankAccount;
        $this->bankAccountId = $bankAccountId;
    }

    public static function create(
        int $amount,
        ?string $currency = null,
        ?string $bankAccount = null,
        ?int $bankAccountId = null
    ): self {
        return new self($amount, $currency, $bankAccount, $bankAccountId);
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
     * Bank account number for the COD payout.
     */
    public function setBankAccount(?string $bankAccount): self
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * Id of a bank account stored in the customer panel (alternative to a raw account number).
     */
    public function setBankAccountId(?int $bankAccountId): self
    {
        $this->bankAccountId = $bankAccountId;

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
            'bankaccount' => $this->bankAccount,
            'bankaccount_id' => $this->bankAccountId,
        ], static function ($value): bool {
            return $value !== null;
        });
    }
}
