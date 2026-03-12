<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Exception;

use AlsendoOne\SDK\Http\Response;

class ApiException extends ApaczkaException
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
        parent::__construct($response->getMessage(), $response->getApiStatus());
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
