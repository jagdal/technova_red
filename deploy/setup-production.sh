#!/usr/bin/env bash
# Configuration production en UNE commande (sur le VPS)
#
# Usage :
#   bash deploy/setup-production.sh
#   bash deploy/setup-production.sh estjagdal@gmail.com
#
# Le script :
#   1. Génère .env.production (mots de passe aléatoires)
#   2. Demande les clés Stripe (copiées depuis backend/.env local)
#   3. Lance le déploiement Docker

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

OUT="${ROOT}/.env.production"

echo "=========================================="
echo "  TechNova — Setup production"
echo "  Domaine : technovax.store"
echo "=========================================="
echo ""

# --- Email HTTPS ---
ACME_EMAIL="${1:-}"
if [ -z "${ACME_EMAIL}" ]; then
  read -r -p "Email Let's Encrypt (ex: estjagdal@gmail.com) : " ACME_EMAIL
fi
if [ -z "${ACME_EMAIL}" ]; then
  echo "❌ Email obligatoire pour le certificat HTTPS."
  exit 1
fi

# --- Clés Stripe ---
echo ""
echo "Copie les clés depuis ton backend/.env local (sur ton PC)."
echo "Webhook : laisse vide pour l'instant (après HTTPS)."
echo ""

read -r -p "STRIPE_SECRET_KEY (sk_test_...) : " STRIPE_SECRET_KEY
read -r -p "STRIPE_PUBLIC_KEY (pk_test_...) : " STRIPE_PUBLIC_KEY
read -r -p "STRIPE_WEBHOOK_SECRET (Entrée = vide) : " STRIPE_WEBHOOK_SECRET

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

STRIPE_SECRET_KEY=${STRIPE_SECRET_KEY}
STRIPE_PUBLIC_KEY=${STRIPE_PUBLIC_KEY}
STRIPE_WEBHOOK_SECRET=${STRIPE_WEBHOOK_SECRET}
EOF

chmod 600 "${OUT}"

echo ""
echo "✅ .env.production créé (secrets générés automatiquement)."
echo ""
read -r -p "Lancer le déploiement maintenant ? (o/N) : " CONFIRM
if [[ "${CONFIRM}" =~ ^[oOyY]$ ]]; then
  bash deploy/deploy.sh
else
  echo "OK. Lance plus tard : bash deploy/deploy.sh"
fi
