<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP client (Guzzle) opties
    |--------------------------------------------------------------------------
    |
    | Basisopties voor de Guzzle PSR-18 client. Waarden zijn rechtstreeks
    | doorgegeven aan GuzzleHttp\Client.
    |
    */
    'http' => [
        'timeout'         => (float) env('INSTAGRAM_SCRAPER_TIMEOUT', 60),
        'connect_timeout' => (float) env('INSTAGRAM_SCRAPER_CONNECT_TIMEOUT', 15),
        'http_errors'     => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Automatisch opnieuw proberen bij fouten
    |--------------------------------------------------------------------------
    |
    | Bij netwerkstoringen of rate-limiting (429 / 5xx) wordt het verzoek
    | automatisch herhaald met exponentiële vertraging.
    |
    | max_attempts : totaal aantal pogingen (1 = geen retry)
    | delay_ms     : basisvertraging in milliseconden vóór de eerste retry
    |                (wordt verdubbeld bij elke volgende poging)
    | on_codes     : HTTP-statuscodes die een retry triggeren
    |
    */
    'retry' => [
        'max_attempts' => (int) env('INSTAGRAM_SCRAPER_RETRY_MAX', 3),
        'delay_ms'     => (int) env('INSTAGRAM_SCRAPER_RETRY_DELAY_MS', 1000),
        'on_codes'     => [429, 500, 502, 503, 504],
    ],

    /*
    |--------------------------------------------------------------------------
    | User-Agent
    |--------------------------------------------------------------------------
    |
    | Overschrijf de standaard user-agent van de scraper. Laat leeg om de
    | ingebouwde waarde te gebruiken.
    |
    */
    'user_agent' => env('INSTAGRAM_SCRAPER_USER_AGENT'),

    /*
    |--------------------------------------------------------------------------
    | HTTP proxy
    |--------------------------------------------------------------------------
    |
    | Optionele proxy voor alle uitgaande verzoeken, bijv.:
    |   http://user:pass@proxy.example.com:8080
    |
    */
    'proxy' => env('INSTAGRAM_SCRAPER_PROXY'),

];
