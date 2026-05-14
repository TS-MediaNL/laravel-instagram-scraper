<?php

namespace TsMedia\LaravelInstagramScraper\InstagramScraper\Exception;

class InstagramChallengeRecaptchaException extends InstagramException
{
    public function __construct($message = "", $code = 400, $responseBody = "", $previous = null)
    {
        parent::__construct($message, $code, $responseBody, $previous);
    }
}
