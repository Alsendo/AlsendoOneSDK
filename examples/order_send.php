<?php

/**
 * AlsendoOne SDK — Example: Create a shipment order
 *
 * Demonstrates the complete flow using typed DTOs:
 * get a price valuation, send the order, and schedule a pickup.
 */

require __DIR__ . '/../vendor/autoload.php';

use AlsendoOne\SDK\AlsendoClient;
use AlsendoOne\SDK\DTO\Address;
use AlsendoOne\SDK\DTO\Request\NotificationRequest;
use AlsendoOne\SDK\DTO\Request\OrderRequest;
use AlsendoOne\SDK\DTO\Request\PickupRequest;
use AlsendoOne\SDK\DTO\Request\ShipmentRequest;
use AlsendoOne\SDK\Type\Service;
use AlsendoOne\SDK\Exception\ApiException;
use AlsendoOne\SDK\Exception\ConnectionException;

// Replace with your credentials
$appId = 'your_app_id';
$appSecret = 'your_app_secret';

$client = new AlsendoClient($appId, $appSecret);

// Build order using typed DTOs
// Replace with a service available on your account, see getServiceStructure()
$service = Service::from(Service::DpdCourier);

$order = OrderRequest::create()
    ->setService($service)
    ->setSenderAddress(new Address(
        'Firma Sp. z o.o.',       // name
        'Jan Kowalski',           // contact person
        'jan@example.com',        // email
        '500100200',              // phone
        'Marszalkowska 1/10',     // line1
        null,                     // line2
        '00-001',                 // postal code
        'Warszawa',               // city
        'PL'                      // country code
    ))
    ->setReceiverAddress(new Address(
        'Anna Nowak',
        null,
        'anna@example.com',
        '600300400',
        'Dluga 15',
        null,
        '80-831',
        'Gdansk',
        'PL'
    ))
    ->addShipment(new ShipmentRequest(
        'PACZKA',   // shipment type code
        2.5,        // weight in kilograms
        40,         // length in cm
        30,         // width in cm
        20          // height in cm
    ))
    ->setPickup(new PickupRequest(
        'COURIER',
        date('Y-m-d', strtotime('+1 day')),
        '10:00',
        '16:00'
    ))
    ->setNotification(
        (new NotificationRequest())
            ->setNew(true, false, true)   // receiver e-mail + sender e-mail
            ->setSent(true)               // receiver e-mail
            ->setDelivered(true)          // receiver e-mail
    )
    ->setComment('Fragile items, please handle with care.');

try {
    // Step 1: Get price valuation
    echo "Getting price valuation..." . PHP_EOL;
    $valuation = $client->getValuation($order);

    foreach ($valuation->getPriceTable() as $price) {
        // Prices are in groszy (1 PLN = 100 groszy)
        printf(
            "  Service %d: %.2f PLN net / %.2f PLN gross\n",
            $price->getServiceId(),
            $price->getPrice() / 100,
            $price->getPriceGross() / 100
        );
    }

    // Step 2: Send the order
    echo PHP_EOL . "Creating order..." . PHP_EOL;
    $result = $client->sendOrder($order);

    echo "  Order created:" . PHP_EOL;
    echo "    ID: " . $result->getId() . PHP_EOL;
    echo "    Service: " . $result->getServiceName() . PHP_EOL;
    echo "    Status: " . $result->getStatus() . PHP_EOL;
    echo "    Waybill: " . $result->getWaybillNumber() . PHP_EOL;
    echo "    Tracking: " . $result->getTrackingUrl() . PHP_EOL;

    // Step 3: Check pickup hours and schedule
    echo PHP_EOL . "Checking available pickup hours..." . PHP_EOL;
    $pickupHours = $client->getPickupHours('00-001', $service);

    $hours = $pickupHours->getHours();
    if (!empty($hours)) {
        $firstDay = reset($hours);
        echo "  Available date: " . $firstDay->getDate() . PHP_EOL;

        $pickup = $client->schedulePickup(
            $result->getId(),
            $firstDay->getDate(),
            '10:00',
            '16:00'
        );

        echo "  Pickup scheduled, courier ID: " . $pickup->getForeignCourierId() . PHP_EOL;
    } else {
        echo "  No pickup hours available for this postal code." . PHP_EOL;
    }

    echo PHP_EOL . "Done." . PHP_EOL;
} catch (ConnectionException $e) {
    echo "Connection error: " . $e->getMessage() . PHP_EOL;
    exit(1);
} catch (ApiException $e) {
    echo "API error " . $e->getCode() . ": " . $e->getMessage() . PHP_EOL;

    $data = $e->getResponseData();
    if (!empty($data)) {
        echo "Response data: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }

    exit(1);
}
