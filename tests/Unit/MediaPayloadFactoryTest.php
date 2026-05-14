<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Media;
use TsMedia\LaravelInstagramScraper\Support\MediaPayloadFactory;

final class MediaPayloadFactoryTest extends TestCase
{
    /**
     * Build a Media object using the same API-key conventions as the scraper.
     *
     * @param array<string, mixed> $overrides
     */
    private function makeMedia(array $overrides = []): Media
    {
        $defaults = [
            'id'           => '123456789',
            'shortcode'    => 'Bxy123abc',
            'caption'      => ['text' => 'Test caption'],
            'created_time' => 1700000000,
            'video_views'  => 500,
            'like_count'   => 100,
            'comment_count' => 20,
            'display_url'  => 'https://example.com/image.jpg',
        ];

        return Media::create(array_merge($defaults, $overrides));
    }

    public function test_media_to_clip_item_returns_expected_structure(): void
    {
        $media  = $this->makeMedia();
        $result = MediaPayloadFactory::mediaToClipItem($media);

        $this->assertArrayHasKey('media', $result);
        $this->assertSame('123456789', $result['media']['pk']);
        $this->assertSame('123456789', $result['media']['id']);
        $this->assertSame('Bxy123abc', $result['media']['code']);
        $this->assertSame('Test caption', $result['media']['caption']['text']);
        $this->assertSame(500, $result['media']['play_count']);
        $this->assertSame(100, $result['media']['like_count']);
        $this->assertSame(20, $result['media']['comment_count']);
        $this->assertSame('https://example.com/image.jpg', $result['media']['image_versions2']['candidates'][0]['url']);
    }

    public function test_video_clip_items_from_medias_filters_non_video(): void
    {
        $video = $this->makeMedia();
        $image = $this->makeMedia(['video_views' => null, 'type' => Media::TYPE_IMAGE]);

        $results = MediaPayloadFactory::videoClipItemsFromMedias([$video, $image]);

        $this->assertCount(1, $results);
        $this->assertSame('123456789', $results[0]['media']['pk']);
    }

    public function test_video_clip_items_returns_empty_for_no_videos(): void
    {
        $image = $this->makeMedia(['video_views' => null, 'type' => Media::TYPE_IMAGE]);

        $results = MediaPayloadFactory::videoClipItemsFromMedias([$image]);

        $this->assertSame([], $results);
    }

    public function test_feed_pk_lookup_from_medias_builds_map(): void
    {
        $media  = $this->makeMedia();
        $lookup = MediaPayloadFactory::feedPkLookupFromMedias([$media]);

        $this->assertArrayHasKey('123456789', $lookup);
        $this->assertTrue($lookup['123456789']);
    }

    public function test_feed_pk_lookup_skips_media_without_id(): void
    {
        $media  = Media::create();
        $lookup = MediaPayloadFactory::feedPkLookupFromMedias([$media]);

        $this->assertSame([], $lookup);
    }

    public function test_feed_pk_lookup_ignores_non_media_items(): void
    {
        /** @phpstan-ignore-next-line */
        $lookup = MediaPayloadFactory::feedPkLookupFromMedias(['not-a-media']);

        $this->assertSame([], $lookup);
    }

    public function test_play_count_is_never_negative(): void
    {
        $media  = $this->makeMedia(['video_views' => -99]);
        $result = MediaPayloadFactory::mediaToClipItem($media);

        $this->assertSame(0, $result['media']['play_count']);
    }

    public function test_thumbnail_fallback_when_no_high_res(): void
    {
        $media = Media::create([
            'id'            => '999',
            'shortcode'     => 'abc',
            'thumbnail_src' => 'https://example.com/thumb.jpg',
            'video_views'   => 10,
        ]);

        $result = MediaPayloadFactory::mediaToClipItem($media);
        $url    = $result['media']['image_versions2']['candidates'][0]['url'];

        $this->assertSame('https://example.com/thumb.jpg', $url);
    }
}
