<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TsMedia\LaravelInstagramScraper\InstagramProfileClient;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramException;
use TsMedia\LaravelInstagramScraper\LaravelInstagramScraperServiceProvider;

/**
 * Live HTTP tegen instagram.com — alleen draaien met RUN_INSTAGRAM_NETWORK_TEST=1
 * (zie {@see composer.json} script <code>test:network</code> of <code>scripts/smoke-test.php</code>).
 *
 * @group network
 */
final class PackageNetworkSmokeTest extends TestCase
{
    /**
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelInstagramScraperServiceProvider::class,
        ];
    }

    private function requireLiveNetworkFlag(): void
    {
        $flag = strtolower((string) getenv('RUN_INSTAGRAM_NETWORK_TEST'));
        if (! in_array($flag, ['1', 'true', 'yes'], true)) {
            $this->markTestSkipped(
                'Live-test overgeslagen. Zet RUN_INSTAGRAM_NETWORK_TEST=1 of gebruik: composer test:network'
            );
        }
    }

    public function test_service_container_resolves_instagram_profile_client(): void
    {
        $client = $this->app->make(InstagramProfileClient::class);

        $this->assertInstanceOf(InstagramProfileClient::class, $client);
    }

    public function test_public_account_lookup_returns_id_and_username(): void
    {
        $this->requireLiveNetworkFlag();

        $username = (string) (getenv('INSTAGRAM_SMOKE_USERNAME') ?: 'instagram');
        $username = ltrim(strtolower(trim($username)), '@');

        $client = $this->app->make(InstagramProfileClient::class);

        try {
            $account = $client->accountByUsername($username);
        } catch (InstagramException $e) {
            $this->markTestSkipped(
                'Live Instagram-call mislukt (serviceprovider + HTTP-stack zijn wel geladen): '.$e->getMessage()
            );

            return;
        }

        $this->assertNotSame('', (string) $account->getId());
        $this->assertGreaterThan(0, (int) $account->getId());
        $this->assertSame($username, strtolower((string) $account->getUsername()));
    }

    public function test_timeline_returns_array_for_public_account(): void
    {
        $this->requireLiveNetworkFlag();

        $username = (string) (getenv('INSTAGRAM_SMOKE_USERNAME') ?: 'instagram');
        $username = ltrim(strtolower(trim($username)), '@');

        $client = $this->app->make(InstagramProfileClient::class);

        try {
            $account = $client->accountByUsername($username);
        } catch (InstagramException $e) {
            $this->markTestSkipped(
                'Live Instagram-call mislukt (serviceprovider + HTTP-stack zijn wel geladen): '.$e->getMessage()
            );

            return;
        }

        if ($account->isPrivate()) {
            $this->markTestSkipped("Account @{$username} is privé; timeline-test overgeslagen.");

            return;
        }

        $userId = (int) $account->getId();

        try {
            $medias = $client->timelineByUserId($userId, 3, '');
        } catch (InstagramException $e) {
            $this->markTestSkipped('Timeline-call mislukt: '.$e->getMessage());

            return;
        }

        $this->assertIsArray($medias);
        $this->assertGreaterThan(0, count($medias), 'Verwacht minstens één timeline-item voor een publiek account.');
    }
}
