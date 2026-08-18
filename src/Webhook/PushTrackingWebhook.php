<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Webhook;

use AlsendoOne\SDK\DTO\Webhook\PushTrackingNotification;
use AlsendoOne\SDK\Exception\WebhookVerificationException;

/**
 * Verifies and parses push-tracking webhooks.
 *
 * When an order is created with `push_tracking_url`, the platform POSTs a
 * JSON body to that URL on every status change:
 *
 *     {"signature": "<hex hmac>", "response": {"orderNumber": ..., "statuses": [...]}}
 *
 * The signature is HMAC-SHA256 over "{app_id}::{json}:" (the same scheme as
 * outgoing API requests, with the route and expires components left empty),
 * where {json} is the `response` object serialized with unescaped unicode,
 * slashes and line terminators, keyed with your own `app_secret`.
 *
 * Respond with HTTP 200 to acknowledge the notification.
 */
class PushTrackingWebhook
{
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS;

    private string $appId;
    private string $appSecret;

    public function __construct(string $appId, string $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * Verify the signature of a raw webhook request body and parse it.
     *
     * @param string $rawBody Raw HTTP request body as received
     * @throws WebhookVerificationException on malformed payload or signature mismatch
     */
    public function parse(string $rawBody): PushTrackingNotification
    {
        $decoded = json_decode($rawBody, true);
        if (!is_array($decoded)) {
            throw new WebhookVerificationException('Webhook body is not valid JSON.');
        }

        $signature = $decoded['signature'] ?? null;
        if (!is_string($signature) || $signature === '') {
            throw new WebhookVerificationException('Webhook body is missing the signature field.');
        }

        $response = $decoded['response'] ?? null;
        if (!is_array($response)) {
            throw new WebhookVerificationException('Webhook body is missing the response field.');
        }

        if (!hash_equals($this->expectedSignature($response), $signature)) {
            throw new WebhookVerificationException('Webhook signature does not match.');
        }

        return PushTrackingNotification::fromArray($response);
    }

    /**
     * Convenience boolean variant of {@see parse()}.
     */
    public function isValid(string $rawBody): bool
    {
        try {
            $this->parse($rawBody);

            return true;
        } catch (WebhookVerificationException $e) {
            return false;
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function expectedSignature(array $response): string
    {
        $data = json_encode($response, self::JSON_FLAGS);
        $stringToSign = sprintf('%s::%s:', $this->appId, $data);

        return hash_hmac('sha256', $stringToSign, $this->appSecret);
    }
}
