<?php

declare(strict_types=1);

namespace AlsendoOne\SDK;

use AlsendoOne\SDK\Auth\SignatureGenerator;
use AlsendoOne\SDK\DTO\Request\OrderRequest;
use AlsendoOne\SDK\DTO\Response\AccessPoint;
use AlsendoOne\SDK\DTO\Response\Order;
use AlsendoOne\SDK\DTO\Response\OrderShort;
use AlsendoOne\SDK\DTO\Response\PickupHoursResponse;
use AlsendoOne\SDK\DTO\Response\PickupResponse;
use AlsendoOne\SDK\DTO\Response\ServiceStructure;
use AlsendoOne\SDK\DTO\Response\TurnInResponse;
use AlsendoOne\SDK\DTO\Response\Valuation;
use AlsendoOne\SDK\DTO\Response\WaybillResponse;
use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Http\GuzzleHttpClient;
use AlsendoOne\SDK\Http\HttpClientInterface;
use AlsendoOne\SDK\Http\Response;
use AlsendoOne\SDK\Type\Service;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Alsendo API v2 client with typed request/response DTOs.
 *
 * All public methods return strongly-typed DTOs. For raw (untyped) array access,
 * use the {@see request()} method directly, which returns a {@see Response} object
 * with {@see Response::getResponseData()} providing the raw array.
 */
class AlsendoClient
{
    public const VERSION = '1.0.0';

    private const DEFAULT_BASE_URL = 'https://www.apaczka.pl/api/v2/';

    private SignatureGenerator $signatureGenerator;
    private HttpClientInterface $httpClient;
    private string $baseUrl;
    private LoggerInterface $logger;
    private string $userAgent;

    public function __construct(
        string $appId,
        string $appSecret,
        ?HttpClientInterface $httpClient = null,
        string $baseUrl = self::DEFAULT_BASE_URL,
        ?LoggerInterface $logger = null
    ) {
        $this->signatureGenerator = new SignatureGenerator($appId, $appSecret);
        $this->httpClient = $httpClient ?? new GuzzleHttpClient();
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->logger = $logger ?? new NullLogger();
        $this->userAgent = self::buildUserAgent();
    }

    /**
     * Build RFC 9110 §10.1.5 compliant User-Agent string.
     *
     * Format: "AlsendoOneSDK/<sdk-version> (PHP <php-version>; <os-family>)"
     * Example: "AlsendoOneSDK/1.0.0 (PHP 7.4.33; Linux)"
     */
    private static function buildUserAgent(): string
    {
        return sprintf(
            'AlsendoOneSDK/%s (PHP %s; %s)',
            self::VERSION,
            PHP_VERSION,
            PHP_OS_FAMILY
        );
    }

    /**
     * Returns the User-Agent header value sent with every request.
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
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
    public function getPoints(string $supplier, string $countryCode = 'PL', string $subtype = '', string $postalCode = ''): array
    {
        $params = ['country_code' => $countryCode];
        if ($subtype !== '') {
            $params['subtype'] = $subtype;
        }
        if ($postalCode !== '') {
            $params['postal_code'] = $postalCode;
        }

        $data = $this->request('points/' . $supplier . '/', $params)->getResponseData();

        return array_map(
            fn (array $item) => AccessPoint::fromArray($item),
            $data['points'] ?? []
        );
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

        $url = $this->baseUrl . $route;
        $headers = ['User-Agent' => $this->userAgent];

        $this->logger->debug('SDK request', [
            'route'      => $route,
            'url'        => $url,
            'params'     => $params,
            'user_agent' => $this->userAgent,
        ]);

        try {
            $response = $this->httpClient->post($url, $formParams, $headers);
        } catch (\Throwable $e) {
            $this->logger->error('SDK connection error', [
                'route' => $route,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        $rawBody = $response->getBody();
        if (!$response->isSuccess()) {
            $this->logger->error('SDK API error', [
                'route'       => $route,
                'http_status' => $response->getHttpStatusCode(),
                'api_status'  => $response->getApiStatus(),
                'message'     => $response->getMessage(),
                'body'        => $rawBody,
            ]);
            throw new ApiException($response);
        }

        $this->logger->debug('SDK response', [
            'route'       => $route,
            'http_status' => $response->getHttpStatusCode(),
            'body'        => strlen($rawBody) > 8000 ? substr($rawBody, 0, 8000) . '...[truncated]' : $rawBody,
        ]);

        return $response;
    }
}
