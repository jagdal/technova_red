import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  fetchCart,
  addCartItem,
  updateCartItem,
  removeCartItem,
} from '../services/api';
import { normalizeCartItem } from '../models/catalogModel';
import { useAuth } from './AuthContext';

export const CartContext = createContext();

export const useCart = () => {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within a CartProvider');
  }
  return context;
};

export const CartProvider = ({ children }) => {
  const { isAuthenticated } = useAuth();
  const [cart, setCart] = useState([]);
  const [cartTotal, setCartTotal] = useState(0);
  const [loading, setLoading] = useState(false);

  const syncFromApi = useCallback(async () => {
    if (!isAuthenticated) {
      setCart([]);
      setCartTotal(0);
      return;
    }
    setLoading(true);
    try {
      const data = await fetchCart();
      setCart((data.items || []).map(normalizeCartItem));
      setCartTotal(parseFloat(data.total || 0));
    } catch {
      setCart([]);
      setCartTotal(0);
    } finally {
      setLoading(false);
    }
  }, [isAuthenticated]);

  useEffect(() => {
    syncFromApi();
  }, [syncFromApi]);

  const addToCart = async (product, quantity = 1) => {
    if (!isAuthenticated) {
      throw new Error('LOGIN_REQUIRED');
    }
    await addCartItem(product.id, quantity);
    await syncFromApi();
  };

  const removeFromCart = async (productId) => {
    await removeCartItem(productId);
    await syncFromApi();
  };

  const updateQuantity = async (productId, quantity) => {
    if (quantity <= 0) {
      await removeFromCart(productId);
      return;
    }
    await updateCartItem(productId, quantity);
    await syncFromApi();
  };

  const clearCart = () => {
    setCart([]);
    setCartTotal(0);
  };

  const getTotalItems = () => cart.reduce((t, item) => t + item.quantity, 0);

  const value = {
    cart,
    cartTotal,
    loading,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    syncFromApi,
    getTotalPrice: () => cartTotal,
    getTotalItems,
    cartCount: getTotalItems(),
  };

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
};

export function useRequireAuth() {
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();

  return (callback) => {
    if (!isAuthenticated) {
      navigate('/login', { state: { from: window.location.pathname } });
      return false;
    }
    return callback();
  };
}
