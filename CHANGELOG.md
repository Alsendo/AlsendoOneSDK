# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-08-18

### Added
- `Address`: `foreign_address_subtype` — destination point network/brand,
  read by the API on the receiver address of linehaul (CBL/Packeta)
  point-to-point services.

### Changed
- Documented money units (grosze) and timestamp formats (`Y-m-d H:i:s` in
  Europe/Warsaw for order fields vs. ISO 8601 for tracking) in DTO
  docblocks; documented valuation behaviour without `service_id`,
  the `foreign_address_id` flow for shipping to pickup points, and the
  `{}` vs `[]` empty-payload signing difference in the README.

## [1.0.0] - 2026-08-18

First stable release.

### Added
- `PushTrackingWebhook` — verification (HMAC-SHA256) and typed parsing of
  incoming push-tracking notifications sent to `push_tracking_url`
  (`PushTrackingNotification`, `PushTrackingStatus`,
  `PushTrackingOperatorStatus` DTOs, `WebhookVerificationException`).
- `AlsendoClientInterface` — type-hint the interface to keep the client
  mockable in consuming applications.
- `registerCustomer()` and `checkData()` — privileged account endpoints
  (`CustomerRegisterRequest`, `CustomerRegisterResponse` DTOs).
- `RetryingHttpClient` — opt-in decorator retrying network failures with
  linear backoff (never retries API error envelopes).
- `LoggingHttpClient` — opt-in PSR-3 logging decorator (never logs
  credentials or signed payloads); `psr/log` added to `require`.
- `SECURITY.md` with a private vulnerability-reporting channel.
- `AlsendoClient::getTracking()` — tracking events for a waybill number
  (`TrackingResponse`, `TrackingEvent` DTOs).
- `AlsendoClient::SANDBOX_URL` constant with the sandbox base URL.
- Default `User-Agent` header (`AlsendoOneSDK/<version> PHP/<version>`) sent by
  the bundled Guzzle adapter. The version is resolved from composer package
  metadata (`AlsendoClient::version()`), not hardcoded.
- `CodRequest`: `bankaccount` / `bankaccount_id` payout fields.
- `Address`: `state_code` (required e.g. for US destinations) and `is_residential`.
- `Order::getPickup()` returns a typed `OrderPickup` object.
- `Order`/`OrderShort`/`ValuationPrice`: `getServiceId()` exposes the raw
  service id.
- Service enum: DPD Pickup to parcel machines (32, 33) and Poczta Polska
  Ukraina (71).
- GitHub Actions CI (PHPUnit on PHP 7.4–8.4 incl. lowest deps, PHPStan,
  PHP-CS-Fixer).

### Fixed
- **Shipment dimensions are now sent as `dimension1/2/3` (cm)** — the API
  silently ignores `length`/`width`/`height` keys in requests, so orders were
  created with default dimensions and undervalued prices.
- Unknown service ids no longer break response parsing (`Service::tryFrom()`;
  `getService()` is now nullable).
- `Order::fromArray()` reads the order-level VAT key as actually serialized by
  the API (`price_var`, with `price_vat` fallback); the value is the VAT rate.
- `externalId` is serialized as boolean `false` when absent — normalized to
  `null` instead of causing a `TypeError`.
- `getPoints()` returns a plain list (the API responds with a 1-indexed object
  map) and no longer sends an empty `subtype` filter.
- `ApiException` carries a descriptive message when the API responds with a
  body that is not a valid response envelope (e.g. an HTML error page).
- Examples and README quick start are now runnable (`setService()` with the
  `Service` enum, pickup hours keyed by date, correct sandbox host).

### Changed
- **Rebrand: `ApaczkaClient` → `AlsendoClient`**, `ApaczkaClientInterface` →
  `AlsendoClientInterface`, `ApaczkaException` → `AlsendoException`
  (adopts the intent of PR #1).
- **Minimum PHP version lowered to 7.4.** The `Service` enum is now a
  class (`AlsendoOne\SDK\Type\Service`) with int constants and cached
  instances via `Service::from()` / `Service::tryFrom()` — two instances of
  the same service id are identical (`===`). Call sites change from
  `Service::DpdCourier` (enum case) to `Service::from(Service::DpdCourier)`.
- `ShipmentRequest::$weight` and `Shipment::$weight`/`$weightBillable` are
  `float` (kilograms) to support sub-kilogram parcels.
- Minimum PHP version aligned between README and composer.json.
- `psr/http-client` and `psr/http-factory` removed from `require` — they were
  never used; the SDK ships its own `HttpClientInterface` abstraction.
