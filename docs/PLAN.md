# Plan: AlsendoOne SDK — publiczny klient PHP dla Apaczka API v2

## 1. Informacje o projekcie

**Projekt:** AlsendoOne
**Repozytorium:** `github.com/Alsendo/AlsendoOneSDK`
**Composer:** `alsendo/alsendo-one-sdk`
**Minimalne wymagania:** PHP 7.4+ (kompatybilne z monorepo), z opcjonalnym wsparciem PHP 8.0+

## 2. Zakres endpointów

| Endpoint | Metoda | Opis |
|----------|--------|------|
| `service_structure/` | POST | Struktura serwisów (usługi, opcje, typy paczek) |
| `points/{supplier}/` | POST | Lista punktów odbioru wg kuriera |
| `order_valuation/` | POST | Wycena przesyłki |
| `order_send/` | POST | Nadanie zlecenia |
| `order/{id}/` | GET | Szczegóły zlecenia |
| `orders/` | GET | Lista zleceń (paginacja) |
| `cancel_order/{id}/` | POST | Anulowanie zlecenia |
| `pickup_hours/` | POST | Dostępne godziny odbioru |
| `pickup/{id}/` | POST | Zamówienie kuriera (pojedyncze) |
| `batch_pickup/` | POST | Zamówienie kuriera (batch) |
| `waybill/{id}/` | GET | Pobranie listu przewozowego (PDF base64) |
| `turn_in/` | POST | Potwierdzenie nadania (PDF base64) |

Endpointy uprzywilejowane (`customer_register`, `check_data`, `check_orders_by_token` itp.) — faza 2.

## 3. Struktura pakietu

```
AlsendoOneSDK/
├── composer.json
├── LICENSE                     # MIT
├── README.md
├── docs/
│   └── PLAN.md
├── src/
│   ├── ApaczkaClient.php       # Główna fasada API
│   ├── Auth/
│   │   └── SignatureGenerator.php  # HMAC-SHA256 signing
│   ├── Http/
│   │   ├── HttpClientInterface.php # Abstrakcja HTTP (PSR-18 compatible)
│   │   ├── GuzzleHttpClient.php    # Domyślna implementacja (Guzzle)
│   │   └── Response.php            # Wrapper na odpowiedź API
│   ├── Exception/
│   │   ├── ApaczkaException.php
│   │   ├── AuthenticationException.php
│   │   ├── ValidationException.php
│   │   └── ApiException.php        # status=400 z API
│   ├── Request/
│   │   ├── OrderSendRequest.php    # DTO do nadania zlecenia
│   │   ├── OrderValuationRequest.php
│   │   ├── PickupRequest.php
│   │   ├── PointsRequest.php
│   │   └── Address.php             # Value object adresu
│   └── Response/
│       ├── Order.php
│       ├── OrderList.php
│       ├── ServiceStructure.php
│       ├── Valuation.php
│       ├── PickupHours.php
│       └── Points.php
├── tests/
│   ├── Unit/
│   │   ├── Auth/SignatureGeneratorTest.php
│   │   ├── ApaczkaClientTest.php
│   │   └── Request/OrderSendRequestTest.php
│   └── Integration/              # Testy z prawdziwym API (opcjonalne)
│       └── ApaczkaClientIntegrationTest.php
└── examples/
    ├── order_send.php
    ├── get_service_structure.php
    └── get_waybill.php
```

## 4. Kluczowe decyzje architektoniczne

### a) Autentykacja — odwzorowanie logiki z serwera

```php
// Dokładne odwzorowanie z server.php:
$stringToSign = "{$appId}:{$route}:{$jsonRequest}:{$expires}";
$signature = hash_hmac('sha256', $stringToSign, $appSecret);
```

- Trailing slash w `$route` jest wymagany
- `expires` = `time() + 900` (15 minut)

### b) HTTP Client

- Interfejs `HttpClientInterface` — pozwala na podmianę klienta HTTP
- Domyślna implementacja: Guzzle 7 (jako `suggest`, nie `require`)
- Alternatywa: dowolny klient PSR-18 (`psr/http-client`)
- Request body: `application/x-www-form-urlencoded` (nie JSON!) — tak działa serwer

