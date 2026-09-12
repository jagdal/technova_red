# TechNova — Plateforme e-commerce Apple

Frontend **React** + Backend **Symfony** connectés.

## Démarrage rapide (projet complet)

**Terminal 1 — Backend :**
```bash
cd backend
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:load-demo-data
php -S 127.0.0.1:8000 -t public public/router.php
# ou : composer serve
# ou : symfony serve --port=8000 --no-tls
```

**Terminal 2 — Frontend :**
```bash
npm install
npm run dev
```

→ Ouvrir **http://localhost:5173** (le proxy Vite envoie `/api` vers Symfony)

**Comptes démo :**
- Admin : `admin@technova.com` / `Admin1234!`
- Client : s'inscrire via le bouton « Inscription »

---

# Backend API (Symfony)

Plateforme e-commerce dédiée à la vente de produits Apple (MacBook, iPhone, iPad).  
Ce backend expose une **API REST** consommée par le frontend React.

## Stack

- **PHP 8.2+** / **Symfony 7.4**
- **MySQL 8** / **Doctrine ORM**
- **API Platform** (CRUD Produit / Catégorie)
- **JWT** (authentification API)

## Prérequis

- PHP 8.2+ avec extensions : `pdo_mysql`, `mbstring`, `openssl`
- Composer
- MySQL 8 (local ou via Docker)

## Installation

```bash
cd backend
composer install
cp .env .env.local   # adapter DATABASE_URL et secrets
```

### Base de données (Docker)

```bash
docker compose up -d
php bin/console doctrine:migrations:migrate --no-interaction
```

### Lancer le serveur

```bash
symfony server:start
# ou
php -S 127.0.0.1:8000 -t public public/router.php
# ou : composer serve
# ou : symfony serve --port=8000 --no-tls
```

L'API est disponible sur `http://localhost:8000/api`.

## Variables d'environnement

| Variable | Description |
|----------|-------------|
| `DATABASE_URL` | Connexion MySQL |
| `APP_SECRET` | Secret Symfony |
| `JWT_SECRET` | Clé de signature JWT |
| `CORS_ALLOW_ORIGIN` | Origines autorisées (frontend React) |

## Architecture

```
src/
├── Controller/    → Contrôleurs API fins (auth, panier, commande, paiement)
├── Entity/        → Entités Doctrine + ApiResource (Produit, Categorie)
├── Repository/    → Requêtes personnalisées
├── Service/       → Logique métier
├── Security/      → JWT, Voters
└── DTO/           → Validation des entrées API
```

## Endpoints principaux

| Méthode | Route | Description | Rôle |
|---------|-------|-------------|------|
| GET | `/api/auth/csrf` | Tokens CSRF | Public |
| POST | `/api/auth/register` | Inscription client | Public |
| POST | `/api/auth/login` | Connexion | Public |
| GET | `/api/catalogue/produits` | Catalogue (filtres, pagination) | Public |
| GET/POST | `/api/panier` | Gestion panier | CLIENT |
| POST | `/api/commandes/checkout` | Valider commande | CLIENT |
| POST | `/api/paiements` | Paiement | CLIENT |
| GET | `/api/admin/dashboard` | Tableau de bord | ADMIN |
| CRUD | `/api/produits`, `/api/categories` | API Platform | Lecture publique / écriture ADMIN |

## Tests

```bash
php bin/phpunit
```

- **Unit** : `CartService::calculateTotal()`, validation email inscription
- **Fonctionnel** : ajout panier → validation commande

## Sécurité

- Mots de passe hachés via `UserPasswordHasherInterface`
- Requêtes Doctrine (pas de SQL concaténé)
- CSRF sur inscription / connexion
- Contrôle d'accès `ROLE_CLIENT` / `ROLE_ADMIN` + Voters Symfony
- Validation serveur sur tous les DTO

## Branches Git

- `main` : production
- `dev` : développement

La CI GitHub Actions exécute PHPUnit avant tout merge.

---

## Déploiement production (VPS Ubuntu 24.04 — technovax.store)

Stack : **Docker Compose** + **Caddy** (HTTPS Let's Encrypt) + **Nginx** (React + proxy `/api`).

### Prérequis DNS

Enregistrements chez votre registrar :

| Type | Nom | Valeur |
|------|-----|--------|
| A | `@` | IP du VPS |
| A | `www` | IP du VPS |

### 1. Installation initiale sur le VPS

```bash
# Se connecter au VPS
ssh root@VOTRE_IP

# Cloner le projet
git clone https://github.com/VOTRE_USER/technova_red.git /opt/technova
cd /opt/technova

# Installer Docker + UFW (ports 22, 80, 443)
sudo bash deploy/install-vps.sh

# Configurer les secrets production
cp .env.production.example .env.production
nano .env.production   # APP_SECRET, JWT_SECRET, MySQL, Stripe, ACME_EMAIL

# Déployer
bash deploy/deploy.sh
```

### 2. Données initiales (première fois)

```bash
docker compose -f docker-compose.prod.yml --env-file .env.production \
  exec php php bin/console app:load-demo-data
```

### 3. Mises à jour

```bash
cd /opt/technova && bash deploy/deploy.sh
```

### Architecture production

```
Internet :443
    ↓
Caddy (HTTPS technovax.store)
    ↓
frontend (Nginx — React SPA + proxy /api)
    ↓
nginx-symfony → php-fpm (Symfony)
    ↓
MySQL (réseau interne, port non exposé)
```

### Fichiers production

| Fichier | Rôle |
|---------|------|
| `docker-compose.prod.yml` | Stack production |
| `docker/Caddyfile` | HTTPS automatique |
| `.env.production` | Secrets (non versionné) |
| `deploy/deploy.sh` | Build + démarrage |

### Stripe en production

- `FRONTEND_URL=https://technovax.store`
- Webhook Stripe : `https://technovax.store/api/paiements/stripe/webhook`

### Dev local (inchangé)

Le fichier `docker-compose.yml` à la racine reste pour le développement local (`localhost:80`, `:8080`).
