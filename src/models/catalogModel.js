/**
 * Modèle catalogue — transformation des données API vers le format UI.
 * Couche « Modèle » du MVC frontend.
 */

export const FALLBACK_IMAGE =
  'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=85';

const CATEGORY_IMAGES = {
  iPhone: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&q=85',
  iPad: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&q=85',
  MacBook: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&q=85',
};

/** Fallback par libellé si l'API n'envoie pas url_image. */
const PRODUCT_IMAGES = {
  'iPad Pro M4': 'https://images.unsplash.com/photo-1607452258545-943d7243463c?w=800&q=85',
  'iPad Air M1': 'https://images.unsplash.com/photo-1648806030599-c963fd14a22f?w=800&q=85',
  'iPad Pro (2022, M2 series)': 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&q=85',
  'iPhone 12 Pro Max': 'https://images.unsplash.com/photo-1606061587005-c1c57d134082?w=800&q=85',
  'iPhone 13 Pro Max': 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&q=85',
  'iPhone 14 Pro Max': 'https://images.unsplash.com/photo-1724051017997-15c226434b57?w=800&q=85',
  'iPhone 15 Pro Max': 'https://images.unsplash.com/photo-1695822822491-d92cee704368?w=800&q=85',
  'iPhone 16 Pro Max': 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&q=85',
  'iPhone 17 Pro Max': 'https://images.unsplash.com/photo-1759588071781-2c3ba9128497?w=800&q=85',
  'MacBook Air (2023, M2 series)': 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&q=85',
  'MacBook Pro 14': 'https://images.unsplash.com/photo-1611186871348-b1ce06e07c0f?w=800&q=85',
  'MacBook Pro (2024, M4 series)': 'https://images.unsplash.com/photo-1612815154858-60bb4c7ffd18?w=800&q=85',
  'MacBook Pro (M1 series)': 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=85',
};

export function resolveProductImage(name, category, urlFromApi) {
  if (urlFromApi) return urlFromApi;
  return PRODUCT_IMAGES[name] || CATEGORY_IMAGES[category] || FALLBACK_IMAGE;
}

export const CATEGORY_UI = {
  iPhone: { label: 'iPhones', slug: 'iphone', icon: 'smartphone' },
  iPad: { label: 'iPads', slug: 'ipad', icon: 'tablet' },
  MacBook: { label: 'MacBooks', slug: 'macbook', icon: 'laptop' },
};

export function normalizeProduct(apiProduct) {
  const name = apiProduct.libelle_produit || apiProduct.libelleProduit || '';
  const categoryName = apiProduct.nom_categorie || apiProduct.nomCategorie || '';
  const urlImage = apiProduct.url_image || apiProduct.urlImage || '';

  return {
    id: apiProduct.ref_produit ?? apiProduct.refProduit,
    name,
    price: parseFloat(apiProduct.prix),
    category: categoryName,
    categoryId: apiProduct.ref_categorie ?? apiProduct.refCategorie,
    description: apiProduct.description || '',
    stock: apiProduct.stock ?? 0,
    image: resolveProductImage(name, categoryName, urlImage),
    rating: apiProduct.note_moyenne ?? null,
    reviewCount: apiProduct.nb_avis ?? 0,
  };
}

export function normalizeCategory(apiCategory) {
  const name = apiCategory.nom_categorie || apiCategory.nomCategorie;
  const ui = CATEGORY_UI[name] || { label: name, slug: name.toLowerCase(), icon: 'package' };

  return {
    id: apiCategory.ref_categorie ?? apiCategory.refCategorie,
    name,
    label: ui.label,
    slug: ui.slug,
    icon: ui.icon,
  };
}

export function normalizeCartItem(apiItem) {
  const name = apiItem.libelle_produit || '';
  const urlImage = apiItem.url_image || apiItem.urlImage || '';
  return {
    id: apiItem.ref_produit,
    name,
    price: parseFloat(apiItem.prix_unitaire),
    quantity: apiItem.quantite,
    image: resolveProductImage(name, '', urlImage),
  };
}
