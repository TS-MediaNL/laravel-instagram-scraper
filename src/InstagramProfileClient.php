<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper;

use TsMedia\LaravelInstagramScraper\InstagramScraper\Instagram;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Account;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Media;

/**
 * Aanbevolen entrypoint in Laravel: publiek profiel + timeline zonder direct de engine te hoeven typen.
 * Voor geavanceerde calls: {@see self::engine()}.
 */
final class InstagramProfileClient
{
    public function __construct(
        private readonly Instagram $instagram
    ) {}

    public function engine(): Instagram
    {
        return $this->instagram;
    }

    public function accountByUsername(string $username): Account
    {
        return $this->instagram->getAccount($username);
    }

    /**
     * @return list<Media>
     */
    public function timelineByUserId(int $userId, int $count = 24, string $maxId = ''): array
    {
        return $this->instagram->getMediasByUserId($userId, $count, $maxId);
    }
}
