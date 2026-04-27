<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\DTO\Request;

use AlsendoOne\SDK\DTO\Request\CustomsDataRequest;
use AlsendoOne\SDK\DTO\Request\ShipmentRequest;
use PHPUnit\Framework\TestCase;

class ShipmentRequestTest extends TestCase
{
    public function testToArrayUsesDimensionFieldNames(): void
    {
        $shipment = new ShipmentRequest('PACKAGE', 2500, 400, 300, 200);

        $result = $shipment->toArray();

        $this->assertSame('PACKAGE', $result['shipment_type_code']);
        $this->assertSame(2500, $result['weight']);
        $this->assertSame(400, $result['dimension1']);
        $this->assertSame(300, $result['dimension2']);
        $this->assertSame(200, $result['dimension3']);
    }

    public function testToArrayDoesNotEmitLegacyDimensionKeys(): void
    {
        $shipment = new ShipmentRequest('PACKAGE', 1000, 100, 100, 100);

        $result = $shipment->toArray();

        $this->assertArrayNotHasKey('length', $result);
        $this->assertArrayNotHasKey('width', $result);
        $this->assertArrayNotHasKey('height', $result);
    }

    public function testNullableFieldsAreOmittedWhenNull(): void
    {
        $shipment = new ShipmentRequest('PACKAGE', 1000, 100, 100, 100);

        $result = $shipment->toArray();

        $this->assertArrayNotHasKey('content', $result);
        $this->assertArrayNotHasKey('comment', $result);
        $this->assertArrayNotHasKey('customs_data', $result);
    }

    public function testOptionalFieldsAreIncludedWhenSet(): void
    {
        $shipment = new ShipmentRequest('PACKAGE', 1000, 100, 100, 100, 'Books', 'Handle with care');

        $result = $shipment->toArray();

        $this->assertSame('Books', $result['content']);
        $this->assertSame('Handle with care', $result['comment']);
    }

    public function testCustomsDataIsSerialized(): void
    {
        $shipment = new ShipmentRequest('PACKAGE', 1000, 100, 100, 100);
        $shipment->addCustomsData(new CustomsDataRequest('Book', 1, 5000, 500, '4901.99', 'PL'));
        $shipment->addCustomsData(new CustomsDataRequest('Pen', 10, 200, 50));

        $result = $shipment->toArray();

        $this->assertCount(2, $result['customs_data']);
        $this->assertSame('Book', $result['customs_data'][0]['description']);
        $this->assertSame(1, $result['customs_data'][0]['quantity']);
        $this->assertSame(5000, $result['customs_data'][0]['unit_price']);
        $this->assertSame('4901.99', $result['customs_data'][0]['hs_code']);
        $this->assertSame('PL', $result['customs_data'][0]['country_of_origin']);
        $this->assertSame('Pen', $result['customs_data'][1]['description']);
        $this->assertArrayNotHasKey('hs_code', $result['customs_data'][1]);
        $this->assertArrayNotHasKey('country_of_origin', $result['customs_data'][1]);
    }

    public function testStaticCreateProducesEquivalentInstance(): void
    {
        $a = ShipmentRequest::create('PACKAGE', 1000, 100, 100, 100);
        $b = new ShipmentRequest('PACKAGE', 1000, 100, 100, 100);

        $this->assertSame($a->toArray(), $b->toArray());
    }

    public function testSettersUpdateFieldsForToArray(): void
    {
        $shipment = ShipmentRequest::create('PACKAGE', 1, 1, 1, 1)
            ->setShipmentTypeCode('ENVELOPE')
            ->setWeight(500)
            ->setLength(350)
            ->setWidth(250)
            ->setHeight(20)
            ->setContent('Documents')
            ->setComment('Urgent');

        $result = $shipment->toArray();

        $this->assertSame('ENVELOPE', $result['shipment_type_code']);
        $this->assertSame(500, $result['weight']);
        $this->assertSame(350, $result['dimension1']);
        $this->assertSame(250, $result['dimension2']);
        $this->assertSame(20, $result['dimension3']);
        $this->assertSame('Documents', $result['content']);
        $this->assertSame('Urgent', $result['comment']);
    }

    public function testSetCustomsDataReplacesCollection(): void
    {
        $shipment = new ShipmentRequest('PACKAGE', 1000, 100, 100, 100);
        $shipment->addCustomsData(new CustomsDataRequest('Old item', 1, 100, 10));

        $shipment->setCustomsData([
            new CustomsDataRequest('New item', 2, 200, 20),
        ]);

        $result = $shipment->toArray();

        $this->assertCount(1, $result['customs_data']);
        $this->assertSame('New item', $result['customs_data'][0]['description']);
    }

    public function testSettersReturnSelfForChaining(): void
    {
        $shipment = new ShipmentRequest('PACKAGE', 1, 1, 1, 1);

        $this->assertSame($shipment, $shipment->setShipmentTypeCode('A'));
        $this->assertSame($shipment, $shipment->setWeight(1));
        $this->assertSame($shipment, $shipment->setLength(1));
        $this->assertSame($shipment, $shipment->setWidth(1));
        $this->assertSame($shipment, $shipment->setHeight(1));
        $this->assertSame($shipment, $shipment->setContent(null));
        $this->assertSame($shipment, $shipment->setComment(null));
        $this->assertSame($shipment, $shipment->setCustomsData(null));
        $this->assertSame($shipment, $shipment->addCustomsData(new CustomsDataRequest('x', 1, 1, 1)));
    }
}