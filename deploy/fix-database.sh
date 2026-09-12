#!/usr/bin/env bash
# Répare MySQL unhealthy / Restarting (127) sur VPS 2 Go RAM
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

ENV_FILE="${ENV_FILE:-.env.production}"
COMPOSE="docker compose -f docker-compose.prod.yml --env-file ${ENV_FILE}"

echo "==> Arrêt stack + suppression volume MySQL corrompu"
${COMPOSE} down -v 2>/dev/null || true
docker volume rm technova_db_data 2>/dev/null || true

echo "==> Nettoyage fin de ligne Windows (.env.production)"
sed -i 's/\r$//' "${ENV_FILE}" 2>/dev/null || true

echo "==> Pull image MySQL (8.0.32 — compatible CPU sans x86-64-v2)"
docker pull mysql:8.0.32

echo "==> Démarrage MySQL seul (attendre init ~90s)"
${COMPOSE} up -d database

echo "==> Attente healthcheck..."
for i in $(seq 1 24); do
  STATUS=$(docker inspect technova_database --format='{{.State.Health.Status}}' 2>/dev/null || echo "unknown")
  echo "  [$i/24] database: ${STATUS}"
  if [ "${STATUS}" = "healthy" ]; then
    echo "✅ MySQL OK"
    break
  fi
  if [ "${STATUS}" = "unhealthy" ] && [ "$i" -ge 12 ]; then
    echo "❌ MySQL unhealthy — logs :"
    docker logs technova_database --tail 40
    exit 1
  fi
  sleep 10
done

echo "==> Démarrage stack complet"
${COMPOSE} up -d --build

echo "==> État final"
${COMPOSE} ps
