<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Facades;

use Illuminate\Support\Facades\Facade;
use TsMedia\LaravelInstagramScraper\InstagramProfileClient;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Instagram;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Account;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Media;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Comment;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Highlight;

/**
 * @method static Instagram engine()
 * @method static Account accountByUsername(string $username)
 * @method static list<Media> timelineByUserId(int $userId, int $count = 24, string $maxId = '')
 * @method static list<Media> highlightsByUserId(int $userId)
 * @method static list<Comment> commentsByShortCode(string $shortCode, int $count = 20, string $maxId = '')
 * @method static Account|null accountOrNull(string $username)
 *
 * @see InstagramProfileClient
 */
class InstagramProfile extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return InstagramProfileClient::class;
    }
}
