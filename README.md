# Laravel Instagram Scraper

Een Laravel package om **publieke** Instagram-profielen te scrapen via HTTP.  
Gebouwd op **Laravel's eigen HTTP client** (PSR-18 adapter), met automatische retry, proxy-ondersteuning en volledige integratie in het Laravel service-container systeem.

---

## Vereisten

| Vereiste | Versie |
|---|---|
| PHP | ^8.2 |
| Laravel | ^11.0 \| ^12.0 |
| ext-json | * |
| ext-curl | * |

---

## Installatie

```bash
composer require tsmedia/laravel-instagram-scraper
```

Laravel registreert de service provider en de `InstagramProfile` facade automatisch via package auto-discovery.

### Config publiceren (optioneel)

```bash
php artisan vendor:publish --tag=instagram-scraper-config
```

Dit plaatst `config/instagram-scraper.php` in je project zodat je alle opties kunt aanpassen.

---

## Configuratie

Voeg de gewenste waarden toe aan je `.env`:

```dotenv
# Timeouts (seconden)
INSTAGRAM_SCRAPER_TIMEOUT=60
INSTAGRAM_SCRAPER_CONNECT_TIMEOUT=15

# Automatische retry bij fouten
INSTAGRAM_SCRAPER_RETRY_MAX=3          # Totaal aantal pogingen (1 = geen retry)
INSTAGRAM_SCRAPER_RETRY_DELAY_MS=1000  # Basisvertraging in ms (wordt verdubbeld per poging)

# Optioneel: vaste user-agent
INSTAGRAM_SCRAPER_USER_AGENT=

# Optioneel: HTTP proxy
INSTAGRAM_SCRAPER_PROXY=http://user:pass@proxy.example.com:8080
```

### Retry-gedrag

De retry gebruikt **exponentiële backoff**: bij 3 pogingen en 1000ms basisvertraging zijn de wachttijden 1s → 2s → 4s.  
Standaard wordt opnieuw geprobeerd bij statuscodes: `429`, `500`, `502`, `503`, `504` en bij verbindingsfouten.

---

## Gebruik

### Via de `InstagramProfile` facade

De makkelijkste manier — gebruik dit in controllers, jobs en commands:

```php
use TsMedia\LaravelInstagramScraper\Facades\InstagramProfile;

// Accountinformatie ophalen
$account = InstagramProfile::accountByUsername('nasa');

echo $account->getUsername();       // nasa
echo $account->getFullName();       // NASA
echo $account->getBiography();      // Explore the universe...
echo $account->getFollowersCount(); // 97000000
echo $account->getMediaCount();     // 4200
echo $account->getProfilePicUrl();  // https://...
echo $account->isPrivate();         // false
echo $account->isVerified();        // true
```

### Via dependency injection

Aanbevolen in services en repositories — beter testbaar:

```php
use TsMedia\LaravelInstagramScraper\InstagramProfileClient;

class InstagramService
{
    public function __construct(
        private readonly InstagramProfileClient $instagram,
    ) {}

    public function getProfile(string $username): array
    {
        $account = $this->instagram->accountByUsername($username);

        return [
            'username'   => $account->getUsername(),
            'followers'  => $account->getFollowersCount(),
            'is_private' => $account->isPrivate(),
        ];
    }
}
```

---

## Alle beschikbare methoden

### `accountByUsername(string $username): Account`

Haal volledig account op via gebruikersnaam. Gooit `InstagramNotFoundException` als het account niet bestaat.

```php
$account = InstagramProfile::accountByUsername('natgeo');

$account->getId();               // numerieke user-ID (string)
$account->getUsername();
$account->getFullName();
$account->getBiography();
$account->getWebsite();
$account->getFollowersCount();
$account->getFollowsCount();
$account->getMediaCount();
$account->getProfilePicUrl();
$account->getProfilePicUrlHd();
$account->isPrivate();
$account->isVerified();
```

---

### `accountOrNull(string $username): ?Account`

Zoals `accountByUsername`, maar geeft `null` terug als het account niet bestaat — handig voor bulk-checks:

```php
$account = InstagramProfile::accountOrNull('might_not_exist');

if ($account === null) {
    // account bestaat niet of is privé
}
```

---

### `timelineByUserId(int $userId, int $count = 24, string $maxId = ''): array`

Haal de tijdlijn op van een specifiek account via de numerieke user-ID.  
Gebruik `$maxId` (de `id` van de laatste `Media`) voor paginering.

