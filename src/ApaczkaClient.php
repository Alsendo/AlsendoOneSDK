<?php

declare(strict_types=1);

namespace AlsendoOne\SDK;

use AlsendoOne\SDK\Auth\SignatureGenerator;
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
use AlsendoOne\SDK\Http\GuzzleHttpClient;
use AlsendoOne\SDK\Http\HttpClientInterface;
use AlsendoOne\SDK\Http\Response;

/**
 * Apaczka API v2 client with typed request/response DTOs.
 *
 * All public methods return strongly-typed DTOs. For raw (untyped) array access,
 * use the {@see request()} method directly, which returns a {@see Response} object
 * with {@see Response::getResponseData()} providing the raw array.
 */
class ApaczkaClient implements ApaczkaClientInterface
{
    /**
     * SDK version, sent in the User-Agent header by the bundled Guzzle adapter.
     */
    public const VERSION = '1.0.0-dev';

    /**
     * Base URL of the sandbox environment. Pass as the $baseUrl constructor
     * argument to test against the sandbox instead of production.
     */
    public const SANDBOX_URL = 'https://panel-sandbox.apaczka.pl/api/v2/';

    private const DEFAULT_BASE_URL = 'https://www.apaczka.pl/api/v2/';

    private SignatureGenerator $signatureGenerator;
    private HttpClientInterface $httpClient;
    private string $baseUrl;

