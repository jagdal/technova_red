import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function AdminRoute({ children }) {
  const { isAuthenticated, isAdmin, loading } = useAuth();
  const location = useLocation();

  if (loading) {
    return (
      <div className="admin-loading">
        <p>Chargement...</p>
      </div>
    );
  }

  if (!isAuthenticated || !isAdmin) {
    return <Navigate to="/login" state={{ from: location.pathname }} replace />;
  }

  return children;
}
