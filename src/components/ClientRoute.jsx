import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

/**
 * Route réservée aux clients connectés (pas admin).
 */
export default function ClientRoute({ children }) {
  const { isAuthenticated, isAdmin, loading } = useAuth();
  const location = useLocation();

  if (loading) {
    return (
      <main className="container section">
        <p>Chargement...</p>
      </main>
    );
  }

  if (!isAuthenticated || isAdmin) {
    return <Navigate to="/login" state={{ from: location.pathname }} replace />;
  }

  return children;
}
