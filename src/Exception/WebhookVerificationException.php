<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Exception;

/**
 * Thrown when an incoming push-tracking webhook cannot be verified —
 * malformed payload or signature mismatch.
 */
class WebhookVerificationException extends AlsendoException
{
}
