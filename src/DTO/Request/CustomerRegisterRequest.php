<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Request;

/**
 * Customer registration payload for the customer_register/ endpoint.
 *
 * Note: the endpoint is partner-gated — the calling application needs the
 * "register via API" privilege granted by Apaczka.
 */
class CustomerRegisterRequest
{
    private ?string $login = null;
    private ?string $email = null;
    private ?string $password = null;
    private ?string $phone = null;
    private ?string $nip = null;
    private ?string $companyName = null;
    private ?string $promoCode = null;
    private ?string $codBankAccountNumber = null;

    public static function create(): self
    {
        return new self();
    }

    /**
     * Login — must be a valid e-mail address.
     */
    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Password — more than 6 characters required by the API.
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Phone — more than 8 characters required by the API.
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Polish VAT id; when present the customer is registered as a company.
     */
    public function setNip(?string $nip): self
    {
        $this->nip = $nip;

        return $this;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function setPromoCode(?string $promoCode): self
    {
        $this->promoCode = $promoCode;

        return $this;
    }

    public function setCodBankAccountNumber(?string $codBankAccountNumber): self
    {
        $this->codBankAccountNumber = $codBankAccountNumber;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return array_filter([
            'login' => $this->login,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'nip' => $this->nip,
            'company_name' => $this->companyName,
            'promo_code' => $this->promoCode,
            'cod_bank_account_number' => $this->codBankAccountNumber,
        ], static function ($value): bool {
            return $value !== null;
        });
    }
}
