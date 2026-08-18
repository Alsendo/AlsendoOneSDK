<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Webhook;

use AlsendoOne\SDK\Exception\WebhookVerificationException;
use AlsendoOne\SDK\Webhook\PushTrackingWebhook;
use PHPUnit\Framework\TestCase;

class PushTrackingWebhookTest extends TestCase
{
    private const APP_ID = 'test_app_id';
    private const APP_SECRET = 'test_app_secret';

    private PushTrackingWebhook $webhook;

    protected function setUp(): void
    {
        $this->webhook = new PushTrackingWebhook(self::APP_ID, self::APP_SECRET);
    }

    /**
     * @param array<string, mixed> $response
     */
    private function signedBody(array $response): string
    {
        $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS);
        $signature = hash_hmac('sha256', sprintf('%s::%s:', self::APP_ID, $json), self::APP_SECRET);

        return (string) json_encode(['signature' => $signature, 'response' => $response]);
    }

    /**
     * @return array<string, mixed>
     */
    private function sampleResponse(): array
    {
        return [
            'orderNumber' => '435397465',
            'trackingNumber' => '0000879334592Q',
            'operator' => 'DPD',
            'statuses' => [
                [
                    'status' => 'ON_THE_WAY',
                    'updatedAt' => '2026-08-18T08:00:00',
                    'trackingOperatorStatuses' => [
                        [
                            'status' => 'IN_TRANSIT',
                            'statusDetailed' => 'Przesyłka w drodze',
                            'place' => 'Kraków',
                            'description' => 'W trasie / na dworze pada',
                            'updatedAt' => '2026-08-18T08:00:00',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testParsesValidNotification(): void
    {
        $notification = $this->webhook->parse($this->signedBody($this->sampleResponse()));

        $this->assertSame('435397465', $notification->getOrderNumber());
        $this->assertSame('0000879334592Q', $notification->getTrackingNumber());
        $this->assertSame('DPD', $notification->getOperator());
        $this->assertCount(1, $notification->getStatuses());

        $status = $notification->getStatuses()[0];
        $this->assertSame('ON_THE_WAY', $status->getStatus());
        $this->assertCount(1, $status->getOperatorStatuses());
        $this->assertSame('Kraków', $status->getOperatorStatuses()[0]->getPlace());
    }

    public function testUnicodePayloadWithSlashesVerifies(): void
    {
        // The signature covers JSON with unescaped unicode/slashes — a payload
        // with Polish diacritics and URLs must verify.
        $response = $this->sampleResponse();
        $response['statuses'][0]['trackingOperatorStatuses'][0]['description'] = 'Łódź/Gdańsk — čučoriedka';

        $notification = $this->webhook->parse($this->signedBody($response));

        $this->assertSame(
            'Łódź/Gdańsk — čučoriedka',
            $notification->getStatuses()[0]->getOperatorStatuses()[0]->getDescription()
        );
    }

    public function testEmptyStatusesIsValid(): void
    {
        $response = $this->sampleResponse();
        $response['statuses'] = [];

        $notification = $this->webhook->parse($this->signedBody($response));

        $this->assertSame([], $notification->getStatuses());
    }

    public function testRejectsTamperedPayload(): void
    {
        $body = $this->signedBody($this->sampleResponse());
        $tampered = str_replace('435397465', '999999999', $body);

        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('signature does not match');

        $this->webhook->parse($tampered);
    }

    public function testRejectsWrongSecret(): void
    {
        $other = new PushTrackingWebhook(self::APP_ID, 'wrong_secret');

        $this->assertFalse($other->isValid($this->signedBody($this->sampleResponse())));
    }

    public function testRejectsInvalidJson(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('not valid JSON');

        $this->webhook->parse('<html>not json</html>');
    }

    public function testRejectsMissingSignature(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('missing the signature');

        $this->webhook->parse((string) json_encode(['response' => $this->sampleResponse()]));
    }

    public function testRejectsMissingResponse(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('missing the response');

        $this->webhook->parse((string) json_encode(['signature' => 'abc']));
    }

    public function testIsValidReturnsTrueForValidBody(): void
    {
        $this->assertTrue($this->webhook->isValid($this->signedBody($this->sampleResponse())));
    }
}