```php
$medias = InstagramProfile::timelineByUserId(528817151, count: 12);

foreach ($medias as $media) {
    echo $media->getShortCode();              // Bxy123abc
    echo $media->getType();                   // image | video | sidecar
    echo $media->getCaption();                // onderschrift
    echo $media->getLikesCount();
    echo $media->getCommentsCount();
    echo $media->getCreatedTime();            // Unix timestamp
    echo $media->getImageHighResolutionUrl(); // thumbnail URL
    echo $media->getLink();                   // https://www.instagram.com/p/Bxy123abc/
}

// Tweede pagina laden
$lastId = end($medias)->getId();
$page2  = InstagramProfile::timelineByUserId(528817151, count: 12, maxId: $lastId);
```

---

### `timelineByUsername(string $username, int $count = 24): array`

Korte variant als je alleen de gebruikersnaam weet (haalt userId intern op):

```php
$medias = InstagramProfile::timelineByUsername('nasa', count: 9);
```

---

### `mediasByTag(string $tag, int $count = 24): array`

Recente posts voor een hashtag:

```php
$medias = InstagramProfile::mediasByTag('amsterdam', count: 15);

foreach ($medias as $media) {
    echo $media->getShortCode();
    echo $media->getCaption();
}
```

---

### `mediaByShortCode(string $shortCode): Media`

Één post ophalen via de shortcode (het stuk in de URL na `/p/`):

```php
// URL: https://www.instagram.com/p/Bxy123abc/
$media = InstagramProfile::mediaByShortCode('Bxy123abc');

echo $media->getId();
echo $media->getCaption();
echo $media->getType();    // image | video | sidecar
echo $media->getVideoUrl(); // bij type video
```

---

### `commentsByShortCode(string $shortCode, int $count = 20, string $maxId = ''): array`

Comments van een post ophalen:

```php
$comments = InstagramProfile::commentsByShortCode('Bxy123abc', count: 50);

foreach ($comments as $comment) {
    echo $comment->getText();
    echo $comment->getCreatedAt();
    echo $comment->getOwner()->getUsername();
}
```

---

### `highlightsByUserId(int $userId): array`

Story highlights van een account:

```php
$highlights = InstagramProfile::highlightsByUserId(528817151);

foreach ($highlights as $highlight) {
    echo $highlight->getTitle();     // 'Behind the scenes'
    echo $highlight->getCoverUrl();  // thumbnail
}
```

---

### `locationById(int $locationId): Location`

Locatie-informatie op basis van een Facebook locatie-ID:

```php
$location = InstagramProfile::locationById(213385402);

echo $location->getName();
echo $location->getLat();
echo $location->getLng();
```

---

### `mediasByLocationId(int $locationId, int $count = 12): array`

Recente posts bij een locatie:

```php
$medias = InstagramProfile::mediasByLocationId(213385402, count: 9);
```

---

### `engine(): Instagram`

Directe toegang tot de volledige scraper-engine voor geavanceerde operaties:

```php
$engine = InstagramProfile::engine();

// Volgers ophalen (vereist login)
$followers = $engine->getFollowers($userId, $count = 100);

// Zoeken op tag
$tags = $engine->searchTagsByTagName('amsterdam');

// Inloggen met sessie
$engine->login();
```

---

## MediaPayloadFactory

Transformeer `Media`-objecten naar een genormaliseerd array-formaat (compatibel met RocketAPI-structuur):

```php
use TsMedia\LaravelInstagramScraper\Support\MediaPayloadFactory;

$medias = InstagramProfile::timelineByUserId(528817151, count: 24);

// Alle video's als clip-payload
$clips = MediaPayloadFactory::videoClipItemsFromMedias($medias);

foreach ($clips as $clip) {
    $clip['media']['pk'];          // ID
    $clip['media']['code'];        // shortcode
    $clip['media']['play_count'];  // views
    $clip['media']['like_count'];
    $clip['media']['comment_count'];
    $clip['media']['caption']['text'];
    $clip['media']['image_versions2']['candidates'][0]['url']; // thumbnail
}

// Opzoektabel: pk → true (voor snelle deduplicatie)
$seen = MediaPayloadFactory::feedPkLookupFromMedias($medias);

if (isset($seen[$someMediaId])) {
    // al verwerkt
}

// Eén media naar clip-formaat
$clip = MediaPayloadFactory::mediaToClipItem($medias[0]);
```

---

## Foutafhandeling

Alle exceptions staan in `TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\`:

```php
use TsMedia\LaravelInstagramScraper\Facades\InstagramProfile;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramAuthException;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramNotFoundException;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramAgeRestrictedException;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramException;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Http\NetworkException;

