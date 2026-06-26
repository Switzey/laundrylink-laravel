<?php

$storagePath = $_ENV['APP_STORAGE_PATH'] ?? $_SERVER['APP_STORAGE_PATH'] ?? '/tmp/laundrylink-storage';

$_ENV['APP_STORAGE_PATH'] = $storagePath;
$_SERVER['APP_STORAGE_PATH'] = $storagePath;
putenv("APP_STORAGE_PATH={$storagePath}");

$cachePath = $storagePath.'/bootstrap-cache';

foreach ([
    'APP_CONFIG_CACHE' => $cachePath.'/config.php',
    'APP_EVENTS_CACHE' => $cachePath.'/events.php',
    'APP_PACKAGES_CACHE' => $cachePath.'/packages.php',
    'APP_ROUTES_CACHE' => $cachePath.'/routes.php',
    'APP_SERVICES_CACHE' => $cachePath.'/services.php',
] as $key => $value) {
    if (! isset($_ENV[$key]) && ! isset($_SERVER[$key]) && getenv($key) === false) {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv("{$key}={$value}");
    }
}

foreach ([
    $storagePath,
    $cachePath,
    $storagePath.'/framework',
    $storagePath.'/framework/cache',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/testing',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}

require __DIR__.'/../public/index.php';
