import { createContext, useContext, useState } from 'react';
import {
  fetchCsrfTokens,
  loginClient,
  registerClient,
  logoutClient,
} from '../services/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem('technova_user');
    return saved ? JSON.parse(saved) : null;
  });
  const [loading, setLoading] = useState(false);

  const login = async (email, password) => {
    setLoading(true);
    try {
      const tokens = await fetchCsrfTokens();
      const data = await loginClient(email, password, tokens.login_token);
      setUser(data.user);
      return data;
    } finally {
      setLoading(false);
    }
  };

  const register = async (payload) => {
    setLoading(true);
    try {
      const tokens = await fetchCsrfTokens();
      const data = await registerClient({ ...payload, csrfToken: tokens.register_token });
      setUser(data.user);
      localStorage.setItem('technova_token', data.token);
      localStorage.setItem('technova_user', JSON.stringify(data.user));
      return data;
    } finally {
      setLoading(false);
    }
  };

  const logout = async () => {
    await logoutClient();
    setUser(null);
  };

  const isAuthenticated = !!user && !!localStorage.getItem('technova_token');
  const isAdmin = user?.type === 'admin';

  return (
    <AuthContext.Provider
      value={{ user, loading, login, register, logout, isAuthenticated, isAdmin }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
}