try {
    $account = InstagramProfile::accountByUsername($username);

} catch (InstagramNotFoundException $e) {
    // Account bestaat niet
    Log::warning("Instagram account niet gevonden: {$username}");

} catch (InstagramAgeRestrictedException $e) {
    // Account is leeftijdsbeperkt (403)
    Log::info("Leeftijdsbeperkt account: {$username}");

} catch (InstagramAuthException $e) {
    // Authenticatie vereist of sessie verlopen (401)
    Log::error('Instagram auth fout: ' . $e->getMessage());

} catch (NetworkException $e) {
    // Geen verbinding (DNS, timeout, proxy)
    Log::error('Netwerkfout: ' . $e->getMessage());

} catch (InstagramException $e) {
    // Algemene Instagram fout — bevat HTTP-code en response body
    Log::error("Instagram fout [{$e->getHttpCode()}]: {$e->getMessage()}");
    Log::debug('Response body: ' . $e->getResponseBody());
}
```

---

## Gebruik in een Laravel Job

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use TsMedia\LaravelInstagramScraper\InstagramProfileClient;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramNotFoundException;

class SyncInstagramProfile implements ShouldQueue
{
    use Dispatchable, Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly string $username,
    ) {}

    public function handle(InstagramProfileClient $instagram): void
    {
        $account = $instagram->accountOrNull($this->username);

        if ($account === null) {
            return;
        }

        \DB::table('instagram_profiles')->updateOrInsert(
            ['username' => $account->getUsername()],
            [
                'full_name'       => $account->getFullName(),
                'followers_count' => $account->getFollowersCount(),
                'media_count'     => $account->getMediaCount(),
                'is_verified'     => $account->isVerified(),
                'synced_at'       => now(),
            ],
        );
    }
}
```

---

## Gebruik in een Artisan Command

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use TsMedia\LaravelInstagramScraper\Facades\InstagramProfile;
use TsMedia\LaravelInstagramScraper\Support\MediaPayloadFactory;

class FetchInstagramReels extends Command
{
    protected $signature   = 'instagram:reels {username} {--count=12}';
    protected $description = 'Haal recente reels op voor een Instagram-account';

    public function handle(): int
    {
        $username = $this->argument('username');
        $count    = (int) $this->option('count');

        $this->info("Account ophalen: {$username}");
        $account  = InstagramProfile::accountByUsername($username);
        $userId   = (int) $account->getId();

        $this->info("Timeline ophalen ({$count} posts)...");
        $medias = InstagramProfile::timelineByUserId($userId, $count);

        $clips = MediaPayloadFactory::videoClipItemsFromMedias($medias);

        $this->table(
            ['Shortcode', 'Views', 'Likes'],
            array_map(fn($clip) => [
                $clip['media']['code'],
                number_format($clip['media']['play_count']),
                number_format($clip['media']['like_count']),
            ], $clips),
        );

        $this->info(count($clips) . ' video(s) gevonden.');

        return self::SUCCESS;
    }
}
```

---

## Testen (Http::fake)

Omdat de package Laravel's HTTP client gebruikt kun je verzoeken mocken met `Http::fake()`:

```php
use Illuminate\Support\Facades\Http;
use TsMedia\LaravelInstagramScraper\Facades\InstagramProfile;

Http::fake([
    'www.instagram.com/api/v1/users/web_profile_info/*' => Http::response(
        file_get_contents(base_path('tests/fixtures/instagram_account.json')),
        200,
    ),
]);

$account = InstagramProfile::accountByUsername('nasa');

Http::assertSent(fn ($request) =>
    str_contains($request->url(), 'web_profile_info')
);
```

---

## HTTP-integratie

De package gebruikt **Laravel's eigen HTTP client** (`Illuminate\Http\Client`) als PSR-18 adapter.  
Dat betekent:

- **Logging**: alle requests verschijnen automatisch in Laravel Telescope / Debugbar
- **Fake/Mock**: `Http::fake()` werkt out-of-the-box in tests
- **Events**: `RequestSending`, `ResponseReceived`, `ConnectionFailed` worden gefired
- **Macros**: je kunt eigen macros op de HTTP client registreren

---

## Configuratie-referentie

```php
// config/instagram-scraper.php
return [
    'http' => [
        'timeout'         => 60,    // Maximale request-tijd in seconden
        'connect_timeout' => 15,    // Maximale verbindingstijd in seconden
        'http_errors'     => false, // Geen exceptions op HTTP-fouten (scraper doet eigen afhandeling)
    ],

    'retry' => [
        'max_attempts' => 3,                            // Totaal aantal pogingen
        'delay_ms'     => 1000,                         // Basisvertraging (exponentieel)
        'on_codes'     => [429, 500, 502, 503, 504],    // Codes die retry triggeren
    ],

    'user_agent' => null, // Overschrijf de standaard user-agent (null = gebruik ingebouwde)
    'proxy'      => null, // HTTP-proxy URL (null = geen proxy)
];
```

---

## Licentie

MIT — © 2026 [TS-Media](https://groteverbouwing.nl)
