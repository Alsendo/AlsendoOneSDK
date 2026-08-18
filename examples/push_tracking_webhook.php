<?php

/**
 * AlsendoOne SDK — Example: Receive a push-tracking webhook
 *
 * When you create an order with `push_tracking_url`, the platform POSTs a
 * signed JSON notification to that URL on every shipment status change.
 * Deploy an endpoint like this one at that URL to receive the updates.
 */

require __DIR__ . '/../vendor/autoload.php';

use AlsendoOne\SDK\Exception\WebhookVerificationException;
use AlsendoOne\SDK\Webhook\PushTrackingWebhook;

// The same credentials you use for the API client — the notification is
// signed with your own app_secret.
$appId = 'your_app_id';
$appSecret = 'your_app_secret';

$webhook = new PushTrackingWebhook($appId, $appSecret);

// Verify the signature and parse the raw request body.
try {
    $notification = $webhook->parse((string) file_get_contents('php://input'));
} catch (WebhookVerificationException $e) {
    // Malformed payload or signature mismatch — do not process it.
    http_response_code(400);
    echo 'Rejected: ' . $e->getMessage();
    exit;
}

// Process the status changes. `statuses` may be empty; timestamps are
// Y-m-d\TH:i:s without an offset, in Europe/Warsaw time.
foreach ($notification->getStatuses() as $status) {
    error_log(sprintf(
        'Order %s (%s, waybill %s): %s at %s',
        $notification->getOrderNumber(),
        $notification->getOperator(),
        $notification->getTrackingNumber(),
        $status->getStatus(),          // e.g. ON_THE_WAY, OUT_FOR_DELIVERY, DELIVERED
        $status->getUpdatedAt()
    ));

    foreach ($status->getOperatorStatuses() as $operatorStatus) {
        error_log(sprintf(
            '  carrier: %s | %s | %s',
            $operatorStatus->getStatus(),
            $operatorStatus->getPlace(),
            $operatorStatus->getDescription()
        ));
    }
}

// Acknowledge the notification — the platform expects HTTP 200.
http_response_code(200);
echo 'OK';
