<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\InstagramScraper\Http;

use Psr\Http\Message\ResponseInterface;

class Response
{
    public readonly int $code;
    public readonly string $raw_body;
    /** @var mixed */
    public readonly mixed $body;
    /** @var array<string, array<int, string>> */
    public readonly array $headers;

    public function __construct(ResponseInterface $response)
    {
        $this->code    = $response->getStatusCode();
        $this->headers = $response->getHeaders();
        $rawBody       = $response->getBody()->getContents();
        $this->raw_body = $rawBody;

        $json = json_decode($rawBody, false, 512, JSON_BIGINT_AS_STRING);

        $this->body = (json_last_error() === JSON_ERROR_NONE) ? $json : $rawBody;
    }

    public function isOk(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    public function isJson(): bool
    {
        return $this->body !== $this->raw_body;
    }
}
