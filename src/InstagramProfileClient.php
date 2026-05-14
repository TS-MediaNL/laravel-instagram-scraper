<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper;

use TsMedia\LaravelInstagramScraper\InstagramScraper\Instagram;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Account;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Comment;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Highlight;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Location;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Media;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramNotFoundException;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramException;

/**
 * Aanbevolen entrypoint in Laravel. Biedt de meestgebruikte scraper-operaties
 * zonder dat je direct met de engine hoeft te werken.
 *
 * Voor geavanceerde calls: {@see self::engine()}.
 */
final class InstagramProfileClient
{
    public function __construct(
        private readonly Instagram $instagram,
    ) {}

    /**
     * Directe toegang tot de volledige scraper-engine.
     */
    public function engine(): Instagram
    {
        return $this->instagram;
    }

    /**
     * Haal accountinformatie op via gebruikersnaam.
     *
     * @throws InstagramNotFoundException
     * @throws InstagramException
     */
    public function accountByUsername(string $username): Account
    {
        return $this->instagram->getAccount($username);
    }

    /**
     * Haal accountinformatie op via gebruikersnaam, of null als het account niet bestaat.
     *
     * @throws InstagramException
     */
    public function accountOrNull(string $username): ?Account
    {
        try {
            return $this->instagram->getAccount($username);
        } catch (InstagramNotFoundException) {
            return null;
        }
    }

    /**
     * Haal de tijdlijn van een account op via userId.
     *
     * @return list<Media>
     * @throws InstagramException
     */
    public function timelineByUserId(int $userId, int $count = 24, string $maxId = ''): array
    {
        return $this->instagram->getMediasByUserId($userId, $count, $maxId);
    }

    /**
     * Haal de posts op direct via een Account-object (meest efficiënt — geen extra lookup).
     * Aanbevolen flow:
     *   $account = $client->accountByUsername('nasa');
     *   $posts   = $client->timelineByAccount($account);
     *
     * @return list<Media>
     * @throws InstagramException
     */
    public function timelineByAccount(Account $account, int $count = 24): array
    {
        return $this->instagram->getMediasByUsername($account->getUsername(), $count);
    }

    /**
     * Haal de posts op via gebruikersnaam.
     *
     * @return list<Media>
     * @throws InstagramException
     */
    public function timelineByUsername(string $username, int $count = 24): array
    {
        return $this->instagram->getMediasByUsername($username, $count);
    }

    /**
     * Haal de laatste media op voor een hashtag.
     *
     * @return list<Media>
     * @throws InstagramException
     */
    public function mediasByTag(string $tag, int $count = 24): array
    {
        return $this->instagram->getMediasByTag($tag, $count);
    }

    /**
     * Haal de highlights op van een account.
     *
     * @return list<Highlight>
     * @throws InstagramException
     */
    public function highlightsByUserId(int $userId): array
    {
        return $this->instagram->getHighlights($userId);
    }

    /**
     * Haal comments op voor een specifieke media-shortcode.
     *
     * @return list<Comment>
     * @throws InstagramException
     */
    public function commentsByShortCode(string $shortCode, int $count = 20, string $maxId = ''): array
    {
        return $this->instagram->getMediaCommentsByCode($shortCode, $count, $maxId);
    }

    /**
     * Haal een enkel media-object op via shortcode (bv. "Bxy123abc").
     *
     * @throws InstagramNotFoundException
     * @throws InstagramException
     */
    public function mediaByShortCode(string $shortCode): Media
    {
        return $this->instagram->getMediaByCode($shortCode);
    }

    /**
     * Haal locatie-informatie op via Facebook locatie-ID.
     *
     * @throws InstagramException
     */
    public function locationById(int $locationId): Location
    {
        return $this->instagram->getLocationById($locationId);
    }

    /**
     * Haal recente media op voor een locatie.
     *
     * @return list<Media>
     * @throws InstagramException
     */
    public function mediasByLocationId(int $locationId, int $count = 12): array
    {
        return $this->instagram->getMediasByLocationId($locationId, $count);
    }
}
