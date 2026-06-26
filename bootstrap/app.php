<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$storagePath = $_ENV['APP_STORAGE_PATH'] ?? $_SERVER['APP_STORAGE_PATH'] ?? getenv('APP_STORAGE_PATH') ?: null;
$environment = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? getenv('APP_ENV') ?: null;

if (! $storagePath && $environment === 'testing') {
    $storagePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'laundrylink-testing-storage';
}

if ($storagePath) {
    $_ENV['APP_STORAGE_PATH'] = $storagePath;
    $_SERVER['APP_STORAGE_PATH'] = $storagePath;
    putenv("APP_STORAGE_PATH={$storagePath}");

    $cachePath = $storagePath.DIRECTORY_SEPARATOR.'bootstrap-cache';

    foreach ([
        'APP_CONFIG_CACHE' => $cachePath.DIRECTORY_SEPARATOR.'config.php',
        'APP_EVENTS_CACHE' => $cachePath.DIRECTORY_SEPARATOR.'events.php',
        'APP_PACKAGES_CACHE' => $cachePath.DIRECTORY_SEPARATOR.'packages.php',
        'APP_ROUTES_CACHE' => $cachePath.DIRECTORY_SEPARATOR.'routes.php',
        'APP_SERVICES_CACHE' => $cachePath.DIRECTORY_SEPARATOR.'services.php',
    ] as $key => $value) {
        if (! isset($_ENV[$key]) && ! isset($_SERVER[$key]) && getenv($key) === false) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

if ($storagePath) {
    if (preg_match('/^[A-Za-z]:[\/\\\\]/', $storagePath) === 1) {
        $drivePrefix = substr($storagePath, 0, 3);

        $app->addAbsoluteCachePathPrefix($drivePrefix);
        $app->addAbsoluteCachePathPrefix(str_replace('/', '\\', $drivePrefix));
        $app->addAbsoluteCachePathPrefix(str_replace('\\', '/', $drivePrefix));
    }

    $cachePath = $storagePath.DIRECTORY_SEPARATOR.'bootstrap-cache';

    foreach ([
        $storagePath,
        $cachePath,
        $storagePath.DIRECTORY_SEPARATOR.'framework',
        $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache',
        $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'data',
        $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'sessions',
        $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'testing',
        $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'views',
        $storagePath.DIRECTORY_SEPARATOR.'logs',
    ] as $directory) {
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    $app->useStoragePath($storagePath);
}

return $app;
