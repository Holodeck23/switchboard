<?php

// Vercel serverless entry point for Laravel.
// The Lambda filesystem is read-only except /tmp, so redirect every path
// Laravel needs to write (bootstrap cache, compiled views) into /tmp before boot.
foreach ([
    'APP_CONFIG_CACHE'   => '/tmp/config.php',
    'APP_EVENTS_CACHE'   => '/tmp/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_ROUTES_CACHE'   => '/tmp/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'VIEW_COMPILED_PATH' => '/tmp/views',
] as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $_SERVER[$key] = $value;
}

if (! is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0755, true);
}

// Serve existing static files (Filament assets, etc.) directly.
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require __DIR__.'/public/index.php';
