<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit;

use AlsendoOne\SDK\ApaczkaClient;
use AlsendoOne\SDK\DTO\Response\AccessPoint;
use AlsendoOne\SDK\DTO\Response\DispatchCodeResponse;
use AlsendoOne\SDK\DTO\Response\Order;
use AlsendoOne\SDK\DTO\Response\OrderShort;
use AlsendoOne\SDK\DTO\Response\PickupHoursResponse;
use AlsendoOne\SDK\DTO\Response\PickupResponse;
use AlsendoOne\SDK\DTO\Response\ServiceStructure;
use AlsendoOne\SDK\DTO\Response\TrackingResponse;
use AlsendoOne\SDK\DTO\Response\TurnInResponse;
use AlsendoOne\SDK\DTO\Response\Valuation;
use AlsendoOne\SDK\DTO\Response\WaybillResponse;
use AlsendoOne\SDK\Enum\Service;
use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Http\HttpClientInterface;
use AlsendoOne\SDK\Http\Response;
use PHPUnit\Framework\TestCase;

class ApaczkaClientTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private ApaczkaClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->client = new ApaczkaClient(
            'test_app_id',
            'test_app_secret',
            $this->httpClient,
            'https://api.example.com/api/v2/'
        );
    }

    public function testGetServiceStructure(): void
    {
        $responseData = [
            'services' => [
                41 => ['id' => 41, 'name' => 'InPost Paczkomat', 'supplier' => 'INPOST'],
            ],
            'options' => [],
            'package_type' => [],
            'points_type' => [],
            'pickup_type' => [],
            'unit_type' => [],
        ];
        $this->mockPostResponse('https://api.example.com/api/v2/service_structure/', $responseData);

        $result = $this->client->getServiceStructure();

        $this->assertInstanceOf(ServiceStructure::class, $result);
        $this->assertCount(1, $result->getServices());
        $this->assertSame('InPost Paczkomat', $result->getServices()[41]['name']);
    }

    public function testGetOrder(): void
    {
        $responseData = [
            'order' => [
                'id' => 123,
                'supplier' => 'DPD',
                'service_id' => 41,
                'service_name' => 'DPD Classic',
                'waybill_number' => 'WB123',
                'pickup' => [
                    'type' => 'BOX_MACHINE',
                    'date' => '',
                    'hours_from' => '',
                    'hours_to' => '',
                    'address_id' => null,
                ],
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
                'price_var' => 23,
                'price_gross' => 1845,
                'cod' => false,
                'cod_currency' => null,
                'declaration_value' => false,
                'externalId' => null,
            ],
        ];
        $this->mockPostResponse('https://api.example.com/api/v2/order/123/', $responseData);

        $result = $this->client->getOrder(123);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertSame(123, $result->getId());
        $this->assertSame('new', $result->getStatus());
        $this->assertSame('DPD', $result->getSupplier());
        $this->assertSame(Service::InPostPaczkomat, $result->getService());
        $this->assertNotNull($result->getPickup());
        $this->assertSame('BOX_MACHINE', $result->getPickup()->getType());
        $this->assertNull($result->getPickup()->getAddressId());
    }

    public function testGetOrders(): void
    {
        $responseData = [
            'orders' => [
                [
                    'id' => 1,
                    'service_id' => 41,
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
                ],
                [
                    'id' => 2,
                    'service_id' => 42,
                    'service_name' => 'DHL Standard',
                    'waybill_number' => 'WB002',
                    'pickup_number' => null,
                    'tracking_url' => '',
                    'status' => 'sent',
                    'shipments_count' => 1,
                    'content' => '',
                    'comment' => '',
                    'receiver' => [],
                    'created' => '2024-01-16',
                    'delivered' => null,
                    'externalId' => null,
                    'supplier' => 'DHL',
                ],
            ],
        ];
        $this->mockPostResponse('https://api.example.com/api/v2/orders/', $responseData);

        $result = $this->client->getOrders(1, 10);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(OrderShort::class, $result[0]);
        $this->assertInstanceOf(OrderShort::class, $result[1]);
        $this->assertSame(1, $result[0]->getId());
        $this->assertSame(2, $result[1]->getId());
    }

    public function testGetOrdersLimitCappedAt25(): void
    {
        $this->httpClient->method('post')
            ->willReturnCallback(function (string $url, array $params) {
                $request = json_decode($params['request'], true);
                $this->assertSame(25, $request['limit']);

                return new Response(200, json_encode([
                    'status' => 200,
                    'message' => 'OK',
                    'response' => ['orders' => []],
                ]));
            });

        $this->client->getOrders(1, 100);
    }

    public function testSendOrder(): void
    {
        $responseData = [
            'order' => [
                'id' => 456,
                'service_id' => 41,
                'service_name' => 'DPD Classic',
                'waybill_number' => 'ABC123',
                'pickup_number' => null,
                'tracking_url' => 'https://tracking.example.com/456',
                'status' => 'new',
                'shipments_count' => 1,
                'content' => '',
                'comment' => '',
                'receiver' => [],
                'created' => '2024-01-15',
                'delivered' => null,
                'externalId' => null,
                'supplier' => 'DPD',
            ],
        ];
        $this->mockPostResponse('https://api.example.com/api/v2/order_send/', $responseData);

        $result = $this->client->sendOrder(['service_id' => 41]);

        $this->assertInstanceOf(OrderShort::class, $result);
        $this->assertSame(456, $result->getId());
        $this->assertSame('ABC123', $result->getWaybillNumber());
    }

    public function testCancelOrder(): void
    {
        $this->mockPostResponse('https://api.example.com/api/v2/cancel_order/789/', []);

        // cancelOrder returns void - just verify no exception is thrown
        $this->client->cancelOrder(789);
        $this->addToAssertionCount(1);
    }

    public function testGetValuation(): void
    {
        $responseData = [
            'price_table' => [
                41 => [
                    'price' => 1500,
                    'price_gross' => 1845,
                    'options' => [],
                    'shipments' => [],
                ],
            ],
        ];
        $this->mockPostResponse('https://api.example.com/api/v2/order_valuation/', $responseData);

        $result = $this->client->getValuation(['service_id' => 41]);

        $this->assertInstanceOf(Valuation::class, $result);
        $this->assertNotNull($result->getPriceForService(Service::InPostPaczkomat));
        $this->assertSame(1500, $result->getPriceForService(Service::InPostPaczkomat)->getPrice());
        $this->assertSame(1845, $result->getPriceForService(Service::InPostPaczkomat)->getPriceGross());
    }

    public function testGetPickupHours(): void
    {
        $responseData = [
            'postal_code' => '00-001',
            'hours' => [
                '2024-01-15' => [
                    'date' => '2024-01-15',
                    'services' => [
                        41 => ['from' => '08:00', 'to' => '18:00'],
                    ],
                ],
            ],
        ];
        $this->mockPostResponse('https://api.example.com/api/v2/pickup_hours/', $responseData);

        $result = $this->client->getPickupHours('00-001');

        $this->assertInstanceOf(PickupHoursResponse::class, $result);
        $this->assertSame('00-001', $result->getPostalCode());
        $this->assertCount(1, $result->getHours());
    }

    public function testSchedulePickup(): void
    {
        $responseData = ['foreign_courier_id' => 'COURIER-123'];
        $this->mockPostResponse('https://api.example.com/api/v2/pickup/123/', $responseData);

        $result = $this->client->schedulePickup(123, '2024-01-15', '10:00', '14:00');

        $this->assertInstanceOf(PickupResponse::class, $result);
        $this->assertSame('COURIER-123', $result->getForeignCourierId());
    }

    public function testScheduleBatchPickup(): void
    {
        $responseData = ['success' => true];
        $this->mockPostResponse('https://api.example.com/api/v2/batch_pickup/', $responseData);

        $result = $this->client->scheduleBatchPickup([1, 2, 3], '2024-01-15', '10:00', '14:00');

        $this->assertSame(['success' => true], $result);
    }

    public function testGetWaybill(): void
    {
        $responseData = ['waybill' => 'base64pdfdata==', 'type' => 'pdf'];
        $this->mockPostResponse('https://api.example.com/api/v2/waybill/123/', $responseData);

        $result = $this->client->getWaybill(123);

        $this->assertInstanceOf(WaybillResponse::class, $result);
        $this->assertSame('base64pdfdata==', $result->getWaybill());
        $this->assertSame('pdf', $result->getType());
    }

    public function testGetTurnIn(): void
    {
        $responseData = ['turn_in' => 'base64pdfdata=='];
        $this->mockPostResponse('https://api.example.com/api/v2/turn_in/', $responseData);

        $result = $this->client->getTurnIn([1, 2, 3]);

        $this->assertInstanceOf(TurnInResponse::class, $result);
        $this->assertSame('base64pdfdata==', $result->getTurnIn());
    }

    public function testGetDispatchCode(): void
    {
        $responseData = ['order_id' => 123, 'dispatch_code' => 'ABC-XYZ-001'];
        $this->mockPostResponse('https://api.example.com/api/v2/dispatch_code/123/', $responseData);

        $result = $this->client->getDispatchCode(123);

        $this->assertInstanceOf(DispatchCodeResponse::class, $result);
        $this->assertSame(123, $result->getOrderId());
        $this->assertSame('ABC-XYZ-001', $result->getDispatchCode());
    }

    public function testGetDispatchCodeReturnsNullWhenCarrierDidNotProvide(): void
    {
        $responseData = ['order_id' => 456, 'dispatch_code' => null];
        $this->mockPostResponse('https://api.example.com/api/v2/dispatch_code/456/', $responseData);

        $result = $this->client->getDispatchCode(456);

        $this->assertInstanceOf(DispatchCodeResponse::class, $result);
        $this->assertSame(456, $result->getOrderId());
        $this->assertNull($result->getDispatchCode());
    }

    public function testGetPoints(): void
    {
        $responseData = [
            'points' => [
                [
                    'type' => 'PACZKOMAT',
                    'subtype' => '',
                    'name' => 'Punkt Krakow',
                    'foreign_address_id' => 'KRA01',
                    'address' => [],
                    'image_url' => null,
                    'open_hours' => null,
                    'option_cod' => false,
                    'option_send' => true,
                    'option_deliver' => true,
                    'additional_info' => '',
                    'distance' => 0,
                ],
            ],
        ];
        $this->mockPostResponse('https://api.example.com/api/v2/points/INPOST/', $responseData);

        $result = $this->client->getPoints('INPOST');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(AccessPoint::class, $result[0]);
        $this->assertSame('KRA01', $result[0]->getForeignAddressId());
        $this->assertSame('Punkt Krakow', $result[0]->getName());
    }

    public function testGetTracking(): void
    {
        $responseData = [
            'data' => [
                [
                    'status' => 'DELIVERED',
                    'status_original' => 'Przesyłka doręczona',
                    'description' => 'Doręczono do odbiorcy',
                    'place' => 'Gdansk',
                    'updated_at' => '2026-08-15 14:32:00',
                ],
                [
                    'status' => 'IN_TRANSIT',
                    'status_original' => 'Przesyłka w drodze',
                    'description' => '',
                    'place' => 'Warszawa',
                    'updated_at' => '2026-08-14 08:01:00',
                ],
            ],
            'service' => 'DPD',
        ];
        $this->mockPostResponse('https://api.example.com/api/v2/tracking/WB123/', $responseData);

        $result = $this->client->getTracking('WB123');

        $this->assertInstanceOf(TrackingResponse::class, $result);
        $this->assertSame('DPD', $result->getService());
        $this->assertCount(2, $result->getEvents());

        $first = $result->getEvents()[0];
        $this->assertSame('DELIVERED', $first->getStatus());
        $this->assertSame('Przesyłka doręczona', $first->getStatusOriginal());
        $this->assertSame('Doręczono do odbiorcy', $first->getDescription());
        $this->assertSame('Gdansk', $first->getPlace());
        $this->assertSame('2026-08-15 14:32:00', $first->getUpdatedAt());

        $this->assertSame('IN_TRANSIT', $result->getEvents()[1]->getStatus());
    }

    public function testGetTrackingWithEmptyData(): void
    {
        $this->mockPostResponse('https://api.example.com/api/v2/tracking/UNKNOWN/', [
            'data' => [],
            'service' => '',
        ]);

        $result = $this->client->getTracking('UNKNOWN');

        $this->assertSame([], $result->getEvents());
        $this->assertSame('', $result->getService());
    }

    public function testGetTrackingSignsRouteWithWaybillPathParam(): void
    {
        $this->httpClient->expects($this->once())
            ->method('post')
            ->willReturnCallback(function (string $url, array $params) {
                $this->assertSame('https://api.example.com/api/v2/tracking/WB123/', $url);

                // The signature must cover the route including the path param and trailing slash.
                $expected = hash_hmac(
                    'sha256',
                    sprintf('test_app_id:tracking/WB123/:%s:%s', $params['request'], $params['expires']),
                    'test_app_secret'
                );
                $this->assertSame($expected, $params['signature']);

                return new Response(200, json_encode([
                    'status' => 200,
                    'message' => '',
                    'response' => ['data' => [], 'service' => ''],
                ]));
            });

        $this->client->getTracking('WB123');
    }

    public function testApiErrorThrowsApiException(): void
    {
        $body = json_encode([
            'status' => 500,
            'message' => 'Internal error',
            'response' => [],
        ]);

        $this->httpClient->method('post')
            ->willReturn(new Response(200, $body));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Internal error');

        $this->client->getOrder(999);
    }

    public function testGetOrderWithUnknownServiceIdDoesNotThrow(): void
    {
        $this->mockPostResponse('https://api.example.com/api/v2/order/123/', [
            'order' => [
                'id' => 123,
                'service_id' => 99999,
                'service_name' => 'Future Courier',
            ],
        ]);

        $result = $this->client->getOrder(123);

        $this->assertNull($result->getService());
        $this->assertSame(99999, $result->getServiceId());
        $this->assertSame('Future Courier', $result->getServiceName());
    }

    public function testGetValuationKeepsUnknownServiceIds(): void
    {
        $this->mockPostResponse('https://api.example.com/api/v2/order_valuation/', [
            'price_table' => [
                '41' => ['price' => 1500, 'price_gross' => 1845],
                '99999' => ['price' => 2000, 'price_gross' => 2460],
            ],
        ]);

        $result = $this->client->getValuation(['service_id' => 41]);

        $this->assertCount(2, $result->getPriceTable());
        $known = $result->getPriceForService(Service::InPostPaczkomat);
        $this->assertNotNull($known);
        $this->assertSame(Service::InPostPaczkomat, $known->getService());

        $unknown = $result->getPriceTable()[99999];
        $this->assertNull($unknown->getService());
        $this->assertSame(99999, $unknown->getServiceId());
        $this->assertSame(2000, $unknown->getPrice());
    }

    public function testRequestSendsCorrectFormParams(): void
    {
        $this->httpClient->expects($this->once())
            ->method('post')
            ->willReturnCallback(function (string $url, array $params) {
                $this->assertSame('https://api.example.com/api/v2/service_structure/', $url);
                $this->assertArrayHasKey('app_id', $params);
                $this->assertArrayHasKey('request', $params);
                $this->assertArrayHasKey('expires', $params);
                $this->assertArrayHasKey('signature', $params);
                $this->assertSame('test_app_id', $params['app_id']);
                $this->assertSame('{}', $params['request']);

                // Verify expires is in the future
                $this->assertGreaterThan(time(), (int) $params['expires']);

                // Verify signature is valid hex
                $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $params['signature']);

                return new Response(200, json_encode([
                    'status' => 200,
                    'message' => 'OK',
                    'response' => [],
                ]));
            });

        $this->client->getServiceStructure();
    }

    public function testBaseUrlTrailingSlashHandled(): void
    {
        $client = new ApaczkaClient('id', 'secret', $this->httpClient, 'https://api.example.com/api/v2');

        $this->httpClient->method('post')
            ->willReturnCallback(function (string $url) {
                $this->assertStringStartsWith('https://api.example.com/api/v2/', $url);
                return new Response(200, json_encode(['status' => 200, 'message' => 'OK', 'response' => []]));
            });

        $client->getServiceStructure();
    }

    /**
     * Helper to mock a successful POST response.
     * @param array<string, mixed> $responseData
     */
    private function mockPostResponse(string $expectedUrl, array $responseData): void
    {
        $body = json_encode([
            'status' => 200,
            'message' => 'OK',
            'response' => $responseData,
        ]);

        $this->httpClient->method('post')
            ->willReturn(new Response(200, $body));
    }
}
