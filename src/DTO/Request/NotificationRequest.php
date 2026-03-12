<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Request;

class NotificationRequest
{
    /** @var array<string, array<string, bool>> */
    private array $settings = [];

    public static function create(): self
    {
        return new self();
    }

    public function setNew(
        bool $receiverEmail = false,
        bool $receiverSms = false,
        bool $senderEmail = false,
        bool $senderSms = false
    ): self {
        $this->settings['new'] = [
            'isReceiverEmail' => $receiverEmail,
            'isReceiverSms' => $receiverSms,
            'isSenderEmail' => $senderEmail,
            'isSenderSms' => $senderSms,
        ];

        return $this;
    }

    public function setSent(
        bool $receiverEmail = false,
        bool $receiverSms = false,
        bool $senderEmail = false,
        bool $senderSms = false
    ): self {
        $this->settings['sent'] = [
            'isReceiverEmail' => $receiverEmail,
            'isReceiverSms' => $receiverSms,
            'isSenderEmail' => $senderEmail,
            'isSenderSms' => $senderSms,
        ];

        return $this;
    }

    public function setException(
        bool $receiverEmail = false,
        bool $receiverSms = false,
        bool $senderEmail = false,
        bool $senderSms = false
    ): self {
        $this->settings['exception'] = [
            'isReceiverEmail' => $receiverEmail,
            'isReceiverSms' => $receiverSms,
            'isSenderEmail' => $senderEmail,
            'isSenderSms' => $senderSms,
        ];

        return $this;
    }

    public function setDelivered(
        bool $receiverEmail = false,
        bool $receiverSms = false,
        bool $senderEmail = false,
        bool $senderSms = false
    ): self {
        $this->settings['delivered'] = [
            'isReceiverEmail' => $receiverEmail,
            'isReceiverSms' => $receiverSms,
            'isSenderEmail' => $senderEmail,
            'isSenderSms' => $senderSms,
        ];

        return $this;
    }

    /**
     * @return array<string, array<string, bool>>
     */
    public function toArray(): array
    {
        return $this->settings;
    }
}
