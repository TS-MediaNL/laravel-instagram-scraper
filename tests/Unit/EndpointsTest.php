<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Endpoints;

final class EndpointsTest extends TestCase
{
    public function test_account_page_link_encodes_username(): void
    {
        $link = Endpoints::getAccountPageLink('test user');

        $this->assertStringContainsString('test+user', $link);
    }

    public function test_followers_link_omits_after_param_when_empty(): void
    {
        $link = Endpoints::getFollowersJsonLink(12345, 50);

        $this->assertStringNotContainsString('after', $link);
    }

    public function test_followers_link_includes_after_when_set(): void
    {
        $link = Endpoints::getFollowersJsonLink(12345, 50, 'cursor123');

        $this->assertStringContainsString('cursor123', $link);
    }

    public function test_deprecated_v1_methods_delegate_correctly(): void
    {
        $this->assertSame(
            Endpoints::getFollowersUrlV1(999),
            Endpoints::getFollowersUrl_v1(999),
        );

        $this->assertSame(
            Endpoints::getFollowingUrlV1(999),
            Endpoints::getFollowingUrl_v1(999),
        );
    }

    public function test_media_request_count_can_be_changed(): void
    {
        $original = Endpoints::getAccountMediasRequestCount();
        Endpoints::setAccountMediasRequestCount(50);

        $this->assertSame(50, Endpoints::getAccountMediasRequestCount());

        Endpoints::setAccountMediasRequestCount($original);
    }

    public function test_delete_comment_url_contains_both_ids(): void
    {
        $url = Endpoints::getDeleteCommentUrl('media999', 'comment111');

        $this->assertStringContainsString('media999', $url);
        $this->assertStringContainsString('comment111', $url);
    }
}
