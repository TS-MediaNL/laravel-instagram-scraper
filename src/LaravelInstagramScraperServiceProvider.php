<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Http\LaravelHttpClientAdapter;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Instagram;

final class LaravelInstagramScraperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/instagram-scraper.php',
            'instagram-scraper',
        );

        $this->app->singleton(Instagram::class, function ($app): Instagram {
            /** @var array<string, mixed> $httpConfig */
            $httpConfig  = $app['config']->get('instagram-scraper.http', []);
            /** @var array<string, mixed> $retryConfig */
            $retryConfig = $app['config']->get('instagram-scraper.retry', []);
            $proxy       = $app['config']->get('instagram-scraper.proxy');
            $userAgent   = $app['config']->get('instagram-scraper.user_agent');

            $pending = $app->make(HttpFactory::class)
                ->timeout((float) ($httpConfig['timeout'] ?? 60))
                ->connectTimeout((float) ($httpConfig['connect_timeout'] ?? 15))
                ->withoutRedirecting();

            if ($proxy) {
                $pending = $pending->withOptions(['proxy' => $proxy]);
            }

            $adapter = new LaravelHttpClientAdapter(
                pending: $pending,
                maxAttempts: max(1, (int) ($retryConfig['max_attempts'] ?? 3)),
                retryDelayMs: max(0, (int) ($retryConfig['delay_ms'] ?? 1000)),
                retryOnCodes: (array) ($retryConfig['on_codes'] ?? [429, 500, 502, 503, 504]),
            );

            $instagram = new Instagram($adapter);

            if ($userAgent) {
                $instagram->setUserAgent($userAgent);
            }

            return $instagram;
        });

        $this->app->singleton(InstagramProfileClient::class, function ($app): InstagramProfileClient {
            return new InstagramProfileClient($app->make(Instagram::class));
        });

        $this->app->alias(InstagramProfileClient::class, 'instagram-profile');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__) . '/config/instagram-scraper.php' => config_path('instagram-scraper.php'),
            ], 'instagram-scraper-config');
        }
    }
}
