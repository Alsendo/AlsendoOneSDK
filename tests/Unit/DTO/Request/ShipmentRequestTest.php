<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\DTO\Request;

use AlsendoOne\SDK\DTO\Request\ShipmentRequest;
use PHPUnit\Framework\TestCase;

class ShipmentRequestTest extends TestCase
{
    public function testToArrayUsesDimensionKeysExpectedByApi(): void
    {
        $shipment = new ShipmentRequest('PACZKA', 30, 120, 60, 60);

        $data = $shipment->toArray();

        $this->assertSame(120, $data['dimension1']);
        $this->assertSame(60, $data['dimension2']);
        $this->assertSame(60, $data['dimension3']);
        $this->assertSame(30.0, $data['weight']);
        $this->assertSame('PACZKA', $data['shipment_type_code']);

        // The API silently ignores these keys in requests — they must not be sent.
        $this->assertArrayNotHasKey('length', $data);
        $this->assertArrayNotHasKey('width', $data);
        $this->assertArrayNotHasKey('height', $data);
    }

    public function testWeightAcceptsFractionalKilograms(): void
    {
        $shipment = new ShipmentRequest('PACZKA', 0.5, 20, 15, 5);

        $this->assertSame(0.5, $shipment->toArray()['weight']);
    }
}
