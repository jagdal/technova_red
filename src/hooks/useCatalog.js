import { useEffect, useState, useCallback } from 'react';
import { fetchProducts, fetchCategories, fetchProductById } from '../services/api';
import { normalizeProduct, normalizeCategory } from '../models/catalogModel';

/**
 * Hook catalogue — charge et normalise produits + catégories.
 */
export function useCatalog(filters = {}) {
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const load = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const [productsData, categoriesData] = await Promise.all([
        fetchProducts(filters),
        fetchCategories(),
      ]);
      setProducts((productsData.items || []).map(normalizeProduct));
      setCategories(categoriesData.map(normalizeCategory));
    } catch (err) {
      setError(err.message);
      setProducts([]);
      setCategories([]);
    } finally {
      setLoading(false);
    }
  }, [JSON.stringify(filters)]);

  useEffect(() => {
    load();
  }, [load]);

  return { products, categories, loading, error, reload: load };
}

/**
 * Hook fiche produit par identifiant.
 */
export function useProduct(id) {
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!id) return;
    setLoading(true);
    fetchProductById(id)
      .then((data) => setProduct(normalizeProduct(data)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  return { product, loading, error };
}
