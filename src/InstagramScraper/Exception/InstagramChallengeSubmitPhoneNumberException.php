<?php

declare(strict_types=1);

namespace TsMedia\LaravelInstagramScraper\InstagramScraper\Exception;

class InstagramChallengeSubmitPhoneNumberException extends InstagramException
{
    public function __construct($message = "", $code = 400, $responseBody = "", $previous = null)
    {
        parent::__construct($message, $code, $responseBody, $previous);
    }
}
