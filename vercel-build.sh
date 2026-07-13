#!/usr/bin/env bash
set -e
composer install --no-dev --optimize-autoloader --no-interaction
php artisan filament:assets
php artisan config:cache
php artisan route:cache
php artisan view:cache
