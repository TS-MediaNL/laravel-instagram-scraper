<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\InstagramScraper\Exception;

/**
 * Instagram is throttling requests (HTTP 429 Too Many Requests).
 *
 * This is an IP- or account-level rate limit. Retrying quickly will not help.
 * Recommended actions:
 *   - Wait at least 10-30 minutes before scraping again from the same IP.
 *   - Use a residential proxy to rotate your outbound IP.
 *   - Reduce scrape frequency or add longer delays between requests.
 *   - If using a session, verify the account is not challenged/blocked.
 */
class InstagramRateLimitException extends InstagramException
{
    public function __construct(string $message = '', string $responseBody = '', ?\Throwable $previous = null)
    {
        parent::__construct(
            $message ?: 'Instagram rate limit (429): too many requests from this IP or session. Wait at least 10-30 minutes before retrying.',
            429,
            $responseBody,
            $previous,
        );
    }
}
