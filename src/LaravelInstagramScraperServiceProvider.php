<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;
use Psr\SimpleCache\CacheInterface;
use TsMedia\LaravelInstagramScraper\Console\TestInstagramScraperCommand;
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
            $httpConfig   = $app['config']->get('instagram-scraper.http', []);
            /** @var array<string, mixed> $retryConfig */
            $retryConfig  = $app['config']->get('instagram-scraper.retry', []);
            /** @var array<string, mixed> $authConfig */
            $authConfig   = $app['config']->get('instagram-scraper.auth', []);
            /** @var array<string, mixed> $delayConfig */
            $delayConfig  = $app['config']->get('instagram-scraper.request_delay', []);
            $proxy       = $app['config']->get('instagram-scraper.proxy');
            $userAgent   = $app['config']->get('instagram-scraper.user_agent');

            $pending = $app->make(HttpFactory::class)
                ->timeout((float) ($httpConfig['timeout'] ?? 60))
                ->connectTimeout((float) ($httpConfig['connect_timeout'] ?? 15));

            if ($proxy) {
                $pending = $pending->withOptions(['proxy' => $proxy]);
            }

            $adapter = new LaravelHttpClientAdapter(
                pending: $pending,
                maxAttempts: max(1, (int) ($retryConfig['max_attempts'] ?? 3)),
                retryDelayMs: max(0, (int) ($retryConfig['delay_ms'] ?? 1000)),
                retryOnCodes: (array) ($retryConfig['on_codes'] ?? [429, 500, 502, 503, 504]),
            );

            $cache    = $app->make(CacheInterface::class);
            $sessionId = $authConfig['session_id'] ?? null;
            $username  = $authConfig['username'] ?? null;
            $password  = $authConfig['password'] ?? null;

            if ($sessionId) {
                // Session ID — meest stabiel voor productie.
                $instagram = Instagram::withUsername($adapter, 'session', $cache);
                $instagram->loginWithSessionId($sessionId);
            } elseif ($username && $password) {
                // Username + wachtwoord — sessie wordt gecachet.
                $instagram = Instagram::withCredentials($adapter, $username, $password, $cache);
                $instagram->login();
            } else {
                // Anoniem — werkt alleen voor publieke profielen, max ±12 posts.
                $instagram = Instagram::withCredentials($adapter, '', '', $cache);
            }

            if ($userAgent) {
                $instagram->setUserAgent($userAgent);
            }

            $instagram->setRequestDelay(
                (int) ($delayConfig['min_ms'] ?? 500),
                (int) ($delayConfig['max_ms'] ?? 2000),
            );

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
            $this->commands([
                TestInstagramScraperCommand::class,
            ]);

            $this->publishes([
                dirname(__DIR__) . '/config/instagram-scraper.php' => config_path('instagram-scraper.php'),
            ], 'instagram-scraper-config');
        }
    }
}
