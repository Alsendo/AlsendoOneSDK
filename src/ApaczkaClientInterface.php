<?php

declare(strict_types=1);

namespace AlsendoOne\SDK;

use AlsendoOne\SDK\DTO\Request\CustomerRegisterRequest;
use AlsendoOne\SDK\DTO\Request\OrderRequest;
use AlsendoOne\SDK\DTO\Response\AccessPoint;
use AlsendoOne\SDK\DTO\Response\CustomerRegisterResponse;
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
use AlsendoOne\SDK\Http\Response;

/**
 * Contract of the Apaczka API v2 client — implement or mock this interface
 * in consuming applications instead of depending on {@see ApaczkaClient}
 * directly.
 */
interface ApaczkaClientInterface
{
    /**
     * @throws ApiException
     */
    public function getOrder(int $orderId): Order;

    /**
     * @return OrderShort[]
     * @throws ApiException
     */
    public function getOrders(int $page = 1, int $limit = 10): array;

    /**
     * @param OrderRequest|array<string, mixed> $orderData
     * @throws ApiException
     */
    public function sendOrder($orderData): OrderShort;

    /**
     * @throws ApiException
     */
    public function cancelOrder(int $orderId): void;

    /**
     * @param OrderRequest|array<string, mixed> $orderData
     * @throws ApiException
     */
    public function getValuation($orderData): Valuation;

    /**
     * @throws ApiException
     */
    public function getServiceStructure(): ServiceStructure;

    /**
     * @throws ApiException
     */
    public function getPickupHours(string $postalCode, ?Service $service = null): PickupHoursResponse;

    /**
     * @throws ApiException
     */
    public function schedulePickup(int $orderId, string $date, string $hourFrom, string $hourTo): PickupResponse;

    /**
     * @param array<int> $orderIds
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function scheduleBatchPickup(array $orderIds, string $date, string $hourFrom, string $hourTo): array;

    /**
     * @throws ApiException
     */
    public function getWaybill(int $orderId): WaybillResponse;

    /**
     * @param array<int> $orderIds
     * @throws ApiException
     */
    public function getTurnIn(array $orderIds): TurnInResponse;

    /**
     * @throws ApiException
     */
    public function getDispatchCode(int $orderId): DispatchCodeResponse;

    /**
     * @throws ApiException
     */
    public function getTracking(string $waybillNumber): TrackingResponse;

    /**
     * @return AccessPoint[]
     * @throws ApiException
     */
    public function getPoints(string $supplier, string $countryCode = 'PL', string $subtype = ''): array;

    /**
     * @param CustomerRegisterRequest|array<string, mixed> $customerData
     * @throws ApiException
     */
    public function registerCustomer($customerData): CustomerRegisterResponse;

    /**
     * @throws ApiException
     */
    public function checkData(string $vatId): void;

    /**
     * @param array<string, mixed> $params
     * @throws ApiException
     */
    public function request(string $route, array $params = []): Response;
}
