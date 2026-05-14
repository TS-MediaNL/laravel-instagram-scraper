<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Tests\Unit;

use GuzzleHttp\Psr7\Response as Psr7Response;
use PHPUnit\Framework\TestCase;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Http\Response;

final class ResponseTest extends TestCase
{
    public function test_parses_json_body(): void
    {
        $psr = new Psr7Response(200, [], '{"status":"ok","count":3}');
        $response = new Response($psr);

        $this->assertSame(200, $response->code);
        $this->assertSame('ok', $response->body->status);
        $this->assertSame(3, $response->body->count);
        $this->assertTrue($response->isJson());
        $this->assertTrue($response->isOk());
    }

    public function test_falls_back_to_raw_string_for_non_json(): void
    {
        $psr = new Psr7Response(200, [], 'plain text response');
        $response = new Response($psr);

        $this->assertSame('plain text response', $response->body);
        $this->assertSame('plain text response', $response->raw_body);
        $this->assertFalse($response->isJson());
    }

    public function test_is_ok_is_false_for_error_codes(): void
    {
        foreach ([400, 404, 429, 500, 503] as $code) {
            $psr = new Psr7Response($code, [], '');
            $response = new Response($psr);

            $this->assertFalse($response->isOk(), "Expected isOk() false for HTTP {$code}");
        }
    }

    public function test_headers_are_accessible(): void
    {
        $psr = new Psr7Response(200, ['X-Test' => 'hello'], '{}');
        $response = new Response($psr);

        $this->assertArrayHasKey('X-Test', $response->headers);
        $this->assertSame(['hello'], $response->headers['X-Test']);
    }

    public function test_bigint_preserved_in_json(): void
    {
        $psr = new Psr7Response(200, [], '{"id":12345678901234567890}');
        $response = new Response($psr);

        $this->assertIsString($response->body->id);
    }
}
