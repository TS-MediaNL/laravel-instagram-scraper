<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Console;

use Illuminate\Console\Command;
use Throwable;
use TsMedia\LaravelInstagramScraper\InstagramProfileClient;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramException;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Media;

final class TestInstagramScraperCommand extends Command
{
    protected $signature = 'instagram-scraper:test
                            {--username=instagram : Publieke Instagram-gebruikersnaam (zonder @)}
                            {--timeline : Ook max. 5 timeline-posts tonen}';

    protected $description = 'Smoke-test: controleer of de package een publiek Instagram-account kan uitlezen.';

    public function handle(): int
    {
        $username = ltrim(strtolower(trim((string) $this->option('username'))), '@');
        if ($username === '') {
            $this->error('Geef een geldige --username op (zonder @).');

            return self::FAILURE;
        }

        $this->info('Instagram scraper — test voor @'.$username);

        try {
            /** @var InstagramProfileClient $client */
            $client = $this->laravel->make(InstagramProfileClient::class);
        } catch (Throwable $e) {
            $this->error('InstagramProfileClient niet te resolven: '.$e->getMessage());

            return self::FAILURE;
        }

        try {
            $account = $client->accountByUsername($username);
        } catch (InstagramException $e) {
            $this->error('Account-lookup mislukt: '.$e->getMessage());
            $this->line('Controleer netwerk, proxy (INSTAGRAM_SCRAPER_PROXY), of of Instagram tijdelijk blokkeert.');

            return self::FAILURE;
        }

        $this->table(
            ['Veld', 'Waarde'],
            [
                ['user id', (string) $account->getId()],
                ['username', (string) $account->getUsername()],
                ['followers', (string) $account->getFollowedByCount()],
                ['private', $account->isPrivate() ? 'ja' : 'nee'],
            ],
        );

        if ($account->isPrivate()) {
            $this->warn('Privé-account: geen publieke timeline.');

            return self::SUCCESS;
        }

        if (! $this->option('timeline')) {
            $this->newLine();
            $this->comment('Voeg --timeline toe om ook posts op te halen.');

            return self::SUCCESS;
        }

        $userId = (int) $account->getId();

        try {
            $medias = $client->timelineByUserId($userId, 5, '');
        } catch (InstagramException $e) {
            $this->error('Timeline mislukt: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Timeline: '.count($medias).' item(s) (max. 5).');

        $rows = [];
        foreach ($medias as $i => $m) {
            if (! $m instanceof Media) {
                continue;
            }
            $rows[] = [
                (string) ($i + 1),
                $m->getType(),
                (string) $m->getShortCode(),
                (string) $m->getId(),
            ];
        }

        if ($rows === []) {
            $this->warn('Geen media-objecten in de response (onverwacht).');

            return self::SUCCESS;
        }

        $this->table(['#', 'type', 'code', 'id'], $rows);

        return self::SUCCESS;
    }
}
