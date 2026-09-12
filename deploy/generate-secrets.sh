#!/usr/bin/env bash
# Génère .env.production avec secrets aléatoires (à lancer sur le VPS)
# Préférez : bash deploy/setup-production.sh (tout-en-un)
#
# Usage : bash deploy/generate-secrets.sh [email]
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT="${ROOT}/.env.production"
ACME_EMAIL="${1:-admin@technovax.store}"

rand() { openssl rand -hex 24; }

cat > "${OUT}" <<EOF
DOMAIN=technovax.store
ACME_EMAIL=${ACME_EMAIL}

MYSQL_DATABASE=technova
MYSQL_ROOT_PASSWORD=$(rand)
MYSQL_USER=symfony
MYSQL_PASSWORD=$(rand)

APP_SECRET=$(rand)
JWT_SECRET=$(rand)
DEFAULT_URI=https://technovax.store
FRONTEND_URL=https://technovax.store
CORS_ALLOW_ORIGIN='^https://(www\.)?technovax\.store$'

STRIPE_SECRET_KEY=
STRIPE_PUBLIC_KEY=
STRIPE_WEBHOOK_SECRET=
EOF

chmod 600 "${OUT}"
echo "✅ ${OUT} créé. Éditez Stripe + ACME_EMAIL si besoin."
