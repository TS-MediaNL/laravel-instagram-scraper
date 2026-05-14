#!/usr/bin/env php
<?php

/**
 * Live smoke-test: bootstrap PHPUnit met RUN_INSTAGRAM_NETWORK_TEST=1.
 *
 * Gebruik (in de root van deze package, na composer install):
 *   php scripts/smoke-test.php
 *   php scripts/smoke-test.php groteverbouwing
 *
 * De tweede argument is optioneel: Instagram-gebruikersnaam (zonder @).
 */

declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

if (! is_file($root . '/vendor/autoload.php')) {
    fwrite(STDERR, "Geen vendor/autoload.php — voer eerst uit: composer install\n");
    exit(1);
}

putenv('RUN_INSTAGRAM_NETWORK_TEST=1');

if (isset($argv[1]) && $argv[1] !== '') {
    putenv('INSTAGRAM_SMOKE_USERNAME=' . $argv[1]);
}

$phpunit = $root . '/vendor/bin/phpunit';
$target  = $root . '/tests/Feature/PackageNetworkSmokeTest.php';

if (! is_file($phpunit)) {
    fwrite(STDERR, "PHPUnit niet gevonden. composer install --dev uitgevoerd?\n");
    exit(1);
}

passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($phpunit) . ' ' . escapeshellarg($target), $code);
exit($code);
