<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Support;

use TsMedia\LaravelInstagramScraper\InstagramScraper\Model\Media;

/**
 * Zet {@see Media} om naar de nested structuur die compatibel is met veel RocketAPI-/clip-mappers
 * (velden onder <code>media.*</code>).
 */
final class MediaPayloadFactory
{
    /**
     * @return array<string, mixed>
     */
    public static function mediaToClipItem(Media $media): array
    {
        $id = $media->getId();
        $caption = $media->getCaption();
        $thumb = $media->getImageHighResolutionUrl()
            ?: $media->getImageStandardResolutionUrl()
            ?: $media->getImageThumbnailUrl();

        return [
            'media' => [
                'pk' => $id,
                'id' => $id,
                'code' => $media->getShortCode(),
                'caption' => [
                    'text' => $caption,
                    'created_at_utc' => $media->getCreatedTime(),
                ],
                'image_versions2' => [
                    'candidates' => [['url' => $thumb]],
                ],
                'play_count' => max(0, (int) $media->getVideoViews()),
                'like_count' => (int) $media->getLikesCount(),
                'comment_count' => (int) $media->getCommentsCount(),
                'user' => [],
                'clips_metadata' => [
                    'is_pinned' => false,
                ],
                'is_shared_to_feed' => true,
            ],
        ];
    }

    /**
     * pk-keys voor feed-lookup (platte items, zelfde sleutel als <code>mergeKey(['pk' => …])</code>).
     *
     * @param  list<Media>  $medias
     * @return array<string, true>
     */
    public static function feedPkLookupFromMedias(array $medias): array
    {
        $lookup = [];
        foreach ($medias as $media) {
            if (! $media instanceof Media) {
                continue;
            }
            $k = self::primaryKey($media);
            if ($k !== null) {
                $lookup[$k] = true;
            }
        }

        return $lookup;
    }

    /**
     * @param  list<Media>  $medias
     * @return list<array<string, mixed>>
     */
    public static function videoClipItemsFromMedias(array $medias): array
    {
        $out = [];
        foreach ($medias as $media) {
            if (! $media instanceof Media) {
                continue;
            }
            if ($media->getType() !== Media::TYPE_VIDEO) {
                continue;
            }
            $out[] = self::mediaToClipItem($media);
        }

        return $out;
    }

    private static function primaryKey(Media $media): ?string
    {
        $id = $media->getId();
        if ($id === null || $id === '') {
            return null;
        }

        return (string) $id;
    }
}
