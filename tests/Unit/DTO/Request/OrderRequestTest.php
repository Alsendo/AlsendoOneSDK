<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\DTO\Request;

use AlsendoOne\SDK\DTO\Address;
use AlsendoOne\SDK\DTO\Request\CodRequest;
use AlsendoOne\SDK\DTO\Request\NotificationRequest;
use AlsendoOne\SDK\DTO\Request\OrderRequest;
use AlsendoOne\SDK\DTO\Request\PickupRequest;
use AlsendoOne\SDK\DTO\Request\ShipmentRequest;
use AlsendoOne\SDK\Type\Service;
use PHPUnit\Framework\TestCase;

class OrderRequestTest extends TestCase
{
    public function testEmptyOrderToArrayReturnsEmptyArray(): void
    {
        $order = OrderRequest::create();

        $this->assertSame([], $order->toArray());
    }

    public function testServiceIdIsIncluded(): void
    {
        $order = OrderRequest::create()
            ->setService(Service::from(Service::InPostPaczkomat));

        $result = $order->toArray();

        $this->assertSame(41, $result['service_id']);
    }

    public function testNullServiceIsOmitted(): void
    {
        $order = OrderRequest::create()
            ->setService(null);

        $this->assertArrayNotHasKey('service_id', $order->toArray());
    }

    public function testAddressWithSenderOnly(): void
    {
        $sender = new Address('Firma', null, null, '500100200', 'Testowa 1', null, '00-001', 'Warszawa', 'PL');
        $order = OrderRequest::create()->setSenderAddress($sender);

        $result = $order->toArray();

        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('sender', $result['address']);
        $this->assertArrayNotHasKey('receiver', $result['address']);
        $this->assertSame('Firma', $result['address']['sender']['name']);
    }

    public function testAddressWithReceiverOnly(): void
    {
        $receiver = new Address('Anna', null, null, '600300400', 'Dluga 15', null, '80-831', 'Gdansk', 'PL');
        $order = OrderRequest::create()->setReceiverAddress($receiver);

        $result = $order->toArray();

        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('receiver', $result['address']);
        $this->assertArrayNotHasKey('sender', $result['address']);
    }

    public function testAddressWithBothSenderAndReceiver(): void
    {
        $sender = new Address('Firma', null, null, '500100200', 'Testowa 1', null, '00-001', 'Warszawa', 'PL');
        $receiver = new Address('Anna', null, null, '600300400', 'Dluga 15', null, '80-831', 'Gdansk', 'PL');

        $order = OrderRequest::create()
            ->setSenderAddress($sender)
            ->setReceiverAddress($receiver);

        $result = $order->toArray();

        $this->assertArrayHasKey('sender', $result['address']);
        $this->assertArrayHasKey('receiver', $result['address']);
    }

    public function testNoAddressIsOmitted(): void
    {
        $order = OrderRequest::create()->setComment('test');

        $this->assertArrayNotHasKey('address', $order->toArray());
    }

    public function testShipmentsAreSerialized(): void
    {
        $order = OrderRequest::create()
            ->addShipment(new ShipmentRequest('PACKAGE', 2500, 400, 300, 200))
            ->addShipment(new ShipmentRequest('ENVELOPE', 500, 350, 250, 20));

        $result = $order->toArray();

        $this->assertCount(2, $result['shipment']);
        $this->assertSame('PACKAGE', $result['shipment'][0]['shipment_type_code']);
        $this->assertSame(2500, $result['shipment'][0]['weight']);
        $this->assertSame(400, $result['shipment'][0]['dimension1']);
        $this->assertSame(300, $result['shipment'][0]['dimension2']);
        $this->assertSame(200, $result['shipment'][0]['dimension3']);
        $this->assertSame('ENVELOPE', $result['shipment'][1]['shipment_type_code']);
        $this->assertSame(350, $result['shipment'][1]['dimension1']);
        $this->assertSame(250, $result['shipment'][1]['dimension2']);
        $this->assertSame(20, $result['shipment'][1]['dimension3']);
    }

    public function testEmptyShipmentsAreOmitted(): void
    {
        $order = OrderRequest::create()->setComment('test');

        $this->assertArrayNotHasKey('shipment', $order->toArray());
    }

    public function testPickupIsSerialized(): void
    {
        $order = OrderRequest::create()
            ->setPickup(new PickupRequest('COURIER', '2026-04-10', '10:00', '16:00'));

        $result = $order->toArray();

        $this->assertSame('COURIER', $result['pickup']['type']);
        $this->assertSame('2026-04-10', $result['pickup']['date']);
    }

    public function testCodIsSerialized(): void
    {
        $order = OrderRequest::create()
            ->setCod(new CodRequest(1500, 'PLN'));

        $result = $order->toArray();

        $this->assertSame(1500, $result['cod']['amount']);
        $this->assertSame('PLN', $result['cod']['currency']);
    }

