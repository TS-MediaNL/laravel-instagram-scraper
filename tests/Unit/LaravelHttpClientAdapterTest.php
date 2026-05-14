<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Tests\Unit;

use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request as LaravelRequest;
use Orchestra\Testbench\TestCase;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Http\LaravelHttpClientAdapter;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Http\NetworkException;

final class LaravelHttpClientAdapterTest extends TestCase
{
    public function test_successful_get_request_returns_psr7_response(): void
    {
        $factory = new HttpFactory();
        $factory->fake(['*' => $factory::response('{"status":"ok"}', 200)]);

        $adapter  = new LaravelHttpClientAdapter($factory->timeout(30));
        $request  = new Psr7Request('GET', 'https://www.instagram.com/api/test');
        $response = $adapter->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('{"status":"ok"}', (string) $response->getBody());
    }

    public function test_headers_from_psr7_request_are_forwarded(): void
    {
        $factory = new HttpFactory();
        $factory->fake(['*' => $factory::response('{}', 200)]);

        $adapter = new LaravelHttpClientAdapter($factory->timeout(30));
        $request = new Psr7Request('GET', 'https://example.com', [
            'X-Instagram-Gis' => 'abc123',
            'X-Csrf-Token'    => 'token456',
        ]);

        $adapter->sendRequest($request);

        $factory->assertSent(function (LaravelRequest $sent): bool {
            $headers = $sent->headers();

            return ($headers['X-Instagram-Gis'][0] ?? null) === 'abc123'
                && ($headers['X-Csrf-Token'][0] ?? null) === 'token456';
        });
    }

    public function test_retries_on_configured_status_codes(): void
    {
        $factory = new HttpFactory();
        $factory->fakeSequence()
            ->push('{"error":"rate limited"}', 429)
            ->push('{"status":"ok"}', 200);

        $adapter  = new LaravelHttpClientAdapter(
            pending: $factory->timeout(30),
            maxAttempts: 2,
            retryDelayMs: 0,
            retryOnCodes: [429],
        );
        $request  = new Psr7Request('GET', 'https://example.com');
        $response = $adapter->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
        $factory->assertSentCount(2);
    }

    public function test_no_retry_when_max_attempts_is_one(): void
    {
        $factory = new HttpFactory();
        $factory->fake(['*' => $factory::response('{}', 429)]);

        $adapter  = new LaravelHttpClientAdapter(
            pending: $factory->timeout(30),
            maxAttempts: 1,
            retryDelayMs: 0,
            retryOnCodes: [429],
        );
        $request  = new Psr7Request('GET', 'https://example.com');
        $response = $adapter->sendRequest($request);

        $this->assertSame(429, $response->getStatusCode());
        $factory->assertSentCount(1);
    }

    public function test_connection_exception_is_wrapped_in_network_exception(): void
    {
        $factory = new HttpFactory();
        $factory->fake(static function () {
            throw new ConnectionException('Could not connect');
        });

        $adapter = new LaravelHttpClientAdapter($factory->timeout(1));
        $request = new Psr7Request('GET', 'https://unreachable.example.com');

        $this->expectException(NetworkException::class);
        $this->expectExceptionMessage('Could not connect');

        $adapter->sendRequest($request);
    }

    public function test_network_exception_exposes_original_request(): void
    {
        $factory = new HttpFactory();
        $factory->fake(static function () {
            throw new ConnectionException('Timeout');
        });

        $adapter  = new LaravelHttpClientAdapter($factory->timeout(1));
        $psr7     = new Psr7Request('GET', 'https://unreachable.example.com');
        $caught   = null;

        try {
            $adapter->sendRequest($psr7);
        } catch (NetworkException $e) {
            $caught = $e;
        }

        $this->assertNotNull($caught);
        $this->assertSame('https://unreachable.example.com', (string) $caught->getRequest()->getUri());
    }

    public function test_post_body_is_forwarded(): void
    {
        $factory = new HttpFactory();
        $factory->fake(['*' => $factory::response('{}', 200)]);

        $body    = 'username=foo&password=bar';
        $adapter = new LaravelHttpClientAdapter($factory->timeout(30));
        $request = new Psr7Request(
            'POST',
            'https://example.com/login',
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            $body,
        );

        $adapter->sendRequest($request);

        $factory->assertSent(function (LaravelRequest $sent) use ($body): bool {
            return $sent->body() === $body;
        });
    }
}
