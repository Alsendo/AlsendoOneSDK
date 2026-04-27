<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Http;

interface HttpClientInterface
{
    /**
     * Send a POST request with form-urlencoded body.
     *
     * @param string $url Full URL
     * @param array<string, string> $formParams Form parameters
     * @param array<string, string> $headers Optional request headers (e.g. User-Agent)
     * @return Response
     */
    public function post(string $url, array $formParams, array $headers = []): Response;

    /**
     * Send a GET request with form-urlencoded query params.
     *
     * @param string $url Full URL
     * @param array<string, string> $queryParams Query parameters
     * @param array<string, string> $headers Optional request headers (e.g. User-Agent)
     * @return Response
     */
    public function get(string $url, array $queryParams, array $headers = []): Response;
}
