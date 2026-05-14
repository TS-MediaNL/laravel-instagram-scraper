<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\InstagramScraper;

class Endpoints
{
    const BASE_URL = 'https://www.instagram.com';
    const LOGIN_URL = 'https://www.instagram.com/accounts/login/ajax/';
    const ACCOUNT_PAGE = 'https://www.instagram.com/api/v1/users/web_profile_info/?username={username}';
    const MEDIA_LINK = 'https://www.instagram.com/p/{code}';
    const ACCOUNT_MEDIAS = 'https://www.instagram.com/graphql/query/?query_hash=e769aa130647d2354c40ea6a439bfc08&variables={variables}';
    const ACCOUNT_TAGGED_MEDIAS = 'https://www.instagram.com/graphql/query/?query_hash=be13233562af2d229b008d2976b998b5&variables={variables}';
    const ACCOUNT_JSON_INFO = 'https://www.instagram.com/{username}/?__a=1&__d=dis';
    const ACCOUNT_ACTIVITY = 'https://www.instagram.com/accounts/activity/?__a=1';
    const MEDIA_JSON_INFO = 'https://www.instagram.com/p/{code}/?__a=1&__d=dis';
    const MEDIA_JSON_BY_LOCATION_ID = 'https://www.instagram.com/explore/locations/{{facebookLocationId}}/?__a=1&max_id={{maxId}}';
    const MEDIA_JSON_BY_TAG = 'https://www.instagram.com/explore/tags/{tag}/?__a=1&max_id={max_id}&__d=dis';
    const GENERAL_SEARCH = 'https://www.instagram.com/web/search/topsearch/?query={query}&count={count}';
    const ACCOUNT_JSON_INFO_BY_ID = 'ig_user({userId}){id,username,external_url,full_name,profile_pic_url,biography,followed_by{count},follows{count},media{count},is_private,is_verified}';
    const COMMENTS_BEFORE_COMMENT_ID_BY_CODE = 'https://www.instagram.com/graphql/query/?query_hash=33ba35852cb50da46f5b5e889df7d159&variables={variables}';
    const LAST_LIKES_BY_CODE = 'ig_shortcode({{code}}){likes{nodes{id,user{id,profile_pic_url,username,follows{count},followed_by{count},biography,full_name,media{count},is_private,external_url,is_verified}},page_info}}';
    const LIKES_BY_SHORTCODE = 'https://www.instagram.com/graphql/query/?query_id=17864450716183058&variables={"shortcode":"{{shortcode}}","first":{{count}},"after":"{{likeId}}"}';
    const FOLLOWING_URL = 'https://www.instagram.com/graphql/query/?query_id=17874545323001329&id={{accountId}}&first={{count}}&after={{after}}';
    const FOLLOWERS_URL = 'https://www.instagram.com/graphql/query/?query_id=17851374694183129&id={{accountId}}&first={{count}}&after={{after}}';
    const FOLLOWING_URL_V1 = 'https://i.instagram.com/api/v1/friendships/{{accountId}}/following/';
    const FOLLOWERS_URL_V1 = 'https://i.instagram.com/api/v1/friendships/{{accountId}}/followers/';
    const FOLLOW_URL = 'https://www.instagram.com/web/friendships/{{accountId}}/follow/';
    const UNFOLLOW_URL = 'https://www.instagram.com/web/friendships/{{accountId}}/unfollow/';
    const REMOVE_FOLLOWER_URL = 'https://www.instagram.com/web/friendships/{{accountId}}/remove_follower/';
    const PENDING_URL = 'https://i.instagram.com/api/v1/friendships/pending/';
    const INBOX_NEWS_URL = 'https://i.instagram.com/api/v1/news/inbox/';
    const INBOX_NEWS_SEEN_URL = 'https://i.instagram.com/api/v1/news/inbox_seen/';
    const USER_TAGS = 'https://i.instagram.com/api/v1/usertags/{{accountId}}/feed/?count={{count}}';
    const USER_FEED = 'https://www.instagram.com/graphql/query/?query_id=17861995474116400&fetch_media_item_count=12&fetch_media_item_cursor=&fetch_comment_count=4&fetch_like=10';
    const USER_FEED2 = 'https://www.instagram.com/?__a=1';
    const USER_FEED_HASH = 'https://www.instagram.com/graphql/query/?query_hash=3f01472fb28fb8aca9ad9dbc9d4578ff';
    const INSTAGRAM_QUERY_URL = 'https://www.instagram.com/query/';
    const INSTAGRAM_CDN_URL = 'https://scontent.cdninstagram.com/';
    const ACCOUNT_JSON_PRIVATE_INFO_BY_ID = 'https://i.instagram.com/api/v1/users/{userId}/info/';
    const ACCOUNT_JSON_PRIVATE_INFO_BY_ID_2 = 'https://www.instagram.com/graphql/query/?query_hash=c9100bf9110dd6361671f113dd02e7d6&variables={"user_id":"{userId}","include_chaining":false,"include_reel":true,"include_suggested_users":false,"include_logged_out_extras":false,"include_highlight_reels":false,"include_related_profiles":false}';
    const LIKE_URL = 'https://www.instagram.com/web/likes/{mediaId}/like/';
    const UNLIKE_URL = 'https://www.instagram.com/web/likes/{mediaId}/unlike/';
    const ADD_COMMENT_URL = 'https://www.instagram.com/web/comments/{mediaId}/add/';
    const DELETE_COMMENT_URL = 'https://www.instagram.com/web/comments/{mediaId}/delete/{commentId}/';
    const ACCOUNT_MEDIAS2 = 'https://www.instagram.com/graphql/query/?query_id=17880160963012870&id={{accountId}}&first=10&after=';
    const HIGHLIGHT_URL = 'https://www.instagram.com/graphql/query/?query_hash=c9100bf9110dd6361671f113dd02e7d6&variables={"user_id":"{userId}","include_chaining":false,"include_reel":true,"include_suggested_users":false,"include_logged_out_extras":false,"include_highlight_reels":true,"include_live_status":false}';
    const HIGHLIGHT_STORIES = 'https://www.instagram.com/graphql/query/?query_hash=45246d3fe16ccc6577e0bd297a5db1ab';
    const THREADS_URL = 'https://i.instagram.com/api/v1/direct_v2/inbox/?persistentBadging=true&folder=&limit={limit}&thread_message_limit={messageLimit}&cursor={cursor}';
    const THREADS_PENDING_REQUESTS_URL = 'https://i.instagram.com/api/v1/direct_v2/pending_inbox/?limit={limit}&cursor={cursor}';
    const THREADS_APPROVE_MULTIPLE_URL = 'https://i.instagram.com/api/v1/direct_v2/threads/approve_multiple/';
    const GRAPH_QL_QUERY_URL = 'https://www.instagram.com/graphql/query/?query_id={{queryId}}';

