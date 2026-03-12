<?php

/**
 * AlsendoOne SDK — Example: Get service structure
 *
 * Fetches and displays all available shipping services, their options,
 * and supported package types.
 */

require __DIR__ . '/../vendor/autoload.php';

use AlsendoOne\SDK\ApaczkaClient;
use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Exception\ConnectionException;

// Replace with your credentials
$appId = 'your_app_id';
$appSecret = 'your_app_secret';

$client = new ApaczkaClient($appId, $appSecret);

try {
    $structure = $client->getServiceStructure();

    // List available services
    $services = $structure->getServices();
    if (!empty($services)) {
        echo "Available services:" . PHP_EOL;
        echo str_repeat('-', 60) . PHP_EOL;

        foreach ($services as $service) {
            printf(
                "  [%d] %s (supplier: %s)\n",
                $service['service_id'] ?? $service['id'] ?? 0,
                $service['name'] ?? 'N/A',
                $service['supplier'] ?? 'N/A'
            );
        }
    }

    // List package types
    $packageTypes = $structure->getPackageTypes();
    if (!empty($packageTypes)) {
        echo PHP_EOL . "Package types:" . PHP_EOL;
        echo str_repeat('-', 60) . PHP_EOL;

        foreach ($packageTypes as $code => $packageType) {
            printf("  [%s] %s\n", $packageType->getType(), $packageType->getDesc());
        }
    }

    // List service options
    $options = $structure->getOptions();
    if (!empty($options)) {
        echo PHP_EOL . "Service options:" . PHP_EOL;
        echo str_repeat('-', 60) . PHP_EOL;

        foreach ($options as $id => $option) {
            printf("  [%s] %s — %s\n", $id, $option->getName(), $option->getDesc());
        }
    }

    // List pickup types
    $pickupTypes = $structure->getPickupTypes();
    if (!empty($pickupTypes)) {
        echo PHP_EOL . "Pickup types:" . PHP_EOL;
        echo str_repeat('-', 60) . PHP_EOL;

        foreach ($pickupTypes as $code => $pickupType) {
            printf("  [%s] %s\n", $pickupType->getType(), $pickupType->getDesc());
        }
    }
} catch (ConnectionException $e) {
    echo "Connection error: " . $e->getMessage() . PHP_EOL;
    exit(1);
} catch (ApiException $e) {
    echo "API error " . $e->getCode() . ": " . $e->getMessage() . PHP_EOL;
    exit(1);
}
