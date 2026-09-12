#!/usr/bin/env bash
# Installation initiale sur Ubuntu 24.04 (VPS)
# Usage (sur le VPS, en root ou sudo) :
#   curl -fsSL ... | bash
#   ou : bash deploy/install-vps.sh

set -euo pipefail

APP_DIR="${APP_DIR:-/opt/technova}"
REPO_URL="${REPO_URL:-}"

echo "==> Mise à jour système"
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get upgrade -y

echo "==> Paquets de base"
apt-get install -y ca-certificates curl git ufw

echo "==> Docker (official repo)"
if ! command -v docker >/dev/null 2>&1; then
  install -m 0755 -d /etc/apt/keyrings
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
  chmod a+r /etc/apt/keyrings/docker.asc
  echo \
    "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
    $(. /etc/os-release && echo "${VERSION_CODENAME}") stable" \
    | tee /etc/apt/sources.list.d/docker.list > /dev/null
  apt-get update -y
  apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
fi

echo "==> Pare-feu UFW"
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

echo "==> Répertoire application : ${APP_DIR}"
mkdir -p "$(dirname "${APP_DIR}")"

if [ -n "${REPO_URL}" ] && [ ! -d "${APP_DIR}/.git" ]; then
  git clone "${REPO_URL}" "${APP_DIR}"
elif [ ! -d "${APP_DIR}/.git" ]; then
  echo "⚠️  Clone manuel requis : git clone <votre-repo> ${APP_DIR}"
fi

cd "${APP_DIR}" 2>/dev/null || { echo "Créez ${APP_DIR} avec le code du projet."; exit 0; }

if [ ! -f .env.production ]; then
  cp .env.production.example .env.production
  echo ""
  echo "⚠️  Éditez ${APP_DIR}/.env.production (secrets, Stripe, email ACME)"
  echo "    nano ${APP_DIR}/.env.production"
  echo ""
fi

echo "==> Installation terminée."
echo "Prochaine étape :"
echo "  cd ${APP_DIR} && bash deploy/deploy.sh"
