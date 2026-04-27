# AlsendoOne SDK

[![Latest Stable Version](https://img.shields.io/packagist/v/alsendo/alsendo-one-sdk.svg)](https://packagist.org/packages/alsendo/alsendo-one-sdk)
[![PHP Version](https://img.shields.io/packagist/php-v/alsendo/alsendo-one-sdk.svg)](https://packagist.org/packages/alsendo/alsendo-one-sdk)
[![License](https://img.shields.io/packagist/l/alsendo/alsendo-one-sdk.svg)](https://github.com/Alsendo/AlsendoOneSDK/blob/main/LICENSE)

Official PHP client for [Alsendo API v2](https://www.apaczka.pl/api/v2/). Ship parcels with 20+ couriers through a single integration.

- Strongly-typed request/response DTOs
- Fluent builders for orders, addresses, shipments, pickups
- Automatic HMAC-SHA256 request signing
- Pluggable HTTP layer (Guzzle 7 adapter included, or bring your own)
- PSR-3 logger integration
- RFC 9110 compliant `User-Agent` on every request

## Table of contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Quick start](#quick-start)
- [Conventions](#conventions)
- [SDK reference](#sdk-reference)
  - [Service structure](#service-structure)
  - [Pricing — valuation](#pricing--valuation)
  - [Orders](#orders)
  - [Pickup scheduling](#pickup-scheduling)
  - [Documents](#documents)
  - [Access points](#access-points)
  - [Raw request](#raw-request)
- [Building requests](#building-requests)
  - [OrderRequest](#orderrequest)
  - [Address](#address)
  - [ShipmentRequest](#shipmentrequest)
  - [PickupRequest](#pickuprequest)
  - [NotificationRequest](#notificationrequest)
  - [CodRequest](#codrequest)
  - [CustomsDataRequest](#customsdatarequest)
- [Error handling](#error-handling)
- [Logging](#logging)
- [User-Agent](#user-agent)
- [Custom HTTP client](#custom-http-client)
- [Custom base URL](#custom-base-url)
- [Contributing](#contributing)
- [License](#license)

## Requirements

- PHP 7.4 or higher
- `ext-json`
- `psr/log` (any of `^1.0 || ^2.0 || ^3.0`)
- An HTTP client implementing `HttpClientInterface` (Guzzle 7 adapter included)

## Installation

```bash
composer require alsendo/alsendo-one-sdk
```

To use the built-in Guzzle adapter (default), also require:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

## Quick start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use AlsendoOne\SDK\AlsendoClient;
use AlsendoOne\SDK\DTO\Address;
use AlsendoOne\SDK\DTO\Request\OrderRequest;
use AlsendoOne\SDK\DTO\Request\PickupRequest;
use AlsendoOne\SDK\DTO\Request\ShipmentRequest;
use AlsendoOne\SDK\Type\Service;

$client = new AlsendoClient('your_app_id', 'your_app_secret');

$order = OrderRequest::create()
    ->setService(Service::from(Service::DpdCourier))
    ->setSenderAddress(new Address(
        'Firma Sp. z o.o.', 'Jan Kowalski', 'jan@example.com', '500100200',
        'Marszałkowska 1/10', null, '00-001', 'Warszawa', 'PL'
    ))
    ->setReceiverAddress(new Address(
        'Anna Nowak', null, 'anna@example.com', '600300400',
        'Długa 15', null, '80-831', 'Gdańsk', 'PL'
    ))
    ->addShipment(new ShipmentRequest('PACKAGE', 2500, 400, 300, 200))
    ->setPickup(new PickupRequest('COURIER', '2026-04-28', '10:00', '16:00'));

$valuation = $client->getValuation($order);   // price quote
$result    = $client->sendOrder($order);      // create the shipment

echo "Order ID: {$result->getId()}\n";
echo "Waybill: {$result->getWaybillNumber()}\n";
```

A complete end-to-end example lives in [`examples/order_send.php`](examples/order_send.php).

## Conventions

Knowing these few facts up front saves a lot of debugging:

| Topic        | Convention                                                                                                              |
| ------------ | ----------------------------------------------------------------------------------------------------------------------- |
| Prices       | All amounts are in **groszy** (1 PLN = 100 groszy). Divide by 100 to display PLN.                                       |
| Weight       | Grams (e.g. `2500` = 2.5 kg).                                                                                           |
| Dimensions   | Millimeters. Constructor takes `length, width, height`; the SDK serialises them as `dimension1/2/3` for the API.        |
| Dates        | `Y-m-d` (e.g. `2026-04-28`).                                                                                            |
| Hours        | `HH:MM` (e.g. `10:00`).                                                                                                 |
| Documents    | `getWaybill()` / `getTurnIn()` return PDF as a base64 string.                                                           |
| Service IDs  | Listed as constants on `AlsendoOne\SDK\Type\Service`. Use `getServiceStructure()` to discover IDs enabled for your account. |

Credentials are obtained in the [Apaczka panel](https://www.apaczka.pl/) under **Settings → API**. The signature `HMAC-SHA256(app_id:route:json_request:expires, app_secret)` is computed for you; signatures expire after 15 minutes and are regenerated per request.

---

## SDK reference

Every method on `AlsendoClient` returns a typed DTO (or `void` for `cancelOrder`). Anything not covered by a typed method can still be reached via [`request()`](#raw-request).

### Service structure

Discover the services, options, package types and pickup types available for **your** account.

```php
$structure = $client->getServiceStructure();

foreach ($structure->getServices() as $id => $service) {
    echo "{$id}: {$service['name']}\n";
}
```

Returns: `AlsendoOne\SDK\DTO\Response\ServiceStructure`.

### Pricing — valuation

Get a price quote without creating an order. Same shape as `sendOrder()` — pass a built `OrderRequest` (or a raw array) and read prices per service.

```php
$valuation = $client->getValuation($order);

foreach ($valuation->getPriceTable() as $price) {
    printf(
        "Service %d: %.2f PLN net / %.2f PLN gross\n",
        $price->getService()->value,
        $price->getPrice() / 100,        // groszy → PLN
        $price->getPriceGross() / 100
    );
}

// Or look up a single service directly:
$dpd = $valuation->getPriceForService(Service::from(Service::DpdCourier));
```

Returns: `AlsendoOne\SDK\DTO\Response\Valuation`.

### Orders

#### Create

```php
$result = $client->sendOrder($order);

echo $result->getId();              // int  — your order ID
echo $result->getWaybillNumber();   // string — courier label number
echo $result->getTrackingUrl();
echo $result->getStatus();
```

Returns: `AlsendoOne\SDK\DTO\Response\OrderShort`.

#### Read one

```php
$order = $client->getOrder(123456);

echo $order->getStatus();
foreach ($order->getShipments() as $s) {
    echo "{$s->getWaybillNumber()} — {$s->getWeight()} g\n";
}
```

Returns: `AlsendoOne\SDK\DTO\Response\Order` (the full record, including shipments, addresses, prices).

#### List

```php
// page 1, 10 per page (max 25)
$orders = $client->getOrders(1, 10);

foreach ($orders as $order) {
    echo "{$order->getId()} — {$order->getStatus()}\n";
}
```

Returns: `OrderShort[]`. The `$limit` argument is automatically capped at 25.

#### Cancel

```php
$client->cancelOrder(123456);   // returns void; throws ApiException on failure
```

### Pickup scheduling

#### Available pickup hours

```php
$response = $client->getPickupHours('00-001');                       // any service
$response = $client->getPickupHours('00-001', Service::from(Service::DpdCourier));

foreach ($response->getHours() as $day) {
    echo $day->getDate() . "\n";
    foreach ($day->getServices() as $serviceId => $window) {
        echo "  service {$serviceId}: {$window['from']}–{$window['to']}\n";
    }
}
```

Returns: `AlsendoOne\SDK\DTO\Response\PickupHoursResponse`.

#### Schedule a single pickup

```php
$pickup = $client->schedulePickup(
    $orderId,
    '2026-04-28',   // date Y-m-d
    '10:00',        // hour from
    '16:00'         // hour to
);

echo $pickup->getForeignCourierId();
```

Returns: `AlsendoOne\SDK\DTO\Response\PickupResponse`.

#### Schedule batch pickup

```php
$result = $client->scheduleBatchPickup(
    [123, 124, 125],   // order IDs
    '2026-04-28',
    '10:00',
    '16:00'
);
```

Returns: raw `array` (this endpoint is courier-specific; consult the Apaczka docs for the exact payload).

### Documents

#### Waybill (shipping label)

```php
$waybill = $client->getWaybill(123456);

$pdf = base64_decode($waybill->getWaybill());
file_put_contents('label.pdf', $pdf);

echo $waybill->getType();   // e.g. 'pdf'
```

Returns: `AlsendoOne\SDK\DTO\Response\WaybillResponse`.

#### Turn-in protocol (batch confirmation)

```php
$turnIn = $client->getTurnIn([123, 124, 125]);

$pdf = base64_decode($turnIn->getTurnIn());
file_put_contents('turn-in.pdf', $pdf);
```

Returns: `AlsendoOne\SDK\DTO\Response\TurnInResponse`.

### Access points

Pickup/drop-off points (lockers, partner shops, etc.) for a given supplier.

```php
$points = $client->getPoints('INPOST');                                // PL, all subtypes
$points = $client->getPoints('INPOST', 'PL', 'PARCEL_LOCKER');         // filter by subtype
$points = $client->getPoints('INPOST', 'PL', '', '00-001');            // restrict by postal code

foreach ($points as $point) {
    echo "{$point->getName()} ({$point->getForeignAddressId()})\n";
}
```

Returns: `AccessPoint[]`.

### Raw request

For endpoints not yet wrapped by a typed method, or when you want full control over the response shape:

```php
$response = $client->request('some/endpoint/', ['key' => 'value']);

$status = $response->getApiStatus();      // int  — Alsendo's own status code (200 = OK)
$msg    = $response->getMessage();        // string — human-readable
$data   = $response->getResponseData();   // array — the `response` payload
```

Returns: `AlsendoOne\SDK\Http\Response`.

---

## Building requests

All request DTOs live under `AlsendoOne\SDK\DTO\Request`. They use a fluent builder style (`create()` + chainable setters) and produce a normalised array via `toArray()` — the SDK calls this for you, so you only ever assemble the object.

### OrderRequest

The aggregate used by `sendOrder()` and `getValuation()`.

```php
use AlsendoOne\SDK\DTO\Request\OrderRequest;
use AlsendoOne\SDK\Type\Service;

$order = OrderRequest::create()
    ->setService(Service::from(Service::DpdCourier))
    ->setSenderAddress($sender)        // Address
    ->setReceiverAddress($receiver)    // Address
    ->addShipment($shipment)           // ShipmentRequest (call multiple times for multi-package)
    ->setPickup($pickup)               // PickupRequest
    ->setNotification($notifications)  // NotificationRequest
    ->setCod($cod)                     // CodRequest (cash on delivery)
    ->setShipmentValue(15000)          // declared value in groszy (150 PLN)
    ->setContent('Electronics')
    ->setComment('Handle with care')
    ->setOption('saturday_delivery', true)   // arbitrary courier-specific options
    ->setIsZebra(true)                       // request Zebra-format label
    ->setPushTrackingUrl('https://example.com/webhook');
```

You may also pass a plain array to `sendOrder()` / `getValuation()` if you prefer.

### Address

```php
use AlsendoOne\SDK\DTO\Address;

$address = new Address(
    'Firma Sp. z o.o.',     // name
    'Jan Kowalski',         // contactPerson (nullable)
    'jan@example.com',      // email
    '500100200',            // phone
    'Marszałkowska 1/10',   // line1
    null,                   // line2
    '00-001',               // postalCode
    'Warszawa',             // city
    'PL'                    // countryCode (ISO 3166-1 alpha-2)
);
```

For pickup-point destinations, use the optional `foreignAddressId` (10th argument) instead of postal address fields.

### ShipmentRequest

```php
use AlsendoOne\SDK\DTO\Request\ShipmentRequest;

// Constructor: type, weight (g), length (mm), width (mm), height (mm)
$shipment = new ShipmentRequest('PACKAGE', 2500, 400, 300, 200);

$shipment
    ->setContent('Books')
    ->setComment('Fragile');
```

International shipments may require customs declarations:

```php
use AlsendoOne\SDK\DTO\Request\CustomsDataRequest;

$shipment->addCustomsData(new CustomsDataRequest(
    'Hardcover book',  // description
    1,                 // quantity
    5000,              // unit price (groszy)
    500,               // weight (g)
    '4901.99',         // HS code (optional)
    'PL'               // country of origin (optional)
));
```

> The API expects dimensions as `dimension1/2/3`. The SDK takes them as `length`, `width`, `height` and converts on serialisation — you don't need to think about it.

### PickupRequest

```php
use AlsendoOne\SDK\DTO\Request\PickupRequest;

// Courier comes to pick up the parcel from the sender
$pickup = new PickupRequest('COURIER', '2026-04-28', '10:00', '16:00');

// Sender drops off at a point — date/hours optional
$pickup = new PickupRequest('SELF');
```

### NotificationRequest

Configure SMS/email events to receiver and/or sender.

```php
use AlsendoOne\SDK\DTO\Request\NotificationRequest;

$notifications = NotificationRequest::create()
    ->setNew(true, false, true)        // (receiverEmail, receiverSms, senderEmail, senderSms)
    ->setSent(true)                    // notify receiver by email when handed to courier
    ->setDelivered(true)               // notify receiver by email on delivery
    ->setException(true, true);        // notify on delivery exception (e.g. attempt failed)
```

### CodRequest

Cash on delivery (in groszy):

```php
use AlsendoOne\SDK\DTO\Request\CodRequest;

$cod = new CodRequest(15000, 'PLN');   // 150 PLN
```

### CustomsDataRequest

See the [`ShipmentRequest`](#shipmentrequest) example above.

---

## Error handling

All SDK exceptions extend `AlsendoOne\SDK\Exception\AlsendoException`.

```php
use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Exception\AuthenticationException;
use AlsendoOne\SDK\Exception\ConnectionException;
use AlsendoOne\SDK\Exception\ValidationException;

try {
    $order = $client->getOrder(123456);
} catch (AuthenticationException $e) {
    // Invalid credentials or expired signature
} catch (ValidationException $e) {
    // Request rejected for invalid parameters
} catch (ApiException $e) {
    // Alsendo returned a non-200 status
    $code = $e->getCode();                 // API status code
    $msg  = $e->getMessage();              // human-readable
    $data = $e->getResponseData();         // raw response payload (array)
    $resp = $e->getResponse();             // Http\Response
} catch (ConnectionException $e) {
    // Network error: timeout, DNS failure, TLS, etc.
}
```

`ApiException::getResponseData()` is the source of truth when you need to render carrier-specific validation messages back to the user.

## Logging

The client accepts any [PSR-3](https://www.php-fig.org/psr/psr-3/) logger. Without one it uses `Psr\Log\NullLogger` (no-op).

```php
use AlsendoOne\SDK\AlsendoClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$logger = new Logger('alsendo');
$logger->pushHandler(new StreamHandler(__DIR__ . '/var/log/alsendo.log', Logger::DEBUG));

$client = new AlsendoClient(
    'app_id',
    'app_secret',
    null,            // default Guzzle adapter
    null,            // default base URL
    $logger
);
```

What gets logged:

- **`debug`** — outgoing request. Context: `route`, `url`, `params`, `user_agent`
- **`debug`** — successful response. Context: `route`, `http_status`, `body` (truncated at 8 KB)
- **`error`** — API returned a non-200 status. Context: `route`, `http_status`, `api_status`, `message`, `body`
- **`error`** — HTTP/network exception. Context: `route`, `error`

Don't enable `debug` in production — request bodies and response payloads may contain customer addresses and tracking info.

## User-Agent

Every request carries a [RFC 9110](https://www.rfc-editor.org/rfc/rfc9110#name-user-agent) compliant `User-Agent` header so Alsendo can correlate API traffic to the SDK and PHP runtime:

```
AlsendoOneSDK/1.0.0 (PHP 7.4.33; Linux)
```

The string is built from `AlsendoClient::VERSION`, `PHP_VERSION` and `PHP_OS_FAMILY`. Inspect it on a configured client with `getUserAgent()`:

```php
echo $client->getUserAgent();   // AlsendoOneSDK/1.0.0 (PHP 7.4.33; Linux)
```

## Custom HTTP client

The SDK ships with a Guzzle 7 adapter. Replace it with anything (cURL, Symfony HttpClient, mock for tests…) by implementing `HttpClientInterface`:

```php
use AlsendoOne\SDK\AlsendoClient;
use AlsendoOne\SDK\Http\HttpClientInterface;
use AlsendoOne\SDK\Http\Response;

class MyHttpClient implements HttpClientInterface
{
    public function post(string $url, array $formParams, array $headers = []): Response
    {
        // ...your transport, must honour the $headers array (User-Agent etc.)
        $httpStatus = 200;
        $body = '{"status": 200, "response": {}}';

        return new Response($httpStatus, $body);
    }

    public function get(string $url, array $queryParams, array $headers = []): Response
    {
        // ...
    }
}

$client = new AlsendoClient('app_id', 'app_secret', new MyHttpClient());
```

The `$headers` argument is optional (`= []`); existing implementations will keep compiling, but won't propagate the User-Agent until they forward it.

## Custom base URL

Useful for staging or proxy setups:

```php
$client = new AlsendoClient(
    'app_id',
    'app_secret',
    null,                                    // default Guzzle client
    'https://sandbox.apaczka.pl/api/v2/'     // custom base URL
);
```

A trailing slash is added automatically if missing.

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Make your changes, with tests
4. Run the full check suite:
   ```bash
   composer test          # PHPUnit
   composer phpstan       # PHPStan
   composer cs-check      # PHP-CS-Fixer (dry run)
   ```
5. Commit, push, open a PR against `main`

Code follows PSR-12.

## License

Released under the [MIT License](LICENSE).
