const API_URL = import.meta.env.VITE_API_URL || '/api';

function getToken() {
  return localStorage.getItem('technova_token');
}

function getAuthHeaders() {
  const headers = { 'Content-Type': 'application/json', Accept: 'application/json' };
  const token = getToken();
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }
  return headers;
}

async function handleResponse(response) {
  const text = await response.text();
  const data = text ? JSON.parse(text) : {};

  if (!response.ok) {
    const message = data.message || data.detail || data['hydra:description'] || 'Une erreur est survenue.';
    const error = new Error(typeof message === 'string' ? message : JSON.stringify(message));
    error.status = response.status;
    error.data = data;
    throw error;
  }

  return data;
}

function parseCollection(data) {
  if (Array.isArray(data)) return data;
  if (data['hydra:member']) return data['hydra:member'];
  if (data.member) return data.member;
  return [];
}

/** Requête API avec cookie de session (panier) + JWT optionnel. */
async function apiFetch(path, options = {}) {
  const response = await fetch(`${API_URL}${path}`, {
    credentials: 'include',
    ...options,
    headers: {
      ...getAuthHeaders(),
      ...options.headers,
    },
  });
  return handleResponse(response);
}

// --- Auth ---

export async function fetchCsrfTokens() {
  return apiFetch('/auth/csrf');
}

export async function registerClient(payload) {
  return apiFetch('/auth/register', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export async function loginClient(email, motDePasse, csrfToken) {
  const data = await apiFetch('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, motDePasse, csrfToken }),
  });
  if (data.token) {
    localStorage.setItem('technova_token', data.token);
    localStorage.setItem('technova_user', JSON.stringify(data.user));
  }
  return data;
}

export async function logoutClient() {
  localStorage.removeItem('technova_token');
  localStorage.removeItem('technova_user');
  return apiFetch('/auth/logout', { method: 'POST' });
}

// --- Catalogue ---

export async function fetchProducts({ q, refCategorie, prixMin, prixMax, page = 1, limit = 50 } = {}) {
  const params = new URLSearchParams();
  if (q) params.set('q', q);
  if (refCategorie) params.set('ref_categorie', String(refCategorie));
  if (prixMin != null) params.set('prix_min', String(prixMin));
  if (prixMax != null) params.set('prix_max', String(prixMax));
  params.set('page', String(page));
  params.set('limit', String(limit));

  return apiFetch(`/catalogue/produits?${params.toString()}`);
}

export async function fetchProductById(id) {
  return apiFetch(`/catalogue/produits/${id}`);
}

export async function fetchCategories() {
  const data = await apiPlatformGet('/categories?itemsPerPage=50');
  return parseCollection(data);
}

// --- Panier ---

export async function fetchCart() {
  return apiFetch('/panier');
}

export async function addCartItem(refProduit, quantite) {
  return apiFetch('/panier/items', {
    method: 'POST',
    body: JSON.stringify({ refProduit, quantite }),
  });
}

export async function updateCartItem(refProduit, quantite) {
  return apiFetch(`/panier/items/${refProduit}`, {
    method: 'PATCH',
    body: JSON.stringify({ quantite }),
  });
}

export async function removeCartItem(refProduit) {
  return apiFetch(`/panier/items/${refProduit}`, { method: 'DELETE' });
}

// --- Commandes & paiement ---

export async function checkoutOrder() {
  return apiFetch('/commandes/checkout', { method: 'POST' });
}

export async function createPayment(idCommande, modePaiement) {
  return apiFetch('/paiements', {
    method: 'POST',
    body: JSON.stringify({ idCommande, modePaiement }),
  });
}

export async function createStripeCheckoutSession(idCommande) {
  return apiFetch('/paiements/stripe/session', {
    method: 'POST',
    body: JSON.stringify({ idCommande }),
  });
}

export async function confirmStripePayment(sessionId) {
  return apiFetch(`/paiements/stripe/confirm?session_id=${encodeURIComponent(sessionId)}`);
}

export async function fetchMyOrders() {
  return apiFetch('/commandes');
}

export async function fetchOrderById(id) {
  return apiFetch(`/commandes/${id}`);
}

// --- Avis produits ---

export async function fetchProductReviews(refProduit) {
  return apiFetch(`/catalogue/produits/${refProduit}/avis`);
}

export async function submitProductReview(payload) {
  return apiFetch('/avis', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

// --- Favoris (wishlist) ---

export async function fetchFavorites() {
  return apiFetch('/favoris');
}

export async function addFavorite(refProduit) {
  return apiFetch(`/favoris/${refProduit}`, { method: 'POST' });
}

export async function removeFavorite(refProduit) {
  return apiFetch(`/favoris/${refProduit}`, { method: 'DELETE' });
}

// --- Contact public ---

export async function submitContactMessage(payload) {
  return apiFetch('/contact', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

// --- Admin ---

function getApiPlatformHeaders(includeBody = true) {
  const headers = { Accept: 'application/ld+json' };
  if (includeBody) {
    headers['Content-Type'] = 'application/ld+json';
  }
  const token = getToken();
  if (token) headers.Authorization = `Bearer ${token}`;
  return headers;
}

async function apiPlatformFetch(path, options = {}) {
  const hasBody = options.body != null;
  const response = await fetch(`${API_URL}${path}`, {
    credentials: 'include',
    ...options,
    headers: {
      ...getApiPlatformHeaders(hasBody),
      ...options.headers,
    },
  });
  return handleResponse(response);
}

/** GET API Platform (catégories, produits admin). */
async function apiPlatformGet(path) {
  return apiPlatformFetch(path);
}

export async function fetchDashboard() {
  return apiFetch('/admin/dashboard');
}

export async function fetchAdminUsers() {
  return apiFetch('/admin/utilisateurs');
}

export async function deleteAdminClient(id) {
  return apiFetch(`/admin/utilisateurs/clients/${id}`, { method: 'DELETE' });
}

export async function fetchAdminMessages() {
  return apiFetch('/admin/messages');
}

export async function markMessageRead(id) {
  return apiFetch(`/admin/messages/${id}/lu`, { method: 'PATCH' });
}

export async function deleteAdminMessage(id) {
  return apiFetch(`/admin/messages/${id}`, { method: 'DELETE' });
}

export async function fetchAdminProducts() {
  const data = await apiPlatformFetch('/produits?itemsPerPage=100');
  return parseCollection(data);
}

export async function createAdminProduct(payload) {
  return apiPlatformFetch('/produits', {
    method: 'POST',
    body: JSON.stringify({
      libelleProduit: payload.libelleProduit,
      description: payload.description,
      prix: String(payload.prix),
      stock: Number(payload.stock),
      categorie: `${API_URL}/categories/${payload.refCategorie}`,
    }),
  });
}

export async function updateAdminProduct(id, payload) {
  return apiPlatformFetch(`/produits/${id}`, {
    method: 'PUT',
    body: JSON.stringify({
      libelleProduit: payload.libelleProduit,
      description: payload.description,
      prix: String(payload.prix),
      stock: Number(payload.stock),
      categorie: `${API_URL}/categories/${payload.refCategorie}`,
    }),
  });
}

export async function deleteAdminProduct(id) {
  return apiPlatformFetch(`/produits/${id}`, { method: 'DELETE' });
}

export { getToken, API_URL };
