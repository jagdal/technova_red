#!/usr/bin/env bash
# Déploiement / mise à jour production
# Usage (sur le VPS) :
#   cd /opt/technova && bash deploy/deploy.sh

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

ENV_FILE="${ENV_FILE:-.env.production}"
COMPOSE="docker compose -f docker-compose.prod.yml --env-file ${ENV_FILE}"

if [ ! -f "${ENV_FILE}" ]; then
  echo "❌ ${ENV_FILE} introuvable. Copiez .env.production.example → .env.production"
  exit 1
fi

# shellcheck disable=SC1090
set -a
source "${ENV_FILE}"
set +a

for var in MYSQL_ROOT_PASSWORD MYSQL_PASSWORD APP_SECRET JWT_SECRET ACME_EMAIL; do
  if [ -z "${!var:-}" ] || [[ "${!var}" == CHANGE_ME* ]]; then
    echo "❌ Variable ${var} non configurée dans ${ENV_FILE}"
    exit 1
  fi
done

echo "==> Domaine : ${DOMAIN:-technovax.store}"

if [ -d .git ]; then
  echo "==> git pull"
  git pull --ff-only || true
fi

echo "==> Build & démarrage containers"
${COMPOSE} build --pull
${COMPOSE} up -d

echo "==> État des services"
${COMPOSE} ps

echo ""
echo "✅ Déploiement lancé."
echo "   Site : https://${DOMAIN:-technovax.store}"
echo "   API  : https://${DOMAIN:-technovax.store}/api"
echo ""
echo "Première installation — données démo (optionnel) :"
echo "  ${COMPOSE} exec php php bin/console app:load-demo-data"
echo ""
echo "Logs :"
echo "  ${COMPOSE} logs -f caddy"
echo "  ${COMPOSE} logs -f php"