    public function __construct(
        string $appId,
        string $appSecret,
        ?HttpClientInterface $httpClient = null,
        string $baseUrl = self::DEFAULT_BASE_URL
    ) {
        $this->signatureGenerator = new SignatureGenerator($appId, $appSecret);
        $this->httpClient = $httpClient ?? new GuzzleHttpClient();
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    // --- Orders ---

    /**
     * Get order details by ID.
     *
     * @throws ApiException
     */
    public function getOrder(int $orderId): Order
    {
        $data = $this->request('order/' . $orderId . '/')->getResponseData();

        return Order::fromArray($data['order'] ?? $data);
    }

    /**
     * List orders with pagination.
     *
     * @param int $page Page number (starting from 1)
     * @param int $limit Items per page (max 25)
     * @return OrderShort[]
     * @throws ApiException
     */
    public function getOrders(int $page = 1, int $limit = 10): array
    {
        $data = $this->request('orders/', [
            'page' => $page,
            'limit' => min($limit, 25),
        ])->getResponseData();

        return array_map(
            fn (array $item) => OrderShort::fromArray($item),
            $data['orders'] ?? []
        );
    }

    /**
     * Send (create) a new order.
     *
     * @param OrderRequest|array<string, mixed> $orderData Order data including service_id, address, shipment, pickup, notification
     * @throws ApiException
     */
    public function sendOrder($orderData): OrderShort
    {
        $data = $orderData instanceof OrderRequest ? $orderData->toArray() : $orderData;

        $response = $this->request('order_send/', [
            'order' => $data,
        ]);

        return OrderShort::fromArray($response->getResponseData()['order'] ?? []);
    }

    /**
     * Cancel an order.
     *
     * @throws ApiException
     */
    public function cancelOrder(int $orderId): void
    {
        $this->request('cancel_order/' . $orderId . '/');
    }

    // --- Pricing ---

    /**
     * Get price valuation for order parameters.
     *
     * @param OrderRequest|array<string, mixed> $orderData Order data for valuation
     * @throws ApiException
     */
    public function getValuation($orderData): Valuation
    {
        $data = $orderData instanceof OrderRequest ? $orderData->toArray() : $orderData;

        $response = $this->request('order_valuation/', [
            'order' => $data,
        ]);

        return Valuation::fromArray($response->getResponseData());
    }

    // --- Service Structure ---

    /**
     * Get available services, options, package types, pickup types.
     *
     * @throws ApiException
     */
    public function getServiceStructure(): ServiceStructure
    {
        $data = $this->request('service_structure/')->getResponseData();

        return ServiceStructure::fromArray($data);
    }

    // --- Pickup ---

    /**
     * Get available pickup hours for postal code.
     *
     * @param string $postalCode Sender postal code
     * @param Service|null $service Optional service filter
     * @throws ApiException
     */
    public function getPickupHours(string $postalCode, ?Service $service = null): PickupHoursResponse
    {
        $params = ['postal_code' => $postalCode];
        if ($service !== null) {
            $params['service_id'] = $service->value;
        }
        $data = $this->request('pickup_hours/', $params)->getResponseData();

        return PickupHoursResponse::fromArray($data);
    }

    /**
     * Schedule pickup for a single order.
     *
     * @param int $orderId Order ID
     * @param string $date Pickup date (Y-m-d)
     * @param string $hourFrom Pickup time from (HH:MM)
     * @param string $hourTo Pickup time to (HH:MM)
     * @throws ApiException
     */
    public function schedulePickup(int $orderId, string $date, string $hourFrom, string $hourTo): PickupResponse
    {
        $data = $this->request('pickup/' . $orderId . '/', [
            'date' => $date,
            'hour_from' => $hourFrom,
            'hour_to' => $hourTo,
        ])->getResponseData();

        return PickupResponse::fromArray($data);
    }

    /**
     * Schedule batch pickup for multiple orders.
     *
     * @param array<int> $orderIds List of order IDs
     * @param string $date Pickup date (Y-m-d)
     * @param string $hourFrom Pickup time from (HH:MM)
     * @param string $hourTo Pickup time to (HH:MM)
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function scheduleBatchPickup(array $orderIds, string $date, string $hourFrom, string $hourTo): array
    {
        return $this->request('batch_pickup/', [
            'order_ids' => $orderIds,
            'date' => $date,
            'hour_from' => $hourFrom,
            'hour_to' => $hourTo,
        ])->getResponseData();
    }

    // --- Documents ---

    /**
     * Get waybill (shipping label) for an order. Returns PDF as base64.
     *
     * @throws ApiException
     */
    public function getWaybill(int $orderId): WaybillResponse
    {
        $data = $this->request('waybill/' . $orderId . '/')->getResponseData();

        return WaybillResponse::fromArray($data);
    }

    /**
     * Get batch confirmation document (turn-in protocol). Returns PDF as base64.
     *
     * @param array<int> $orderIds List of order IDs
     * @throws ApiException
     */
    public function getTurnIn(array $orderIds): TurnInResponse
    {
        $data = $this->request('turn_in/', [
            'order_ids' => $orderIds,
        ])->getResponseData();

        return TurnInResponse::fromArray($data);
    }

    /**
     * Get dispatch/return code for an order.
     *
     * Returns the carrier's dispatch (or return) code for the given order.
     * If the carrier did not provide a code for this order, the
     * {@see DispatchCodeResponse::getDispatchCode()} value will be null.
     *
     * @throws ApiException
     */
    public function getDispatchCode(int $orderId): DispatchCodeResponse
    {
        $data = $this->request('dispatch_code/' . $orderId . '/')->getResponseData();

        return DispatchCodeResponse::fromArray($data);
    }

    // --- Tracking ---

    /**
     * Get tracking events for a waybill number.
     *
     * @param string $waybillNumber Waybill (shipment) number
     * @throws ApiException
     */
    public function getTracking(string $waybillNumber): TrackingResponse
    {
        $data = $this->request('tracking/' . $waybillNumber . '/')->getResponseData();

        return TrackingResponse::fromArray($data);
    }

    // --- Access Points ---

    /**
     * Get access points (pickup/drop-off locations) for a supplier.
     *
     * @param string $supplier Supplier code (e.g. "INPOST", "DPD", "DHL")
     * @param string $countryCode Country code (default: "PL")
     * @param string $subtype Optional point subtype filter
     * @return AccessPoint[]
     * @throws ApiException
     */
    public function getPoints(string $supplier, string $countryCode = 'PL', string $subtype = ''): array
    {
        $params = ['country_code' => $countryCode];
        if ($subtype !== '') {
            $params['subtype'] = $subtype;
        }
        $data = $this->request('points/' . $supplier . '/', $params)->getResponseData();

        // The API returns points as a 1-indexed object map — normalize to a list.
        return array_values(array_map(
            fn (array $item) => AccessPoint::fromArray($item),
            $data['points'] ?? []
        ));
    }

    // --- Account (privileged endpoints) ---

    /**
     * Register a new customer account and provision API credentials for it.
     *
     * Requires the partner-only "register via API" privilege on the calling
     * application.
     *
     * @param CustomerRegisterRequest|array<string, mixed> $customerData
     * @throws ApiException
     */
    public function registerCustomer($customerData): CustomerRegisterResponse
    {
        $data = $customerData instanceof CustomerRegisterRequest ? $customerData->toArray() : $customerData;

        $response = $this->request('customer_register/', [
            'customer' => $data,
        ]);

        return CustomerRegisterResponse::fromArray($response->getResponseData());
    }

    /**
     * Validate a VAT id against the Apaczka registry.
     *
     * Requires a dedicated privilege and is rate-limited (100 calls per day).
     * The API signals an invalid or unknown VAT id with an error envelope,
     * so this method throws an {@see ApiException} in that case and returns
     * normally when the VAT id checks out.
     *
     * @throws ApiException
     */
    public function checkData(string $vatId): void
    {
        $this->request('check_data/', ['vat_id' => $vatId]);
    }

    // --- Raw request ---

    /**
     * Get the underlying HTTP client.
     */
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    /**
     * Send a raw API request and return the full Response object.
     *
     * Use this method for untyped access to any endpoint. Call
     * {@see Response::getResponseData()} on the result to get the raw array.
     *
     * @param string $route API route with trailing slash
     * @param array<string, mixed> $params Request parameters
     * @return Response
     * @throws ApiException
     */
    public function request(string $route, array $params = []): Response
    {
        $jsonRequest = json_encode((object) $params);
        $expires = time() + 900;

        $signature = $this->signatureGenerator->generate($route, $jsonRequest, $expires);

        $formParams = [
            'app_id' => $this->signatureGenerator->getAppId(),
            'request' => $jsonRequest,
            'expires' => (string) $expires,
            'signature' => $signature,
        ];

        $response = $this->httpClient->post($this->baseUrl . $route, $formParams);

        if (!$response->isSuccess()) {
            throw new ApiException($response);
        }

        return $response;
    }
}
