<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Http;

use AlsendoOne\SDK\Exception\ConnectionException;

/**
 * Decorator that retries requests failing with a ConnectionException,
 * with linear backoff between attempts.
 *
 * WARNING: a network failure does not guarantee the request did not reach
 * the server. Retrying non-idempotent operations (order_send in particular)
 * may create duplicates. Use this decorator for read operations, or make
 * sure your integration can deal with duplicate submissions.
 */
class RetryingHttpClient implements HttpClientInterface
{
    private HttpClientInterface $inner;
    private int $maxRetries;
    private int $delayMs;

    /**
     * @param int $maxRetries Number of retries after the initial attempt
     * @param int $delayMs Base delay between attempts; grows linearly (delay × attempt)
     */
    public function __construct(HttpClientInterface $inner, int $maxRetries = 2, int $delayMs = 500)
    {
        $this->inner = $inner;
        $this->maxRetries = max(0, $maxRetries);
        $this->delayMs = max(0, $delayMs);
    }

    public function post(string $url, array $formParams): Response
    {
        return $this->attempt(fn (): Response => $this->inner->post($url, $formParams));
    }

    public function get(string $url, array $queryParams): Response
    {
        return $this->attempt(fn (): Response => $this->inner->get($url, $queryParams));
    }

    /**
     * @param callable(): Response $call
     */
    private function attempt(callable $call): Response
    {
        $attempt = 0;
        while (true) {
            try {
                return $call();
            } catch (ConnectionException $e) {
                ++$attempt;
                if ($attempt > $this->maxRetries) {
                    throw $e;
                }
                if ($this->delayMs > 0) {
                    usleep($this->delayMs * 1000 * $attempt);
                }
            }
        }
    }
}
