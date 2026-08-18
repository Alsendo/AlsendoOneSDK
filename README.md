# AlsendoOne SDK

<!-- badges placeholder -->
[![Latest Stable Version](https://img.shields.io/packagist/v/alsendo/alsendo-one-sdk.svg)](https://packagist.org/packages/alsendo/alsendo-one-sdk)
[![PHP Version](https://img.shields.io/packagist/php-v/alsendo/alsendo-one-sdk.svg)](https://packagist.org/packages/alsendo/alsendo-one-sdk)
[![License](https://img.shields.io/packagist/l/alsendo/alsendo-one-sdk.svg)](https://github.com/Alsendo/AlsendoOneSDK/blob/main/LICENSE)

Official PHP client for [Apaczka API v2](https://www.apaczka.pl/api/v2/). Ship parcels with 20+ couriers through a single integration.

## Requirements

- PHP 8.1 or higher
- `ext-json`
- An HTTP client implementing `HttpClientInterface` (Guzzle 7 adapter included)

## Installation

```bash
composer require alsendo/alsendo-one-sdk
```

If you want to use the built-in Guzzle adapter, also install Guzzle:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

## Quick start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use AlsendoOne\SDK\ApaczkaClient;

$client = new ApaczkaClient(
    'your_app_id',
    'your_app_secret'
);

// Fetch available services
$structure = $client->getServiceStructure();

foreach ($structure->getServices() as $service) {
    echo $service['name'] . ' (ID: ' . $service['service_id'] . ')' . PHP_EOL;
}
```

## Authentication

Every API request is signed with **HMAC-SHA256**. The SDK handles this automatically -- you only need to provide your `app_id` and `app_secret` when creating the client.

The signature is computed from four components:

```
HMAC-SHA256(app_id:route:json_request:expires, app_secret)
```

Each signature is valid for 15 minutes. The SDK generates `expires` timestamps internally, so there is nothing to manage on your end.

You can obtain your API credentials in the [Apaczka panel](https://www.apaczka.pl/) under **Settings > API**.

## Available methods

### Service structure

| Method | Description |
|--------|-------------|
| `getServiceStructure()` | Get available services, options, and package types |

### Access points

| Method | Description |
|--------|-------------|
| `getPoints(string $supplier, string $countryCode = 'PL', string $subtype = '')` | Get pickup/drop-off points for a courier (e.g. InPost lockers) |

### Pricing

| Method | Description |
|--------|-------------|
| `getValuation(array $orderData)` | Get a price quote for given shipment parameters. Prices are returned **in groszy** (1 PLN = 100 groszy) |

### Orders

| Method | Description |
|--------|-------------|
| `sendOrder(array $orderData)` | Create a new shipment order |
| `getOrder(int $orderId)` | Get details of a single order |
| `getOrders(int $page = 1, int $limit = 10)` | List orders with pagination (max 25 per page) |
| `cancelOrder(int $orderId)` | Cancel an order |

### Pickup scheduling

| Method | Description |
|--------|-------------|
| `getPickupHours(string $postalCode, ?Service $service = null)` | Get available pickup time windows for a postal code |
| `schedulePickup(int $orderId, string $date, string $hourFrom, string $hourTo)` | Schedule courier pickup for one order |
| `scheduleBatchPickup(array $orderIds, string $date, string $hourFrom, string $hourTo)` | Schedule courier pickup for multiple orders |

### Tracking

| Method | Description |
|--------|-------------|
| `getTracking(string $waybillNumber)` | Get tracking events (normalized status, carrier status, place, timestamp) for a waybill |

### Documents

| Method | Description |
|--------|-------------|
| `getWaybill(int $orderId)` | Get shipping label as base64-encoded PDF |
| `getTurnIn(array $orderIds)` | Get batch turn-in confirmation as base64-encoded PDF |
| `getDispatchCode(int $orderId)` | Get carrier dispatch/return code for an order |

### Account (privileged)

These endpoints require dedicated partner privileges on the calling application.

| Method | Description |
|--------|-------------|
| `registerCustomer(CustomerRegisterRequest\|array $customerData)` | Register a new customer account; returns provisioned API credentials |
| `checkData(string $vatId)` | Validate a VAT id (rate-limited to 100 calls/day); throws on an invalid id |

### Raw request

| Method | Description |
|--------|-------------|
| `request(string $route, array $params = [])` | Send a signed request to any API endpoint. Returns a `Response` object |

All client methods are also described by `ApaczkaClientInterface` — type-hint
the interface in your application to keep the client mockable in tests.

## Tracking webhooks

When you create an order with `push_tracking_url`, the platform POSTs a signed
JSON notification to that URL on every status change. Use
`PushTrackingWebhook` to verify the signature (computed with your own
`app_secret`) and parse the payload:

```php
use AlsendoOne\SDK\Exception\WebhookVerificationException;
use AlsendoOne\SDK\Webhook\PushTrackingWebhook;

$webhook = new PushTrackingWebhook('your_app_id', 'your_app_secret');

try {
    $notification = $webhook->parse(file_get_contents('php://input'));
} catch (WebhookVerificationException $e) {
    http_response_code(400);
    exit;
}

foreach ($notification->getStatuses() as $status) {
    // e.g. "ON_THE_WAY", "OUT_FOR_DELIVERY", "DELIVERED", "RETURNED"
    updateShipmentStatus($notification->getOrderNumber(), $status->getStatus());
}

http_response_code(200); // acknowledge the notification
```

Notes:

- Respond with HTTP 200, otherwise the platform may retry the delivery.
- Push tracking is only registered for supported couriers; `statuses` may be
  an empty array.
- Timestamps are `Y-m-d\TH:i:s` without an offset, in Europe/Warsaw time.

## Error handling

The SDK throws exceptions extending `AlsendoOne\SDK\Exception\ApaczkaException`:

- `ApiException` — the API returned an error. Note that the Apaczka API responds
  with HTTP 200 even on failures; errors are signalled by a non-200 `status`
  field in the response envelope (in practice always `400`), with the reason in
  the `message` field.
- `ConnectionException` — the HTTP request itself failed (timeout, DNS failure,
  non-200 HTTP status, etc.).

```php
use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Exception\ConnectionException;

try {
    $order = $client->getOrder(123456);
} catch (ApiException $e) {
    // API returned an error envelope, e.g. "Order not found."
    echo 'API error ' . $e->getCode() . ': ' . $e->getMessage();

    // Access the full response
    $response = $e->getResponse();
    $data = $e->getResponseData();
} catch (ConnectionException $e) {
    // Network error (timeout, DNS failure, etc.)
    echo 'Connection error: ' . $e->getMessage();
}
```

The API does not distinguish authentication or validation failures by status
code (everything is `400`) — inspect the exception message if you need to tell
them apart.

## Custom HTTP client

The SDK uses Guzzle by default. You can replace it with any HTTP client by implementing `HttpClientInterface`:

```php
use AlsendoOne\SDK\Http\HttpClientInterface;
use AlsendoOne\SDK\Http\Response;

class MyHttpClient implements HttpClientInterface
{
    public function post(string $url, array $formParams): Response
    {
        // Your implementation using cURL, Symfony HttpClient, etc.
        $httpStatus = 200;
        $body = '{"status": 200, "response": {}}';

        return new Response($httpStatus, $body);
    }

    public function get(string $url, array $queryParams): Response
    {
        // ...
    }
}

$client = new ApaczkaClient('app_id', 'app_secret', new MyHttpClient());
```

The bundled adapter accepts any Guzzle config options — for example to raise
the timeouts (defaults: 30 s request, 10 s connect):

```php
use AlsendoOne\SDK\Http\GuzzleHttpClient;

$client = new ApaczkaClient(
    'app_id',
    'app_secret',
    new GuzzleHttpClient(['timeout' => 120])
);
```

### Retry and logging decorators

Two optional decorators wrap any `HttpClientInterface` implementation:

```php
use AlsendoOne\SDK\Http\GuzzleHttpClient;
use AlsendoOne\SDK\Http\LoggingHttpClient;
use AlsendoOne\SDK\Http\RetryingHttpClient;

$httpClient = new LoggingHttpClient(
    new RetryingHttpClient(new GuzzleHttpClient(), maxRetries: 2, delayMs: 500),
    $psrLogger
);

$client = new ApaczkaClient('app_id', 'app_secret', $httpClient);
```

`RetryingHttpClient` retries only network failures (`ConnectionException`),
never API error envelopes. Beware: a network failure does not guarantee the
request did not reach the server — retrying non-idempotent operations such as
`sendOrder()` may create duplicate orders.

`LoggingHttpClient` logs the URL, statuses and duration through any PSR-3
logger; request parameters (credentials, signed payload) are never logged.

## Sandbox

To test against the sandbox environment, pass `ApaczkaClient::SANDBOX_URL` as
the base URL (sandbox credentials are separate from production ones):

```php
$client = new ApaczkaClient(
    'app_id',
    'app_secret',
    null,                           // use default Guzzle client
    ApaczkaClient::SANDBOX_URL
);
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Make your changes
4. Run the test suite and static analysis:
   ```bash
   composer test          # PHPUnit
   composer phpstan       # PHPStan
   composer cs-check      # PHP-CS-Fixer (dry run)
   ```
5. Commit and push your branch
6. Open a pull request against `main`

Please follow PSR-12 coding standards.

## License

This package is open-source software licensed under the [MIT License](LICENSE).
