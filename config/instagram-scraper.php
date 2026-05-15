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
    | Vertraging tussen requests (anti-detectie)
    |--------------------------------------------------------------------------
    |
    | Een willekeurige pauze tussen opeenvolgende pagineringsrequests voorkomt
    | dat het verkeerspatroon op geautomatiseerd gedrag lijkt. De wachttijd
    | wordt willekeurig gekozen tussen min_ms en max_ms.
    |
    | Alleen van toepassing op paginering (meerdere requests voor hetzelfde
    | account). Enkelvoudige opzoekingen worden niet vertraagd.
    |
    | Aanbevolen waarden:
    |   Laag profiel  : min=1000, max=3000
    |   Normaal       : min=500,  max=2000  (standaard)
    |   Snel (risico) : min=200,  max=800
    |
    */
    'request_delay' => [
        'min_ms' => (int) env('INSTAGRAM_SCRAPER_DELAY_MIN_MS', 500),
        'max_ms' => (int) env('INSTAGRAM_SCRAPER_DELAY_MAX_MS', 2000),
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
        'max_attempts'        => (int) env('INSTAGRAM_SCRAPER_RETRY_MAX', 2),
        'delay_ms'            => (int) env('INSTAGRAM_SCRAPER_RETRY_DELAY_MS', 2000),

        /*
        | Vertraging specifiek voor HTTP 429 (rate limit). Instagram blokkeert
        | doorgaans 30-60+ seconden per rate-limit event; de normale retry-delay
        | (delay_ms) is hiervoor veel te kort.
        |
        | Standaard: 60 000 ms = 60 seconden per poging.
        | Bij max_attempts=2 wacht de scraper dus 60s voor de tweede poging.
        | Als de 429 aanhoudt, wordt InstagramRateLimitException gegooid.
        |
        | Zet op 0 om 429-retries uit te schakelen (fout meteen gooien).
        */
        'rate_limit_delay_ms' => (int) env('INSTAGRAM_SCRAPER_RATE_LIMIT_DELAY_MS', 60_000),

        'on_codes'            => [429, 500, 502, 503, 504],
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

    /*
    |--------------------------------------------------------------------------
    | Instagram authenticatie (optioneel)
    |--------------------------------------------------------------------------
    |
    | Zonder authenticatie werkt de scraper publiek, maar is beperkt tot
    | ±12 posts per profiel via de web_profile_info endpoint. Met een
    | ingelogde sessie zijn meer posts, reels, trial reels en privé-profielen
    | bereikbaar.
    |
    | AANBEVOLEN — session_id:
    |   Haal de "sessionid" cookie op uit je browser nadat je op
    |   instagram.com bent ingelogd (DevTools → Application → Cookies).
    |   Zet hem in INSTAGRAM_SCRAPER_SESSION_ID. Geen wachtwoord nodig.
    |   Geldig totdat je uitlogt of het verlopen is (±90 dagen).
    |
    | ALTERNATIEF — username + password:
    |   De package logt automatisch in en slaat de sessie op in de
    |   Laravel cache. Werkt mogelijk niet bij 2FA-accounts of als
    |   Instagram de inlog blokkeert.
    |
    | Laat alle drie leeg om anoniem te scrapen.
    |
    */
    'auth' => [
        'session_id' => env('INSTAGRAM_SCRAPER_SESSION_ID'),
        'username'   => env('INSTAGRAM_SCRAPER_USERNAME'),
        'password'   => env('INSTAGRAM_SCRAPER_PASSWORD'),
    ],

];