    public function testNotificationIsSerialized(): void
    {
        $notification = NotificationRequest::create()
            ->setNew(true, false, true)
            ->setDelivered(true);

        $order = OrderRequest::create()->setNotification($notification);

        $result = $order->toArray();

        $this->assertTrue($result['notification']['new']['isReceiverEmail']);
        $this->assertFalse($result['notification']['new']['isReceiverSms']);
        $this->assertTrue($result['notification']['new']['isSenderEmail']);
        $this->assertTrue($result['notification']['delivered']['isReceiverEmail']);
    }

    public function testOptionsAreIncluded(): void
    {
        $order = OrderRequest::create()
            ->setOption('saturday_delivery', true)
            ->setOption('insurance', 5000);

        $result = $order->toArray();

        $this->assertSame(true, $result['option']['saturday_delivery']);
        $this->assertSame(5000, $result['option']['insurance']);
    }

    public function testEmptyOptionsAreOmitted(): void
    {
        $order = OrderRequest::create()->setComment('test');

        $this->assertArrayNotHasKey('option', $order->toArray());
    }

    public function testScalarFieldsAreIncluded(): void
    {
        $order = OrderRequest::create()
            ->setShipmentValue(15000)
            ->setContent('Elektronika')
            ->setComment('Ostroznie')
            ->setIsZebra(true)
            ->setPushTrackingUrl('https://example.com/track');

        $result = $order->toArray();

        $this->assertSame(15000, $result['shipment_value']);
        $this->assertSame('Elektronika', $result['content']);
        $this->assertSame('Ostroznie', $result['comment']);
        $this->assertTrue($result['is_zebra']);
        $this->assertSame('https://example.com/track', $result['push_tracking_url']);
    }

    public function testNullScalarFieldsAreOmitted(): void
    {
        $order = OrderRequest::create()
            ->setContent('test');

        $result = $order->toArray();

        $this->assertArrayNotHasKey('shipment_value', $result);
        $this->assertArrayNotHasKey('comment', $result);
        $this->assertArrayNotHasKey('is_zebra', $result);
        $this->assertArrayNotHasKey('push_tracking_url', $result);
    }

    public function testFullOrderBuildsCorrectStructure(): void
    {
        $order = OrderRequest::create()
            ->setService(Service::from(Service::DpdCourier))
            ->setSenderAddress(new Address('Firma', 'Jan', 'jan@test.pl', '500100200', 'Testowa 1', null, '00-001', 'Warszawa', 'PL'))
            ->setReceiverAddress(new Address('Anna', null, 'anna@test.pl', '600300400', 'Dluga 15', null, '80-831', 'Gdansk', 'PL'))
            ->addShipment(new ShipmentRequest('PACKAGE', 2500, 400, 300, 200))
            ->setPickup(new PickupRequest('COURIER', '2026-04-10', '10:00', '16:00'))
            ->setCod(new CodRequest(1500, 'PLN'))
            ->setComment('Ostroznie')
            ->setShipmentValue(15000);

        $result = $order->toArray();

        $this->assertSame(21, $result['service_id']);
        $this->assertArrayHasKey('sender', $result['address']);
        $this->assertArrayHasKey('receiver', $result['address']);
        $this->assertCount(1, $result['shipment']);
        $this->assertSame(400, $result['shipment'][0]['dimension1']);
        $this->assertSame(300, $result['shipment'][0]['dimension2']);
        $this->assertSame(200, $result['shipment'][0]['dimension3']);
        $this->assertArrayHasKey('pickup', $result);
        $this->assertArrayHasKey('cod', $result);
        $this->assertSame('Ostroznie', $result['comment']);
        $this->assertSame(15000, $result['shipment_value']);
    }

    public function testFluentInterfaceReturnsSelf(): void
    {
        $order = OrderRequest::create();

        $this->assertSame($order, $order->setService(null));
        $this->assertSame($order, $order->setSenderAddress(null));
        $this->assertSame($order, $order->setReceiverAddress(null));
        $this->assertSame($order, $order->setShipments([]));
        $this->assertSame($order, $order->addShipment(new ShipmentRequest('PKG', 1, 1, 1, 1)));
        $this->assertSame($order, $order->setPickup(null));
        $this->assertSame($order, $order->setNotification(null));
        $this->assertSame($order, $order->setCod(null));
        $this->assertSame($order, $order->setOptions([]));
        $this->assertSame($order, $order->setOption('key', 'val'));
        $this->assertSame($order, $order->setShipmentValue(null));
        $this->assertSame($order, $order->setContent(null));
        $this->assertSame($order, $order->setComment(null));
        $this->assertSame($order, $order->setIsZebra(null));
        $this->assertSame($order, $order->setPushTrackingUrl(null));
    }
}
