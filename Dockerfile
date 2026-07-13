# Switchboard — single-container demo deploy (PHP + baked, seeded SQLite)
FROM php:8.4-cli-alpine

RUN apk add --no-cache libzip-dev sqlite-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_sqlite intl bcmath zip pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && cp .env.example .env \
    && php artisan key:generate \
    && touch database/database.sqlite \
    && php artisan migrate --force --seed \
    && php artisan config:cache \
    && php artisan route:cache

ENV APP_ENV=production
EXPOSE 8080
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=8080"]
