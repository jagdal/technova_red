# Architecture Backend — TechNova (Symfony MVC)

## Couches

```
HTTP Request
    ↓
Controller (src/Controller/)     ← reçoit la requête, renvoie JSON
    ↓
DTO (src/DTO/)                   ← valide les données entrantes
    ↓
Service (src/Service/)           ← logique métier
    ↓
Repository (src/Repository/)     ← accès base de données
    ↓
Entity (src/Entity/)             ← tables MySQL
```

## Règles MVC

| Couche | Responsabilité | Interdit |
|--------|----------------|----------|
| **Controller** | Routing, HTTP, délégation au Service | Logique métier, SQL direct |
| **Service** | Règles métier, transactions | Réponse HTTP |
| **Repository** | Requêtes Doctrine | Logique métier |
| **Entity** | Structure des données | Logique applicative |
| **DTO** | Validation des entrées | Accès BDD |
| **Security** | Auth JWT, CSRF, Voters | Logique métier |

## API

- **Contrôleurs custom** : auth, panier, commandes, catalogue, contact, admin
- **API Platform** : CRUD admin `Produit` et `Categorie` uniquement

## Sécurité

- JWT (`TokenService`) + session PHP (panier)
- Rôles : `ROLE_CLIENT`, `ROLE_ADMIN`
- Voters : `ProduitVoter`, `CategorieVoter`, `CommandeVoter`
