<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Exception;

/**
 * Reserved for future use — currently never thrown by the SDK.
 *
 * The Apaczka API v2 signals authentication failures with the same status
 * (400) as any other error, so they cannot be distinguished reliably.
 */
class AuthenticationException extends ApaczkaException
{
}