    /**
     * Stabiele v1 feed-endpoint — vervangt de verouderde GraphQL query_hash-variant.
     * Geeft items, more_available en next_max_id terug.
     */
    const USER_FEED_V1 = 'https://www.instagram.com/api/v1/feed/user/{userId}/?count={count}&max_id={maxId}';

    private static int $requestMediaCount = 30;

    public static function setAccountMediasRequestCount(int $count): void
    {
        static::$requestMediaCount = $count;
    }

    public static function getAccountMediasRequestCount(): int
    {
        return static::$requestMediaCount;
    }

    public static function getAccountPageLink(string $username): string
    {
        return str_replace('{username}', urlencode($username), static::ACCOUNT_PAGE);
    }

    public static function getAccountJsonLink(string $username): string
    {
        return str_replace('{username}', urlencode($username), static::ACCOUNT_JSON_INFO);
    }

    public static function getAccountJsonInfoLinkByAccountId(string|int $id): string
    {
        return str_replace('{userId}', urlencode((string) $id), static::ACCOUNT_JSON_INFO_BY_ID);
    }

    public static function getAccountJsonPrivateInfoLinkByAccountId(string|int $id): string
    {
        return str_replace('{userId}', urlencode((string) $id), static::ACCOUNT_JSON_PRIVATE_INFO_BY_ID_2);
    }

