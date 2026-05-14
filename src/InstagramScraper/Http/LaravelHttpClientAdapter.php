<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\InstagramScraper\Http;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * PSR-18 adapter rond Laravel's HTTP client.
 *
 * Hierdoor profiteert de scraper automatisch van alle Laravel HTTP-features:
 * logging, fake/mock in tests, macros, event-listeners, etc.
 *
 * Retry-logica voor HTTP-foutcodes (bv. 429, 503) zit in deze klasse zodat
 * de Instagram-engine ongewijzigd kan controleren op response-codes en
 * exceptions.
 */
final class LaravelHttpClientAdapter implements ClientInterface
{
    /**
     * @param list<int> $retryOnCodes  HTTP-statuscodes die een retry triggeren.
     */
    public function __construct(
        private readonly PendingRequest $pending,
        private readonly int $maxAttempts = 1,
        private readonly int $retryDelayMs = 1000,
        private readonly array $retryOnCodes = [],
    ) {}

    /**
     * @throws NetworkException  Als er geen verbinding gemaakt kan worden.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $attempt = 0;

        while (true) {
            try {
                $response = $this->dispatch($request);
            } catch (ConnectionException $e) {
                throw new NetworkException($request, $e->getMessage(), $e);
            }

            $shouldRetry = $attempt < $this->maxAttempts - 1
                && in_array($response->getStatusCode(), $this->retryOnCodes, strict: true);

            if (! $shouldRetry) {
                return $response;
            }

            $attempt++;
            usleep($this->exponentialDelayMicroseconds($attempt));
        }
    }

    private function dispatch(RequestInterface $request): ResponseInterface
    {
        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }

        $body    = (string) $request->getBody();
        $options = ['headers' => $headers];

        if ($body !== '') {
            $options['body'] = $body;
        }

        return $this->pending
            ->send($request->getMethod(), (string) $request->getUri(), $options)
            ->toPsrResponse();
    }

    /**
     * Exponentiële backoff: 1× delay, 2× delay, 4× delay, …
     */
    private function exponentialDelayMicroseconds(int $attempt): int
    {
        return $this->retryDelayMs * (2 ** ($attempt - 1)) * 1_000;
    }
}
