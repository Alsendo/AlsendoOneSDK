<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Exception;

use AlsendoOne\SDK\Http\Response;

class ApiException extends AlsendoException
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
        parent::__construct(self::buildMessage($response), $response->getApiStatus());
    }

    /**
     * The API is expected to return a {status, message, response} envelope.
     * When it does not (e.g. an HTML error page or a proxy response), fall
     * back to a descriptive message with a body snippet instead of "".
     */
    private static function buildMessage(Response $response): string
    {
        $message = $response->getMessage();
        if ($message !== '') {
            return $message;
        }

        if ($response->toArray() === [] && trim($response->getBody()) !== '') {
            return sprintf(
                'Unexpected API response without a valid envelope (HTTP %d): %s',
                $response->getHttpStatusCode(),
                substr(trim($response->getBody()), 0, 200)
            );
        }

        return $message;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        return $this->response->getResponseData();
    }
}
