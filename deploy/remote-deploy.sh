#!/usr/bin/env bash
# Déploiement depuis votre machine vers le VPS
# Usage :
#   export VPS_HOST=root@IP_DU_VPS
#   export APP_DIR=/opt/technova
#   bash deploy/remote-deploy.sh

set -euo pipefail

VPS_HOST="${VPS_HOST:?Définissez VPS_HOST=root@IP_DU_VPS}"
APP_DIR="${APP_DIR:-/opt/technova}"
REPO_URL="${REPO_URL:-}"

echo "==> Connexion à ${VPS_HOST}"

ssh -o StrictHostKeyChecking=accept-new "${VPS_HOST}" bash -s <<EOF
set -euo pipefail
if [ ! -d "${APP_DIR}/.git" ]; then
  if [ -z "${REPO_URL}" ]; then
    echo "Clone manuel requis dans ${APP_DIR}"
    exit 1
  fi
  git clone "${REPO_URL}" "${APP_DIR}"
fi
cd "${APP_DIR}"
if [ ! -f .env.production ]; then
  cp .env.production.example .env.production
  echo "⚠️  Configurez .env.production sur le VPS puis relancez."
  exit 1
fi
bash deploy/deploy.sh
EOF

echo "✅ Déploiement distant terminé."
