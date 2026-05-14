<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP client (Guzzle) opties
    |--------------------------------------------------------------------------
    |
    | Wordt gebruikt voor InstagramScraper\Http\Request (PSR-18).
    |
    */
    'http' => [
        'timeout' => (float) env('INSTAGRAM_SCRAPER_TIMEOUT', 60),
        'connect_timeout' => (float) env('INSTAGRAM_SCRAPER_CONNECT_TIMEOUT', 15),
        'http_errors' => false,
    ],

];
