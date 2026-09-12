#!/bin/sh
set -e

cd /var/www/html

# Si le bind-mount écrase vendor/, on réinstalle
if [ ! -f vendor/autoload.php ]; then
  echo "[entrypoint] vendor/ manquant — composer install..."
  composer install --no-dev --no-interaction --optimize-autoloader --prefer-dist
fi

mkdir -p var/cache var/log var/sessions
chown -R www-data:www-data var 2>/dev/null || true
chmod -R 775 var 2>/dev/null || true

if [ "$APP_ENV" = "prod" ]; then
  if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
    echo "[entrypoint] doctrine:migrations:migrate..."
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true
  fi
  echo "[entrypoint] cache:warmup..."
  php bin/console cache:clear --env=prod --no-warmup
  php bin/console cache:warmup --env=prod
fi

exec docker-php-entrypoint php-fpm
