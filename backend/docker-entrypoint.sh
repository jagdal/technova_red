#!/bin/sh
set -e

cd /var/www/html

# Symfony exige un fichier .env (secrets réels = variables Docker)
if [ ! -f .env ]; then
  cp .env.example .env 2>/dev/null || echo "APP_ENV=prod" > .env
fi

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
  if ! php bin/console cache:clear --env=prod --no-warmup; then
    echo "[entrypoint] ERREUR cache:clear — voir logs ci-dessus"
    exit 1
  fi
  if ! php bin/console cache:warmup --env=prod; then
    echo "[entrypoint] ERREUR cache:warmup — voir logs ci-dessus"
    exit 1
  fi
  chown -R www-data:www-data var 2>/dev/null || true
fi

exec docker-php-entrypoint php-fpm
