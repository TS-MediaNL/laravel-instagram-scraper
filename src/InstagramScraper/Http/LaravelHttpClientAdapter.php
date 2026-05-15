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
     * @param list<int> $retryOnCodes      HTTP-statuscodes die een retry triggeren.
     * @param int       $rateLimitDelayMs  Basisvertraging (ms) bij HTTP 429 — Instagram
     *                                     vereist typisch 30-60 s rust; veel groter dan
     *                                     de gewone retry-vertraging voor 5xx fouten.
     */
    public function __construct(
        private readonly PendingRequest $pending,
        private readonly int $maxAttempts = 1,
        private readonly int $retryDelayMs = 1000,
        private readonly array $retryOnCodes = [],
        private readonly int $rateLimitDelayMs = 60_000,
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

            $isRateLimit = $response->getStatusCode() === 429;

            $shouldRetry = $attempt < $this->maxAttempts - 1
                && in_array($response->getStatusCode(), $this->retryOnCodes, strict: true);

            if (! $shouldRetry) {
                return $response;
            }

            $attempt++;
            $delayUs = $isRateLimit
                ? $this->rateLimitDelayMicroseconds($attempt)
                : $this->exponentialDelayMicroseconds($attempt);
            usleep($delayUs);
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
     * Exponentiële backoff voor normale fouten (5xx): 1×, 2×, 4× van retryDelayMs.
     */
    private function exponentialDelayMicroseconds(int $attempt): int
    {
        return $this->retryDelayMs * (2 ** ($attempt - 1)) * 1_000;
    }

    /**
     * Lineaire backoff voor 429: elke poging wacht rateLimitDelayMs.
     * Exponentieel is hier minder zinvol — als Instagram rate-limit, is de IP
     * al geflagd en helpt langer wachten bij de eerste poging het meest.
     */
    private function rateLimitDelayMicroseconds(int $attempt): int
    {
        return $this->rateLimitDelayMs * $attempt * 1_000;
    }
}
