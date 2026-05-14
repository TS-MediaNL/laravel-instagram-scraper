# laravel-instagram-scraper

Laravel-package om **publieke** Instagram-profielen uit te lezen (HTTP-scrape met Guzzle 7 / PSR-18). De kern-engine staat onder de namespace `TsMedia\LaravelInstagramScraper\InstagramScraper\` (eigen package-structuur; inhoudelijk gebaseerd op de MIT-licht **instagram-php-scraper**-familie).

**Let op:** Instagram kan scraping blokkeren of wijzigen. Gebruik dit alleen waar dat juridisch en ethisch mag.

---

## Nieuw Laravel-project (aanbevolen flow)

### 1. Project aanmaken

```bash
composer create-project laravel/laravel mijn-app
cd mijn-app
```

### 2. Package toevoegen

**Optie A — vanaf GitHub (als je deze repo al hebt gepusht)**

In `composer.json` van je app:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/TS-MediaNL/laravel-instagram-scraper.git"
    }
],
"require": {
    "tsmedia/laravel-instagram-scraper": "^0.2"
}
```

Daarna:

```bash
composer update tsmedia/laravel-instagram-scraper
```

**Optie B — lokaal als path (tijdens ontwikkelen)**

```json
"repositories": [
    {
        "type": "path",
        "url": "../laravel-instagram-scraper",
        "options": { "symlink": true }
    }
],
"require": {
    "tsmedia/laravel-instagram-scraper": "@dev"
}
```

Pas `url` aan naar waar je de package-map op schijf hebt staan.

### 3. Config (optioneel)

```bash
php artisan vendor:publish --tag=instagram-scraper-config
```

Daarna kun je in `.env` o.a. zetten:

```env
INSTAGRAM_SCRAPER_TIMEOUT=60
INSTAGRAM_SCRAPER_CONNECT_TIMEOUT=15
```

### 4. Gebruik in code

De package registreert automatisch (package discovery):

- `TsMedia\LaravelInstagramScraper\InstagramProfileClient` — **aanbevolen** entrypoint voor account + timeline.
- `TsMedia\LaravelInstagramScraper\InstagramScraper\Instagram` — volledige engine voor alle overige methodes op de scraper.

**Voorbeeld in een controller of command:**

```php
use TsMedia\LaravelInstagramScraper\InstagramProfileClient;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Exception\InstagramException;
use TsMedia\LaravelInstagramScraper\Support\MediaPayloadFactory;

public function __invoke()
{
    $instagram = app(InstagramProfileClient::class);

    try {
        $account = $instagram->accountByUsername('instagram');
        $userId = (int) $account->getId();
        $posts = $instagram->timelineByUserId($userId, 12, '');

        // Optioneel: clip-achtige arrays voor eigen mappers
        $clipItems = MediaPayloadFactory::videoClipItemsFromMedias($posts);
    } catch (InstagramException $e) {
        // rate limit, netwerk, gewijzigde HTML, enz.
    }
}
```

**Alle methodes van de engine** (tags, comments, …) via dezelfde instantie:

```php
$engine = app(InstagramProfileClient::class)->engine();
// $engine->getMediaById(...), enz.
```

### 5. Vereisten

- PHP **8.2+**
- Extensies: `json`, `curl` (zie `composer.json` van de package)

---

## Deze map als eigen GitHub-repository

```bash
cd /pad/naar/laravel-instagram-scraper
git init
git add .
git commit -m "Initial commit: Laravel Instagram scraper package"
git branch -M main
git remote add origin https://github.com/TS-MediaNL/laravel-instagram-scraper.git
git push -u origin main
```

Tag releases (bijv. `v0.2.0`) zodat consumer-apps op `^0.2` kunnen pinnen.

**Nog geen tags?** Gebruik tijdelijk in je Laravel-app:

```json
"require": {
    "tsmedia/laravel-instagram-scraper": "dev-main as 0.2.0"
}
```

(zolang `minimum-stability` op `stable` blijft, helpt de alias `as 0.2.0`.)

### Monorepo (bijv. Groteverbouwing.nl): van `path` naar GitHub

1. Zorg dat `https://github.com/TS-MediaNL/laravel-instagram-scraper` minstens `main` + `composer.json` heeft (eerste push, zie hierboven).
2. Vervang in de **root** `composer.json` van je app het `path`-repository door:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/TS-MediaNL/laravel-instagram-scraper.git"
    }
],
"require": {
    "tsmedia/laravel-instagram-scraper": "^0.2"
}
```

(of `dev-main as 0.2.0` tot de eerste tag `v0.2.0` bestaat).

3. Verwijder daarna de map `packages/laravel-instagram-scraper` uit het monorepo als je alleen nog vanaf GitHub wilt installeren (eerst committen/pushen wat je nodig hebt).

4. `composer update tsmedia/laravel-instagram-scraper`

**Let op:** Composer valideert elke VCS-repository direct. Een **lege** GitHub-repo zonder geldige `composer.json` op `main` laat **alle** `composer`-commando’s in dat project falen — eerst pushen, daarna pas `vcs` in `composer.json` zetten.

---

## Structuur (eigen code)

| Onderdeel | Namespace / rol |
|-----------|-----------------|
| Laravel-serviceprovider, client | `TsMedia\LaravelInstagramScraper\` (`src/*.php`, `src/Support/`) |
| Payload-hulp (`media` → nested array) | `TsMedia\LaravelInstagramScraper\Support\MediaPayloadFactory` |
| HTTP-scrape-engine (modellen, requests) | `TsMedia\LaravelInstagramScraper\InstagramScraper\` |

De engine is inhoudelijk verwant aan **instagram-php-scraper** (MIT); zie upstream voor oorspronkelijke auteurs. De Laravel-laag en namespace-indeling zijn TS-Media MIT (`LICENSE`).

---

## Overige mappen in je monorepo

Referentiekopies (andere forks of experimenten) horen **niet** in `composer.json` van je app tenzij je ze bewust als aparte dependency toevoegt. Eén dependency op `tsmedia/laravel-instagram-scraper` is genoeg.