    public static function getAccountMediasJsonLink(string $variables): string
    {
        return str_replace('{variables}', urlencode($variables), static::ACCOUNT_MEDIAS);
    }

    public static function getAccountTaggedMediasJsonLink(string $variables): string
    {
        return str_replace('{variables}', urlencode($variables), static::ACCOUNT_TAGGED_MEDIAS);
    }

    public static function getMediaPageLink(string $code): string
    {
        return str_replace('{code}', urlencode($code), static::MEDIA_LINK);
    }

    public static function getMediaJsonLink(string $code): string
    {
        return str_replace('{code}', urlencode($code), static::MEDIA_JSON_INFO);
    }

    public static function getMediasJsonByLocationIdLink(string $facebookLocationId, string $maxId = ''): string
    {
        $url = str_replace('{{facebookLocationId}}', urlencode($facebookLocationId), static::MEDIA_JSON_BY_LOCATION_ID);

        return str_replace('{{maxId}}', urlencode($maxId), $url);
    }

    public static function getMediasJsonByTagLink(string $tag, string $maxId = ''): string
    {
        $url = str_replace('{tag}', urlencode($tag), static::MEDIA_JSON_BY_TAG);

        return str_replace('{max_id}', urlencode($maxId), $url);
    }

    public static function getGeneralSearchJsonLink(string $query, int $count = 10): string
    {
        $url = str_replace('{query}', urlencode($query), static::GENERAL_SEARCH);

        return str_replace('{count}', urlencode((string) $count), $url);
    }

    public static function getCommentsBeforeCommentIdByCode(string $variables): string
    {
        return str_replace('{variables}', urlencode($variables), static::COMMENTS_BEFORE_COMMENT_ID_BY_CODE);
    }

    public static function getLastLikesByCodeLink(string $code): string
    {
        return str_replace('{{code}}', urlencode($code), static::LAST_LIKES_BY_CODE);
    }

    public static function getLastLikesByCode(string $code, int $count, string $lastLikeID): string
    {
        $url = str_replace('{{shortcode}}', urlencode($code), static::LIKES_BY_SHORTCODE);
        $url = str_replace('{{count}}', urlencode((string) $count), $url);

        return str_replace('{{likeId}}', urlencode($lastLikeID), $url);
    }

    public static function getActivityUrl(): string
    {
        return static::ACCOUNT_ACTIVITY;
    }

    public static function getFollowUrl(string|int $accountId): string
    {
        return str_replace('{{accountId}}', urlencode((string) $accountId), static::FOLLOW_URL);
    }

    public static function getUnfollowUrl(string|int $accountId): string
    {
        return str_replace('{{accountId}}', urlencode((string) $accountId), static::UNFOLLOW_URL);
    }

    public static function getRemoveFollowerUrl(string|int $accountId): string
    {
        return str_replace('{{accountId}}', urlencode((string) $accountId), static::REMOVE_FOLLOWER_URL);
    }

    public static function getPendingUrl(): string
    {
        return static::PENDING_URL;
    }

    public static function getInboxNewsUrl(): string
    {
        return static::INBOX_NEWS_URL;
    }

    public static function getInboxNewsSeenUrl(): string
    {
        return static::INBOX_NEWS_SEEN_URL;
    }

    public static function getUserTagsUrl(string|int $accountId, int $count = 12): string
    {
        return str_replace(
            ['{{accountId}}', '{{count}}'],
            [urlencode((string) $accountId), urlencode((string) $count)],
            static::USER_TAGS,
        );
    }

    public static function getFollowersJsonLink(string|int $accountId, int $count, string $after = ''): string
    {
        $url = str_replace('{{accountId}}', urlencode((string) $accountId), static::FOLLOWERS_URL);
        $url = str_replace('{{count}}', urlencode((string) $count), $url);

        if ($after === '') {
            return str_replace('&after={{after}}', '', $url);
        }

        return str_replace('{{after}}', urlencode($after), $url);
    }

