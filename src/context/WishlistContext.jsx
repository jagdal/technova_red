import { createContext, useCallback, useContext, useEffect, useState } from 'react';
import { useAuth } from './AuthContext';
import { addFavorite, fetchFavorites, removeFavorite } from '../services/api';

const WishlistContext = createContext(null);

export function WishlistProvider({ children }) {
  const { isAuthenticated, isAdmin } = useAuth();
  const [favoriteIds, setFavoriteIds] = useState([]);
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(false);

  const load = useCallback(async () => {
    if (!isAuthenticated || isAdmin) {
      setFavoriteIds([]);
      setItems([]);
      return;
    }
    setLoading(true);
    try {
      const data = await fetchFavorites();
      setFavoriteIds(data.ids || []);
      setItems(data.items || []);
    } catch {
      setFavoriteIds([]);
      setItems([]);
    } finally {
      setLoading(false);
    }
  }, [isAuthenticated, isAdmin]);

  useEffect(() => {
    load();
  }, [load]);

  const toggleFavorite = async (productId) => {
    if (!isAuthenticated || isAdmin) {
      throw new Error('LOGIN_REQUIRED');
    }
    if (favoriteIds.includes(productId)) {
      await removeFavorite(productId);
    } else {
      await addFavorite(productId);
    }
    await load();
  };

  const isFavorite = (productId) => favoriteIds.includes(productId);

  return (
    <WishlistContext.Provider
      value={{
        favoriteIds,
        items,
        loading,
        reload: load,
        toggleFavorite,
        isFavorite,
        count: favoriteIds.length,
      }}
    >
      {children}
    </WishlistContext.Provider>
  );
}

export function useWishlist() {
  const ctx = useContext(WishlistContext);
  if (!ctx) throw new Error('useWishlist must be used within WishlistProvider');
  return ctx;
}
