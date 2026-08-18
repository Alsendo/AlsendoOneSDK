<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Http;

use AlsendoOne\SDK\Exception\ConnectionException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Decorator that logs every request through a PSR-3 logger.
 *
 * Only the URL, HTTP status, API envelope status and duration are logged —
 * request parameters are deliberately not logged, as they contain the
 * signed payload and application credentials.
 */
class LoggingHttpClient implements HttpClientInterface
{
    private HttpClientInterface $inner;
    private LoggerInterface $logger;
    private string $level;

    public function __construct(
        HttpClientInterface $inner,
        LoggerInterface $logger,
        string $level = LogLevel::INFO
    ) {
        $this->inner = $inner;
        $this->logger = $logger;
        $this->level = $level;
    }

    public function post(string $url, array $formParams): Response
    {
        return $this->send('POST', $url, fn (): Response => $this->inner->post($url, $formParams));
    }

    public function get(string $url, array $queryParams): Response
    {
        return $this->send('GET', $url, fn (): Response => $this->inner->get($url, $queryParams));
    }

    /**
     * @param callable(): Response $call
     */
    private function send(string $method, string $url, callable $call): Response
    {
        $start = microtime(true);

        try {
            $response = $call();
        } catch (ConnectionException $e) {
            $this->logger->error('Apaczka API request failed: {method} {url} — {error}', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
                'duration_ms' => (int) round((microtime(true) - $start) * 1000),
            ]);

            throw $e;
        }

        $this->logger->log($this->level, 'Apaczka API request: {method} {url} → api status {api_status}', [
            'method' => $method,
            'url' => $url,
            'http_status' => $response->getHttpStatusCode(),
            'api_status' => $response->getApiStatus(),
            'duration_ms' => (int) round((microtime(true) - $start) * 1000),
        ]);

        return $response;
    }
}
