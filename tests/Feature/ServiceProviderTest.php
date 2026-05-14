<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TsMedia\LaravelInstagramScraper\InstagramProfileClient;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Instagram;
use TsMedia\LaravelInstagramScraper\LaravelInstagramScraperServiceProvider;

final class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [LaravelInstagramScraperServiceProvider::class];
    }

    public function test_instagram_singleton_is_resolved(): void
    {
        $instagram = $this->app->make(Instagram::class);

        $this->assertInstanceOf(Instagram::class, $instagram);
    }

    public function test_instagram_is_a_singleton(): void
    {
        $a = $this->app->make(Instagram::class);
        $b = $this->app->make(Instagram::class);

        $this->assertSame($a, $b);
    }

    public function test_profile_client_singleton_is_resolved(): void
    {
        $client = $this->app->make(InstagramProfileClient::class);

        $this->assertInstanceOf(InstagramProfileClient::class, $client);
    }

    public function test_profile_client_is_a_singleton(): void
    {
        $a = $this->app->make(InstagramProfileClient::class);
        $b = $this->app->make(InstagramProfileClient::class);

        $this->assertSame($a, $b);
    }

    public function test_profile_client_exposes_engine(): void
    {
        $client   = $this->app->make(InstagramProfileClient::class);
        $instagram = $this->app->make(Instagram::class);

        $this->assertSame($instagram, $client->engine());
    }

    public function test_config_defaults_are_available(): void
    {
        $this->assertSame(60.0, config('instagram-scraper.http.timeout'));
        $this->assertSame(15.0, config('instagram-scraper.http.connect_timeout'));
        $this->assertSame(3,    config('instagram-scraper.retry.max_attempts'));
        $this->assertSame(1000, config('instagram-scraper.retry.delay_ms'));
    }

    public function test_alias_resolves_profile_client(): void
    {
        $client = $this->app->make('instagram-profile');

        $this->assertInstanceOf(InstagramProfileClient::class, $client);
    }
}
