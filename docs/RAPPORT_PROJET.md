# Rapport complet — Projet TechNova (PFE CDA)

**Plateforme e-commerce de produits Apple**  
Stack : Symfony 7 (backend) + React (frontend) + MySQL  
Public : débutant React / Symfony  

---

## Table des matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Comment ça marche (schéma simple)](#2-comment-ça-marche)
3. [Architecture MVC](#3-architecture-mvc)
4. [Backend — dossier par dossier](#4-backend--dossier-par-dossier)
5. [Dossier Frontend](#5-dossier-frontend)
6. [Pages — une par une](#6-pages--une-par-une)
7. [Base de données](#7-base-de-données)
8. [Sécurité](#8-sécurité)
9. [API — liste des routes](#9-api--liste-des-routes)
10. [Démarrer le projet](#10-démarrer-le-projet)
11. [Glossaire](#11-glossaire)

---

## 1. Vue d'ensemble

**TechNova** est une boutique en ligne spécialisée dans les produits Apple (iPhone, iPad, MacBook).

Le projet est divisé en **deux applications** :

| Partie | Technologie | Rôle |
|--------|-------------|------|
| **Frontend** | React + Vite | Interface utilisateur dans le navigateur |
| **Backend** | Symfony (PHP) | API REST + logique métier + base de données |
| **BDD** | MySQL (`technova`) | Stockage des données |

Le frontend **ne parle jamais directement à MySQL**. Il envoie des requêtes HTTP au backend, qui lit/écrit en base.

---

## 2. Comment ça marche

```
┌─────────────────────────────────────────────────────────┐
│  NAVIGATEUR — http://localhost:5173                      │
│  React affiche les pages (produits, panier, admin…)      │
└────────────────────────┬────────────────────────────────┘
                         │ fetch("/api/...")
                         │ (proxy Vite → port 8000)
┌────────────────────────▼────────────────────────────────┐
│  SYMFONY — http://localhost:8000/api                     │
│  Controllers → Services → Repositories → MySQL           │
└────────────────────────┬────────────────────────────────┘
                         │ SQL
┌────────────────────────▼────────────────────────────────┐
│  MySQL — base `technova`                                 │
│  Tables : client, produit, commande, message_contact…    │
└─────────────────────────────────────────────────────────┘
```

**Exemple concret — ajouter au panier :**
1. L'utilisateur clique « Ajouter au panier » sur un produit
2. React appelle `POST /api/panier/items` avec l'ID produit
3. Symfony (`CartController` → `CartService`) enregistre en session
4. React recharge le panier et met à jour le badge

---

## 3. Architecture MVC

### MVC = Modèle · Vue · Contrôleur

| Couche | Rôle | Analogie |
|--------|------|----------|
| **Modèle** | Données + règles métier | Le « cerveau » et la mémoire |
| **Vue** | Affichage | Ce que l'utilisateur voit |
| **Contrôleur** | Relie Vue et Modèle | Le chef d'orchestre |

### Backend Symfony (MVC)

```
Requête HTTP
    → Controller     (reçoit, renvoie JSON)
    → DTO              (valide les données reçues)
    → Service          (logique métier)
    → Repository       (requêtes SQL)
    → Entity           (représente une table)
```

**Fichier détaillé :** `backend/docs/ARCHITECTURE.md`

### Frontend React (équivalent MVC)

```
src/
├── services/api.js       → Modèle (accès API)
├── models/catalogModel.js → Modèle (format des données UI)
├── hooks/useCatalog.js   → Contrôleur réutilisable
├── context/              → Contrôleur global (auth, panier)
├── views/                → Vue (pages)
└── components/           → Vue (morceaux réutilisables)
```

**Fichier détaillé :** `src/docs/ARCHITECTURE.md`

---

## 4. Backend — dossier par dossier

### `backend/` — racine

| Fichier/Dossier | Explication |
|-----------------|-------------|
| `public/index.php` | Porte d'entrée : chaque requête HTTP passe ici |
| `bin/console` | Commandes Symfony en terminal |
| `composer.json` | Dépendances PHP (Symfony, Doctrine…) |
| `.env` | Configuration locale (BDD, secrets) — **ne pas committer** |
| `docker-compose.yml` | MySQL en Docker (optionnel) |
| `migrations/` | Scripts qui créent/modifient les tables |
| `tests/` | Tests automatiques PHPUnit |
| `docs/ARCHITECTURE.md` | Documentation architecture backend |

### `backend/config/`

Configuration de Symfony :

| Fichier | Rôle |
|---------|------|
| `packages/security.yaml` | Qui peut accéder à quoi (client, admin, public) |
| `packages/doctrine.yaml` | Connexion MySQL |
| `packages/nelmio_cors.yaml` | Autorise React à appeler l'API |
| `packages/api_platform.yaml` | Config API auto pour Produits/Catégories |
| `services.yaml` | Injection des services (JWT, CSRF…) |

### `backend/src/Entity/` — Les tables en PHP

Chaque fichier = une table MySQL.

| Fichier | Table | Description |
|---------|-------|-------------|
| `Client.php` | `client` | Utilisateur qui achète (`ROLE_CLIENT`) |
| `Admin.php` | `admin` | Administrateur (`ROLE_ADMIN`) |
| `Categorie.php` | `categorie` | iPhone, iPad, MacBook |
| `Produit.php` | `produit` | Articles du catalogue |
| `Commande.php` | `commande` | Commande passée |
| `Contenir.php` | `contenir` | Ligne commande (produit + quantité + prix) |
| `Paiement.php` | `paiement` | Paiement lié à une commande (1:1) |
| `MessageContact.php` | `message_contact` | Messages du formulaire contact |

### `backend/src/Repository/`

Accès base de données. Un repository par entité.  
Exemple : `ProduitRepository::searchWithFilters()` fait la recherche avec filtres prix/catégorie.

### `backend/src/Service/` — Logique métier

| Service | Rôle |
|---------|------|
| `AuthService` | Inscription, connexion, token JWT |
| `CartService` | Panier (stocké en session serveur) |
| `OrderService` | Création commande (checkout) avec transaction |
| `PaymentService` | Enregistrement paiement |
| `ProductSearchService` | Catalogue public, recherche, détail par ID |
| `ContactService` | Messages contact |
| `DashboardService` | Statistiques admin |
| `AdminUserService` | Gestion utilisateurs admin |
| `SlugService` | URLs SEO (slugs produits) |

### `backend/src/Controller/` — URLs de l'API

| Contrôleur | Préfixe URL | Rôle |
|------------|-------------|------|
| `AuthController` | `/api/auth` | Login, register, CSRF, logout |
| `CatalogueController` | `/api/catalogue/produits` | Catalogue public |
| `CartController` | `/api/panier` | Panier CRUD |
| `CommandeController` | `/api/commandes` | Checkout, liste commandes |
| `PaiementController` | `/api/paiements` | Paiement |
| `ContactController` | `/api/contact` | Formulaire contact |
| `DashboardController` | `/api/admin/dashboard` | Stats admin |
| `AdminUserController` | `/api/admin/utilisateurs` | Liste/suppression clients |
| `AdminMessageController` | `/api/admin/messages` | Boîte messages |
| `SitemapController` | `/api/sitemap.xml` | SEO |

**+ API Platform** génère automatiquement le CRUD admin sur `/api/produits` et `/api/categories`.

### `backend/src/DTO/`

Objets qui reçoivent le JSON du frontend et le valident :

- `LoginDto` — email, mot de passe, token CSRF
- `RegisterClientDto` — inscription client
- `CartItemDto` — ajout panier
- `PaymentDto` — paiement
- `ContactMessageDto` — message contact
- `OrderStatusUpdateDto` — changement statut commande (admin)

### `backend/src/Security/`

| Fichier | Rôle |
|---------|------|
| `JwtAuthenticator.php` | Lit le header `Authorization: Bearer …` |
| `TokenService.php` | Crée et vérifie les JWT |
| `StatelessCsrfService.php` | Tokens CSRF pour login/register |
| `Voter/ProduitVoter.php` | Qui peut créer/modifier/supprimer un produit |
| `Voter/CategorieVoter.php` | Idem pour catégories |
| `Voter/CommandeVoter.php` | Client voit ses commandes, admin voit tout |

### `backend/src/Enum/`

`CommandeStatut.php` — statuts possibles : `en_attente`, `confirmee`, `expediee`, `livree`, `annulee`.

---

## 5. Dossier Frontend

Le frontend est l'application **React** que l'utilisateur voit dans son navigateur.  
Tout le code interface se trouve dans le dossier `src/` à la racine du projet.

### Arborescence complète

```
technova_red/                    ← racine du projet frontend
├── index.html                   ← page HTML de base (point d'entrée navigateur)
├── package.json                 ← dépendances npm (React, Vite, Lucide…)
├── vite.config.js               ← config Vite + proxy /api → Symfony
├── eslint.config.js             ← règles de qualité du code JS
├── dist/                        ← build de production (généré par npm run build)
│
└── src/                         ← CODE SOURCE REACT
    ├── main.jsx                 ← démarre React
    ├── App.jsx                  ← routes + providers
    ├── index.css                ← styles globaux (couleurs, boutons)
    │
    ├── context/                 ← ÉTAT GLOBAL (couche Contrôleur)
    │   ├── AuthContext.jsx      ← connexion, JWT, isAdmin
    │   └── CartContext.jsx      ← panier synchronisé API
    │
    ├── hooks/                   ← LOGIQUE RÉUTILISABLE
    │   └── useCatalog.js        ← charge produits + catégories
    │
    ├── models/                  ← MODÈLE (format des données)
    │   └── catalogModel.js      ← API → format UI (nom, prix, image)
    │
    ├── services/                ← APPELS API
    │   └── api.js               ← toutes les requêtes HTTP
    │
    ├── components/              ← COMPOSANTS RÉUTILISABLES (Vue)
    │   ├── Navbar.jsx + Navbar.css
    │   ├── Footer.jsx + Footer.css
    │   ├── ProductCard.jsx + ProductCard.css
    │   ├── CategoryCard.jsx + CategoryCard.css
    │   ├── AdminLayout.jsx + AdminLayout.css
    │   ├── AdminRoute.jsx
    │   ├── AppIcon.jsx + AppIcon.css
    │
    ├── views/                   ← PAGES (Vue)
    │   ├── public/              ← boutique (clients / visiteurs)
    │   │   ├── Home.jsx + Home.css
    │   │   ├── Products.jsx + Products.css
    │   │   ├── ProductDetails.jsx + ProductDetails.css
    │   │   ├── Cart.jsx + Cart.css
    │   │   ├── Checkout.jsx + Checkout.css
    │   │   ├── Login.jsx + Login.css
    │   │   ├── Signup.jsx + Signup.css
    │   │   ├── Contact.jsx + Contact.css
    │   │   └── Notifications.jsx + Notifications.css
    │   │
    │   └── admin/               ← administration
    │       ├── AdminDashboard.jsx
    │       ├── ProductsAdmin.jsx
    │       ├── UsersAdmin.jsx
    │       └── MessagesAdmin.jsx
    │
    └── docs/
        └── ARCHITECTURE.md      ← doc architecture MVC frontend
```

---

### Fichiers à la racine du projet (hors `src/`)

| Fichier | Rôle |
|---------|------|
| `index.html` | Page HTML vide avec `<div id="root">` — React s'injecte dedans |
| `package.json` | Liste des packages : `react`, `react-router-dom`, `lucide-react`, `vite` |
| `vite.config.js` | Serveur dev port **5173** ; proxy `/api` → `http://localhost:8000` |
| `eslint.config.js` | Vérification automatique du style de code JavaScript |
| `dist/` | Fichiers compilés pour la mise en production (`npm run build`) |

---

### `src/main.jsx` et `src/App.jsx`

| Fichier | Rôle détaillé |
|---------|---------------|
| `main.jsx` | Importe `index.css` et `App.jsx`, monte React dans `#root` |
| `App.jsx` | Enveloppe l'app avec `AuthProvider` + `CartProvider` + `BrowserRouter` ; définit toutes les **routes** (URLs → pages) ; masque Navbar/Footer sur `/admin` |

---

### `src/context/` — État global

| Fichier | Rôle détaillé |
|---------|---------------|
| `AuthContext.jsx` | Gère login, register, logout ; stocke `technova_token` et `technova_user` dans `localStorage` ; expose `isAuthenticated`, `isAdmin`, `user` |
| `CartContext.jsx` | Synchronise le panier avec `GET/POST/PATCH/DELETE /api/panier` ; expose `addToCart`, `removeFromCart`, `updateQuantity`, `cartCount` ; vide le panier si déconnecté |

---

### `src/hooks/` — Hooks React

| Fichier | Rôle détaillé |
|---------|---------------|
| `useCatalog.js` | `useCatalog(filters)` — charge et normalise produits + catégories ; `useProduct(id)` — charge une fiche produit par ID |

---

### `src/models/` — Modèle de données UI

| Fichier | Rôle détaillé |
|---------|---------------|
| `catalogModel.js` | `normalizeProduct()` — transforme `libelle_produit` → `name`, ajoute image ; `normalizeCategory()` — format catégories ; `normalizeCartItem()` — format lignes panier ; `CATEGORY_UI` — icônes iPhone/iPad/MacBook |

---

### `src/services/api.js` — Couche API

**Toutes** les communications avec Symfony passent par ce fichier.

| Groupe | Fonctions |
|--------|-----------|
| Auth | `fetchCsrfTokens`, `loginClient`, `registerClient`, `logoutClient` |
| Catalogue | `fetchProducts`, `fetchProductById`, `fetchCategories` |
| Panier | `fetchCart`, `addCartItem`, `updateCartItem`, `removeCartItem` |
| Commandes | `checkoutOrder`, `createPayment`, `fetchMyOrders` |
| Contact | `submitContactMessage` |
| Admin | `fetchDashboard`, `fetchAdminUsers`, `deleteAdminClient`, `fetchAdminMessages`, `markMessageRead`, `deleteAdminMessage`, CRUD produits API Platform |

---

### `src/components/` — Composants réutilisables

| Fichier `.jsx` | Fichier `.css` | Rôle |
|----------------|----------------|------|
| `Navbar.jsx` | `Navbar.css` | Logo, recherche, panier, connexion, catégories |
| `Footer.jsx` | `Footer.css` | Liens shop, support, réseaux sociaux |
| `ProductCard.jsx` | `ProductCard.css` | Carte produit + bouton « Ajouter au panier » |
| `CategoryCard.jsx` | `CategoryCard.css` | Carte catégorie sur l'accueil |
| `AdminLayout.jsx` | `AdminLayout.css` | Sidebar admin + zone contenu (`<Outlet />`) |
| `AdminRoute.jsx` | — | Redirige vers `/login` si pas admin |
| `AppIcon.jsx` | `AppIcon.css` | Icônes outline Lucide (panier, recherche, user…) |

---

### `src/views/public/` — Pages boutique

Chaque page a son fichier **`.jsx`** (logique + HTML) et **`.css`** (style).

| Fichier | Route URL | Rôle |
|---------|-----------|------|
| `Home.jsx` | `/` | Accueil : hero, catégories, produits vedettes |
| `Products.jsx` | `/products` | Catalogue avec filtres (`?category=`, `?q=`) |
| `ProductDetails.jsx` | `/products/:id` | Fiche produit détaillée |
| `Cart.jsx` | `/cart` | Panier : quantités, suppression, total |
| `Checkout.jsx` | `/checkout` | Validation commande + paiement |
| `Login.jsx` | `/login` | Formulaire connexion |
| `Signup.jsx` | `/signup` | Formulaire inscription client |
| `Contact.jsx` | `/contact` | Formulaire message → API contact |
| `Notifications.jsx` | `/notifications` | Notifications (données mock pour l'instant) |

---

### `src/views/admin/` — Pages administration

Styles partagés via classes `admin-*` dans `AdminLayout.css`.

| Fichier | Route URL | Rôle |
|---------|-----------|------|
| `AdminDashboard.jsx` | `/admin` | KPIs, CA, dernières commandes |
| `ProductsAdmin.jsx` | `/admin/products` | CRUD produits (modal création/édition) |
| `UsersAdmin.jsx` | `/admin/users` | Liste clients + admins, suppression client |
| `MessagesAdmin.jsx` | `/admin/messages` | Boîte messages contact reçus |

---

### Flux de données frontend (résumé)

```
Utilisateur clique
       ↓
views/ (page) ou components/
       ↓
context/ ou hooks/ (logique)
       ↓
services/api.js (HTTP)
       ↓
models/catalogModel.js (formatage)
       ↓
Affichage mis à jour
```

**Documentation complémentaire :** `src/docs/ARCHITECTURE.md`

---

## 6. Pages — une par une

### Pages publiques

#### `Home.jsx` — Accueil (`/`)

- Bannière défilante (hero)
- Grille des catégories (`CategoryCard`)
- Produits vedettes (8 produits)
- **API :** `fetchProducts`, `fetchCategories`

#### `Products.jsx` — Catalogue (`/products`)

- Liste tous les produits
- Filtres via URL : `?category=1`, `?q=iphone`, `?prix_min=`
- **API :** `fetchProducts`, `fetchCategories`

#### `ProductDetails.jsx` — Fiche produit (`/products/:id`)

- Image, description, prix, stock
- Bouton « Ajouter au panier »
- **API :** `GET /api/catalogue/produits/{id}`

#### `Cart.jsx` — Panier (`/cart`)

- Liste des articles, modifier quantité, supprimer
- Lien vers checkout
- **Nécessite :** être connecté
- **API :** via `CartContext` → `/api/panier`

#### `Checkout.jsx` — Paiement (`/checkout`)

- Récapitulatif commande
- Choix mode paiement
- **API :** `checkoutOrder`, `createPayment`

#### `Login.jsx` — Connexion (`/login`)

- Email + mot de passe
- Redirige admin vers `/admin`, client vers page d'origine
- **API :** CSRF + `POST /api/auth/login`

#### `Signup.jsx` — Inscription (`/signup`)

- Formulaire complet (nom, email, téléphone, adresse…)
- **API :** CSRF + `POST /api/auth/register`

#### `Contact.jsx` — Contact (`/contact`)

- Formulaire nom, email, sujet, message
- **API :** `POST /api/contact` → table `message_contact`

#### `Notifications.jsx` — Notifications (`/notifications`)

- ⚠️ Données **fictives** (pas encore branché au backend)

---

### Pages admin (connexion admin requise)

#### `AdminDashboard.jsx` — `/admin`

- KPIs : commandes, produits, clients, CA, messages non lus
- Dernières commandes
- **API :** `GET /api/admin/dashboard`

#### `ProductsAdmin.jsx` — `/admin/products`

- Tableau des produits
- Créer / modifier / supprimer (modal)
- **API :** API Platform `/api/produits`

#### `UsersAdmin.jsx` — `/admin/users`

- Onglets Clients / Administrateurs
- Suppression client (si pas de commandes)
- **API :** `GET /api/admin/utilisateurs`, `DELETE …/clients/{id}`

#### `MessagesAdmin.jsx` — `/admin/messages`

- Liste messages contact
- Marquer lu, supprimer, voir contenu
- **API :** `GET /api/admin/messages`

---

## 7. Base de données

**Nom de la base :** `technova` (XAMPP MySQL)

### Tables principales

```
categorie ──< produit
client ──< commande ──< contenir >── produit
              │
              └── paiement (1 commande = 1 paiement)
admin (séparé)
message_contact (séparé)
```

### Compte admin de démo

- Email : `admin@technova.com`
- Mot de passe : `Admin1234!`

### Charger les données de démo

```bash
cd backend
php bin/console app:load-demo-data
```

---

## 8. Sécurité

| Mécanisme | Utilisation |
|-----------|-------------|
| **JWT** | Token dans `localStorage` — prouve l'identité |
| **CSRF stateless** | Protège login et inscription |
| **Rôles Symfony** | `ROLE_CLIENT`, `ROLE_ADMIN` |
| **Voters** | Règles fines par ressource |
| **Session PHP** | Panier côté serveur (cookie) |
| **CORS** | Autorise `localhost:5173` |

### Qui peut faire quoi ?

| Action | Visiteur | Client | Admin |
|--------|----------|--------|-------|
| Voir catalogue | ✅ | ✅ | ✅ |
| S'inscrire / se connecter | ✅ | — | — |
| Panier / commande | ❌ | ✅ | ❌ |
| Zone `/admin` | ❌ | ❌ | ✅ |
| CRUD produits | ❌ | ❌ | ✅ |

---

## 9. API — liste des routes

### Public

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/api/auth/csrf` | Tokens CSRF |
| POST | `/api/auth/login` | Connexion |
| POST | `/api/auth/register` | Inscription |
| GET | `/api/catalogue/produits` | Liste produits |
| GET | `/api/catalogue/produits/{id}` | Détail produit |
| GET | `/api/categories` | Catégories |
| POST | `/api/contact` | Envoyer message |

### Client connecté

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/api/panier` | Voir panier |
| POST | `/api/panier/items` | Ajouter au panier |
| PATCH | `/api/panier/items/{id}` | Modifier quantité |
| DELETE | `/api/panier/items/{id}` | Retirer du panier |
| POST | `/api/commandes/checkout` | Passer commande |
| GET | `/api/commandes` | Mes commandes |
| POST | `/api/paiements` | Payer |

### Admin

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/api/admin/dashboard` | Tableau de bord |
| GET/POST/PUT/DELETE | `/api/produits` | CRUD produits |
| GET | `/api/admin/utilisateurs` | Liste utilisateurs |
| DELETE | `/api/admin/utilisateurs/clients/{id}` | Supprimer client |
| GET | `/api/admin/messages` | Messages contact |

---

## 10. Démarrer le projet

### Prérequis

- PHP 8.2+, Composer
- Node.js 18+, npm
- MySQL (XAMPP recommandé)
- Base `technova` créée et migrée

### Commandes

```bash
# 1. MySQL démarré dans XAMPP

# 2. Backend
cd backend
composer install
php bin/console doctrine:migrations:migrate
php bin/console app:load-demo-data
php -S localhost:8000 -t public

# 3. Frontend (autre terminal)
cd ..
npm install
npm run dev
```

### URLs

| Service | URL |
|---------|-----|
| Site | http://localhost:5173 |
| API | http://localhost:8000/api |
| Admin | http://localhost:5173/admin |
| phpMyAdmin | http://localhost/phpmyadmin → base `technova` |

---

## 11. Glossaire

| Terme | Signification simple |
|-------|---------------------|
| **React** | Bibliothèque JavaScript pour construire l'interface |
| **Symfony** | Framework PHP pour construire l'API |
| **API REST** | Communication via URLs HTTP (GET, POST…) |
| **JWT** | Jeton de connexion (comme un badge) |
| **Doctrine** | Outil qui relie PHP et MySQL |
| **Entity** | Classe PHP = table MySQL |
| **Migration** | Script qui crée/modifie les tables |
| **DTO** | Objet qui valide les données reçues |
| **Voter** | Règle de permission Symfony |
| **Context (React)** | État partagé entre plusieurs pages |
| **Hook (React)** | Fonction réutilisable (`useCatalog`) |
| **Vite** | Outil qui lance le serveur dev React |
| **Proxy** | Redirige `/api` du frontend vers Symfony |

---

## Améliorations récentes (refactoring MVC)

| Avant | Après |
|-------|-------|
| `pages/CartContext` | `context/CartContext` |
| `utils/productHelpers` | `models/catalogModel` |
| Pages dans `pages/` | `views/public/` et `views/admin/` |
| `fetchProductById` charge 100 produits | `GET /catalogue/produits/{id}` |
| Checkout sans transaction | Transaction DB sur checkout |
| `adminList` dans Controller | `OrderService::getAllOrdersForAdmin()` |
| Suppression client sans vérif | Bloquée si commandes existantes |

---

*Rapport généré pour le projet TechNova — PFE CDA. Pour toute question, relire ce document section par section.*
