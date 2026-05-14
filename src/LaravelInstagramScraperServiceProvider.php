<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Instagram;

final class LaravelInstagramScraperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/instagram-scraper.php',
            'instagram-scraper'
        );

        $this->app->singleton(Instagram::class, function ($app) {
            /** @var array<string, mixed> $httpConfig */
            $httpConfig = $app['config']->get('instagram-scraper.http', []);
            $client = new Client($httpConfig);

            return new Instagram($client);
        });

        $this->app->singleton(InstagramProfileClient::class, function ($app) {
            return new InstagramProfileClient($app->make(Instagram::class));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__).'/config/instagram-scraper.php' => config_path('instagram-scraper.php'),
            ], 'instagram-scraper-config');
        }
    }
}
