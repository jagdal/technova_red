import { NavLink, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import AppIcon from './AppIcon';
import './AdminLayout.css';

const NAV_ITEMS = [
  { to: '/admin', label: 'Tableau de bord', icon: 'chart', end: true },
  { to: '/admin/products', label: 'Produits', icon: 'package' },
  { to: '/admin/users', label: 'Utilisateurs', icon: 'users' },
  { to: '/admin/messages', label: 'Messages', icon: 'message' },
];

export default function AdminLayout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="admin-shell">
      <aside className="admin-sidebar">
        <div className="admin-brand">
          <span className="admin-brand-icon">
            <AppIcon name="zap" size={22} strokeWidth={1.75} />
          </span>
          <div>
            <strong>TechNova</strong>
            <small>Administration</small>
          </div>
        </div>

        <nav className="admin-nav">
          {NAV_ITEMS.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              end={item.end}
              className={({ isActive }) => `admin-nav-link${isActive ? ' active' : ''}`}
            >
              <AppIcon name={item.icon} size={18} strokeWidth={1.75} />
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="admin-sidebar-footer">
          <div className="admin-user-chip">
            <span className="admin-user-avatar">{user?.nom?.[0] || 'A'}</span>
            <div>
              <strong>{user?.nom}</strong>
              <small>{user?.email}</small>
            </div>
          </div>
          <button type="button" className="admin-btn-ghost" onClick={() => navigate('/')}>
            <AppIcon name="external" size={16} strokeWidth={1.75} />
            Voir le site
          </button>
          <button type="button" className="admin-btn-danger" onClick={handleLogout}>
            <AppIcon name="logout" size={16} strokeWidth={1.75} />
            Déconnexion
          </button>
        </div>
      </aside>

      <main className="admin-main">
        <Outlet />
      </main>
    </div>
  );
}
