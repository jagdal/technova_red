# Configuration DNS — technovax.store

## État actuel (vérifié)

- `technovax.store` → **192.64.119.35** (page parking Namecheap)
- `www.technovax.store` → alias parking Namecheap

**Le site ne peut pas être déployé en HTTPS tant que le DNS ne pointe pas vers l’IP du VPS.**

## Configuration requise

Dans **Namecheap → Domain List → technovax.store → Advanced DNS** :

| Type | Host | Value | TTL |
|------|------|-------|-----|
| A Record | `@` | `IP_DE_VOTRE_VPS` | Automatic |
| A Record | `www` | `IP_DE_VOTRE_VPS` | Automatic |

Supprimez ou désactivez les enregistrements **URL Redirect** / **Parking** existants.

## Vérification

```bash
dig +short technovax.store
dig +short www.technovax.store
```

Les deux doivent retourner l’IP du VPS (pas 192.64.119.35).

Propagation : 5 min à 48 h (souvent < 1 h).
