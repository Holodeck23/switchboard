<?php

// Vercel serverless entry point — forwards to Laravel's public/index.php.
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing static files directly (Filament assets, etc.).
if ($uri !== '/' && file_exists(__DIR__.'/../public'.$uri)) {
    return false;
}

require __DIR__.'/../public/index.php';
