<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\InstagramScraper\Http;

use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class Request
{
    private static ClientInterface $client;

    public static function setHttpClient(ClientInterface $client): void
    {
        self::$client = $client;
    }

    /**
     * @param array<string, string>      $headers
     * @param array<string, mixed>|null  $body
     * @throws ClientExceptionInterface
     */
    private static function send(string $method, string|Uri $uri, array $headers = [], ?array $body = null): Response
    {
        $stream = null;

        if ($body !== null) {
            $encoded = http_build_query($body);
            $stream  = Utils::streamFor($encoded);

            // Alleen invullen als de caller de Content-Type nog niet gezet heeft.
            $alreadySet = array_filter(
                array_keys($headers),
                static fn (string $k): bool => strtolower($k) === 'content-type',
            );
            if (empty($alreadySet)) {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }
        }

        $request = new Psr7Request($method, $uri, $headers, $stream);

        return new Response(self::$client->sendRequest($request));
    }

    /**
     * @param array<string, string>      $headers
     * @param array<string, mixed>|null  $parameters
     * @throws ClientExceptionInterface
     */
    public static function get(string $url, array $headers = [], ?array $parameters = null): Response
    {
        $uri = new Uri($url);

        if ($parameters !== null) {
            $uri = $uri->withQuery(http_build_query($parameters));
        }

        return self::send('GET', $uri, $headers);
    }

    /**
     * @param array<string, string>      $headers
     * @param array<string, mixed>|null  $body
     * @throws ClientExceptionInterface
     */
    public static function post(string $url, array $headers = [], ?array $body = null): Response
    {
        return self::send('POST', $url, $headers, $body);
    }
}
