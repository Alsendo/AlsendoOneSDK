<?php

/**
 * AlsendoOne SDK — Example: Download waybill (shipping label)
 *
 * Fetches a waybill PDF for an existing order and saves it to a file.
 */

require __DIR__ . '/../vendor/autoload.php';

use AlsendoOne\SDK\AlsendoClient;
use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Exception\ConnectionException;

// Replace with your credentials
$appId = 'your_app_id';
$appSecret = 'your_app_secret';

// Replace with an actual order ID
$orderId = 123456;

// Output directory for the PDF file
$outputDir = __DIR__ . '/output';

$client = new AlsendoClient($appId, $appSecret);

try {
    echo "Fetching waybill for order #" . $orderId . "..." . PHP_EOL;

    $waybill = $client->getWaybill($orderId);

    $content = $waybill->getWaybill();
    $type = $waybill->getType();

    if (empty($content)) {
        echo "Error: No waybill content returned by the API." . PHP_EOL;
        exit(1);
    }

    echo "  File type: " . $type . PHP_EOL;

    // Decode base64 content
    $fileContent = base64_decode($content, true);

    if ($fileContent === false) {
        echo "Error: Failed to decode base64 content." . PHP_EOL;
        exit(1);
    }

    // Create output directory if it does not exist
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    // Determine file extension from type
    $extension = $type ?: 'pdf';
    $filePath = $outputDir . '/waybill_' . $orderId . '.' . $extension;

    $bytesWritten = file_put_contents($filePath, $fileContent);

    if ($bytesWritten === false) {
        echo "Error: Failed to write file to " . $filePath . PHP_EOL;
        exit(1);
    }

    printf(
        "Waybill saved to %s (%s bytes)" . PHP_EOL,
        $filePath,
        number_format($bytesWritten)
    );
} catch (ConnectionException $e) {
    echo "Connection error: " . $e->getMessage() . PHP_EOL;
    exit(1);
} catch (ApiException $e) {
    echo "API error " . $e->getCode() . ": " . $e->getMessage() . PHP_EOL;
    exit(1);
}