    public static function getFollowingJsonLink(string|int $accountId, int $count, string $after = ''): string
    {
        $url = str_replace('{{accountId}}', urlencode((string) $accountId), static::FOLLOWING_URL);
        $url = str_replace('{{count}}', urlencode((string) $count), $url);

        if ($after === '') {
            return str_replace('&after={{after}}', '', $url);
        }

        return str_replace('{{after}}', urlencode($after), $url);
    }

    public static function getFollowersUrlV1(string|int $accountId): string
    {
        return str_replace('{{accountId}}', urlencode((string) $accountId), static::FOLLOWERS_URL_V1);
    }

    /**
     * @deprecated Use {@see self::getFollowersUrlV1()} instead.
     */
    public static function getFollowersUrl_v1(string|int $accountId): string
    {
        return self::getFollowersUrlV1($accountId);
    }

    public static function getFollowingUrlV1(string|int $accountId): string
    {
        return str_replace('{{accountId}}', urlencode((string) $accountId), static::FOLLOWING_URL_V1);
    }

    /**
     * @deprecated Use {@see self::getFollowingUrlV1()} instead.
     */
    public static function getFollowingUrl_v1(string|int $accountId): string
    {
        return self::getFollowingUrlV1($accountId);
    }

    /**
     * @param array<string, mixed> $variables
     */
    public static function getUserStoriesLink(array $variables = []): string
    {
        return self::getGraphQlUrl(InstagramQueryId::USER_STORIES, ['variables' => json_encode($variables)]);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public static function getGraphQlUrl(string $queryId, array $parameters = []): string
    {
        $url = str_replace('{{queryId}}', urlencode($queryId), static::GRAPH_QL_QUERY_URL);

        if (! empty($parameters)) {
            $url .= '&' . http_build_query($parameters);
        }

        return $url;
    }

    /**
     * @param array<string, mixed> $variables
     */
    public static function getStoriesLink(array $variables): string
    {
        return self::getGraphQlUrl(InstagramQueryId::STORIES, ['variables' => json_encode($variables)]);
    }

    public static function getLikeUrl(string|int $mediaId): string
    {
        return str_replace('{mediaId}', urlencode((string) $mediaId), static::LIKE_URL);
    }

    public static function getUnlikeUrl(string|int $mediaId): string
    {
        return str_replace('{mediaId}', urlencode((string) $mediaId), static::UNLIKE_URL);
    }

    public static function getAddCommentUrl(string|int $mediaId): string
    {
        return str_replace('{mediaId}', (string) $mediaId, static::ADD_COMMENT_URL);
    }

    public static function getDeleteCommentUrl(string|int $mediaId, string|int $commentId): string
    {
        $url = str_replace('{mediaId}', (string) $mediaId, static::DELETE_COMMENT_URL);

        return str_replace('{commentId}', (string) $commentId, $url);
    }

    public static function getHighlightUrl(string|int $userId): string
    {
        return str_replace('{userId}', urlencode((string) $userId), static::HIGHLIGHT_URL);
    }

    public static function getThreadsUrl(int $limit, int $messageLimit, string $cursor): string
    {
        $url = str_replace('{limit}', (string) $limit, static::THREADS_URL);
        $url = str_replace('{messageLimit}', (string) $messageLimit, $url);

        return str_replace('{cursor}', $cursor, $url);
    }

    public static function getThreadsPendingRequestsUrl(int $limit, ?string $cursor = null): string
    {
        $url = str_replace('{limit}', (string) $limit, static::THREADS_PENDING_REQUESTS_URL);

        return str_replace('{cursor}', (string) $cursor, $url);
    }

    public static function getThreadsApproveMultipleUrl(): string
    {
        return static::THREADS_APPROVE_MULTIPLE_URL;
    }

    public static function getUserFeedV1(string|int $userId, int $count = 12, string $maxId = ''): string
    {
        $url = str_replace('{userId}', urlencode((string) $userId), static::USER_FEED_V1);
        $url = str_replace('{count}', urlencode((string) $count), $url);

        return str_replace('{maxId}', urlencode($maxId), $url);
    }
}