### c) Wersjonowanie

- Semantic versioning (1.0.0)
- Branch `main` = stabilna wersja

### d) Kompatybilność PHP

- PHP 7.4 jako minimum
- Typed properties, arrow functions — dostępne od 7.4
- Brak union types (PHP 8.0+) — zachować kompatybilność

## 5. composer.json

```json
{
    "name": "alsendo/alsendo-one-sdk",
    "description": "AlsendoOne SDK — official PHP client for Apaczka API v2",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": ">=7.4",
        "ext-json": "*",
        "psr/http-client": "^1.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5",
        "guzzlehttp/guzzle": "^7.0",
        "phpstan/phpstan": "^1.0",
        "friendsofphp/php-cs-fixer": "^3.0"
    },
    "suggest": {
        "guzzlehttp/guzzle": "Default HTTP client implementation (^7.0)"
    },
    "autoload": {
        "psr-4": {
            "AlsendoOne\\SDK\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "AlsendoOne\\SDK\\Tests\\": "tests/"
        }
    }
}
```

## 6. API klienta — przykład użycia

```php
use AlsendoOne\SDK\ApaczkaClient;

$client = new ApaczkaClient('your_app_id', 'your_app_secret');

// Struktura serwisów
$services = $client->getServiceStructure();

// Wycena
$valuation = $client->getValuation([
    'service_id' => 52,
    'address' => [
        'sender' => ['postal_code' => '00-001', 'country_code' => 'PL'],
        'receiver' => ['postal_code' => '30-001', 'country_code' => 'PL'],
    ],
    'shipment' => [['weight' => 5000, 'length' => 30, 'width' => 20, 'height' => 15]],
]);

// Nadanie zlecenia
$order = $client->sendOrder([...]);

// Pobranie listu przewozowego (zwraca PDF w base64)
$waybill = $client->getWaybill($orderId);
```

## 7. Fazy realizacji

### Faza 1 — MVP (core)

1. `SignatureGenerator` — logika HMAC z testami
2. `HttpClientInterface` + `GuzzleHttpClient`
3. `ApaczkaClient` — fasada z metodami dla wszystkich 12 endpointów
4. `Response` wrapper z obsługą błędów (`status=400`)
5. Testy jednostkowe (mock HTTP)
6. README z dokumentacją i przykładami
7. CI: GitHub Actions (PHPUnit, PHPStan, CS-Fixer)
8. Publikacja na Packagist

### Faza 2 — rozszerzenia

- Request DTOs (typed obiekty zamiast tablic)
- Response DTOs (typed obiekty z hydracją)
- Endpointy uprzywilejowane (customer_register, check_data, etc.)
- Obsługa webhook tracking (weryfikacja podpisu przychodzącego)
- Retry policy / rate limiting
- PSR-3 Logger integration

### Faza 3 — DX

- Generowanie OpenAPI spec z serwera (źródło prawdy)
- Sandbox/testing mode
- Symfony Bundle / Laravel Service Provider

## 8. CI/CD

```yaml
# .github/workflows/ci.yml
- PHP versions: 7.4, 8.0, 8.1, 8.2, 8.3
- PHPUnit
- PHPStan level 6+
- PHP-CS-Fixer (PSR-12)
- Auto-tag release → Packagist webhook
```

## 9. Uwagi

- **Body format**: Serwer oczekuje `application/x-www-form-urlencoded` z kluczami `app_id`, `request`, `expires`, `signature` — nie raw JSON. To krytyczne.
- **Route trailing slash**: Każdy endpoint musi kończyć się `/` w podpisie — inaczej signature mismatch.
- **Kwoty w groszach**: Dokumentacja powinna jasno komunikować, że ceny są w groszach (1 PLN = 100 groszy).
- **Istniejąca Postman collection** (`docs/Apaczka_API_v2.postman_collection.json`) w monorepo — baza do testów integracyjnych i weryfikacji podpisu.
