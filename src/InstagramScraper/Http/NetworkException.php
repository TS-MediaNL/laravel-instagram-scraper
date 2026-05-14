<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\InstagramScraper\Http;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

/**
 * Gegooid als er geen verbinding gemaakt kon worden (DNS-fout, timeout, etc.).
 * Implementeert PSR-18 NetworkExceptionInterface zodat callers hem correct kunnen
 * afvangen ongeacht welke HTTP-implementatie eronder zit.
 */
final class NetworkException extends RuntimeException implements NetworkExceptionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        string $message = '',
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
