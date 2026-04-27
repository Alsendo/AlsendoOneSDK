<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\DTO\Response;

use AlsendoOne\SDK\DTO\Response\Order;
use AlsendoOne\SDK\Type\Service;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function baseData(array $overrides = []): array
    {
        return $overrides + [
            'id' => 123,
            'supplier' => 'DPD',
            'service_id' => Service::DpdCourier,
            'service_name' => 'DPD Classic',
            'waybill_number' => 'WB123',
            'pickup' => null,
            'pickup_number' => null,
            'tracking_url' => 'https://tracking.example.com/123',
            'status' => 'new',
            'shipments_count' => 1,
            'shipments' => [],
            'content' => 'Documents',
            'comment' => '',
            'sender' => [],
            'receiver' => [],
            'created' => '2024-01-15 10:00:00',
            'delivered' => null,
            'price' => 1500,
            'price_var' => 345,
            'price_gross' => 1845,
            'cod' => false,
            'cod_currency' => null,
            'declaration_value' => false,
            'externalId' => null,
        ];
    }

    public function testFromArrayPickupAcceptsString(): void
    {
        $order = Order::fromArray($this->baseData([
            'pickup' => '2024-02-01',
            'pickup_number' => 'PN-1',
        ]));

        $this->assertSame('2024-02-01', $order->getPickup());
        $this->assertSame('PN-1', $order->getPickupNumber());
    }

    public function testFromArrayPickupNonStringValuesAreCoercedToNull(): void
    {
        $order = Order::fromArray($this->baseData([
            'pickup' => false,
            'pickup_number' => ['unexpected', 'array'],
        ]));

        $this->assertNull($order->getPickup());
        $this->assertNull($order->getPickupNumber());
    }

    public function testFromArrayExternalIdMissingResultsInNull(): void
    {
        $data = $this->baseData();
        unset($data['externalId']);

        $order = Order::fromArray($data);

        $this->assertNull($order->getExternalId());
    }

    public function testFromArrayExternalIdFalseBecomesNull(): void
    {
        $order = Order::fromArray($this->baseData(['externalId' => false]));

        $this->assertNull($order->getExternalId());
    }

    public function testFromArrayExternalIdIntegerIsCastToString(): void
    {
        $order = Order::fromArray($this->baseData(['externalId' => 42]));

        $this->assertSame('42', $order->getExternalId());
    }

    public function testFromArrayExternalIdStringIsPreserved(): void
    {
        $order = Order::fromArray($this->baseData(['externalId' => 'EXT-9']));

        $this->assertSame('EXT-9', $order->getExternalId());
    }

    public function testToArrayRoundtripsTopLevelFields(): void
    {
        $order = Order::fromArray($this->baseData([
            'pickup' => '2024-02-01',
            'pickup_number' => 'PN-1',
            'externalId' => 'EXT-9',
            'cod' => 1500,
            'cod_currency' => 'PLN',
            'declaration_value' => 9999,
        ]));

        $array = $order->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('DPD', $array['supplier']);
        $this->assertSame(Service::DpdCourier, $array['service_id']);
        $this->assertSame('WB123', $array['waybill_number']);
        $this->assertSame('2024-02-01', $array['pickup']);
        $this->assertSame('PN-1', $array['pickup_number']);
        $this->assertSame('EXT-9', $array['external_id']);
        $this->assertSame(1500, $array['cod']);
        $this->assertSame('PLN', $array['cod_currency']);
        $this->assertSame(9999, $array['declaration_value']);
        $this->assertSame([], $array['shipments']);
    }
}
