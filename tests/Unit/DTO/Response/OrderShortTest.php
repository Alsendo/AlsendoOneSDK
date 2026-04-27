<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\DTO\Response;

use AlsendoOne\SDK\DTO\Response\OrderShort;
use AlsendoOne\SDK\Type\Service;
use PHPUnit\Framework\TestCase;

class OrderShortTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function baseData(array $overrides = []): array
    {
        return $overrides + [
            'id' => 1,
            'service_id' => Service::DpdCourier,
            'service_name' => 'DPD Classic',
            'waybill_number' => 'WB001',
            'pickup_number' => null,
            'tracking_url' => '',
            'status' => 'new',
            'shipments_count' => 1,
            'content' => '',
            'comment' => '',
            'receiver' => [],
            'created' => '2024-01-15',
            'delivered' => null,
            'externalId' => null,
            'supplier' => 'DPD',
        ];
    }

    public function testFromArrayExternalIdMissingResultsInNull(): void
    {
        $data = $this->baseData();
        unset($data['externalId']);

        $orderShort = OrderShort::fromArray($data);

        $this->assertNull($orderShort->getExternalId());
    }

    public function testFromArrayExternalIdFalseBecomesNull(): void
    {
        $orderShort = OrderShort::fromArray($this->baseData(['externalId' => false]));

        $this->assertNull($orderShort->getExternalId());
    }

    public function testFromArrayExternalIdIntegerIsCastToString(): void
    {
        $orderShort = OrderShort::fromArray($this->baseData(['externalId' => 7]));

        $this->assertSame('7', $orderShort->getExternalId());
    }

    public function testFromArrayExternalIdStringIsPreserved(): void
    {
        $orderShort = OrderShort::fromArray($this->baseData(['externalId' => 'EXT-7']));

        $this->assertSame('EXT-7', $orderShort->getExternalId());
    }

    public function testToArrayContainsRenamedExternalIdKey(): void
    {
        $orderShort = OrderShort::fromArray($this->baseData(['externalId' => 'EXT-7']));

        $array = $orderShort->toArray();

        $this->assertSame('EXT-7', $array['external_id']);
        $this->assertArrayNotHasKey('externalId', $array);
        $this->assertSame(1, $array['id']);
        $this->assertSame(Service::DpdCourier, $array['service_id']);
        $this->assertSame('DPD', $array['supplier']);
        $this->assertSame('WB001', $array['waybill_number']);
    }
}
