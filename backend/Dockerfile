# Backend Symfony — PHP 8.2 FPM
FROM composer:2 AS composer

# bullseye = compatible VPS sans x86-64-v2 (comme MySQL 8.0.32)
FROM php:8.2-fpm-bullseye AS app

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libicu-dev \
        libpng-dev \
        libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        intl \
        pdo \
        pdo_mysql \
        zip \
        opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock symfony.lock ./
COPY bin bin
COPY config config
COPY migrations migrations
COPY public public
COPY src src
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# Fix CRLF -> LF (fichiers créés/édités sous Windows) + rendre exécutable
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh \
    && COMPOSER_ALLOW_SUPERUSER=1 composer install \
        --no-dev \
        --no-scripts \
        --no-interaction \
        --optimize-autoloader \
        --prefer-dist \
    && composer dump-autoload --optimize --classmap-authoritative \
    && mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var \
    && chmod -R 775 var

# Note JWT : ce projet utilise firebase/php-jwt + JWT_SECRET (pas Lexik).
# Aucun dossier config/jwt/ à générer.

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
