import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useCart } from "../context/CartContext";
import { useAuth } from "../context/AuthContext";
import { useWishlist } from "../context/WishlistContext";
import { fetchCategories } from "../services/api";
import { normalizeCategory } from "../models/catalogModel";
import AppIcon from "./AppIcon";
import "./Navbar.css";

export default function Navbar() {
  const { cartCount } = useCart();
  const { isAuthenticated, isAdmin, user, logout } = useAuth();
  const { count: wishlistCount } = useWishlist();
  const navigate = useNavigate();
  const [searchQuery, setSearchQuery] = useState("");
  const [categories, setCategories] = useState([]);

  useEffect(() => {
    fetchCategories()
      .then((data) => setCategories(data.map(normalizeCategory)))
      .catch(() => setCategories([]));
  }, []);

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      navigate(`/products?q=${encodeURIComponent(searchQuery.trim())}`);
      setSearchQuery("");
    } else {
      navigate("/products");
    }
  };

  const handleLogout = async () => {
    await logout();
    navigate("/");
  };

  return (
    <nav className="navbar">
      <div className="navbar-top">
        <div className="container navbar-top-inner">
          <div className="navbar-top-right">
            <a href="mailto:support@technova.com" className="navbar-contact-item">
              <AppIcon name="mail" size={16} strokeWidth={1.75} />
              support@technova.com
            </a>
          </div>
        </div>
      </div>

      <div className="navbar-main">
        <div className="container navbar-main-inner">
          <Link to="/" className="navbar-logo">TECHNOVA</Link>

          <form className="navbar-search" onSubmit={handleSearch}>
            <span className="navbar-search-icon" aria-hidden="true">
              <AppIcon name="search" size={18} strokeWidth={1.75} />
            </span>
            <input
              type="search"
              placeholder="Rechercher un produit..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="navbar-search-input"
              aria-label="Rechercher"
            />
          </form>

          <div className="navbar-actions">
            {isAdmin && (
              <Link to="/admin" className="navbar-action-item" title="Administration">
                <AppIcon name="dashboard" size={22} strokeWidth={1.75} />
                <span className="navbar-action-label">Admin</span>
              </Link>
            )}

            <Link to="/cart" className="navbar-action-item navbar-cart-link" aria-label="Panier">
              <AppIcon name="cart" size={22} strokeWidth={1.75} />
              <span className="navbar-action-label">Panier</span>
              {cartCount > 0 && <span className="navbar-cart-badge">{cartCount}</span>}
            </Link>

            {isAuthenticated && !isAdmin && (
              <>
                <Link to="/favoris" className="navbar-action-item" title="Favoris">
                  <AppIcon name="heart" size={22} strokeWidth={1.75} />
                  <span className="navbar-action-label">Favoris</span>
                  {wishlistCount > 0 && <span className="navbar-cart-badge">{wishlistCount}</span>}
                </Link>
                <Link to="/mes-commandes" className="navbar-action-item" title="Mes commandes">
                  <AppIcon name="orders" size={22} strokeWidth={1.75} />
                  <span className="navbar-action-label">Commandes</span>
                </Link>
              </>
            )}

            {isAuthenticated ? (
              <>
                <div className="navbar-action-item navbar-user-item">
                  <span className="navbar-user-avatar">
                    {(user?.prenom?.[0] || user?.nom?.[0] || "U").toUpperCase()}
                  </span>
                  <span className="navbar-action-label">
                    {user?.prenom || user?.nom}
                  </span>
                </div>
                <button type="button" className="navbar-action-item navbar-logout-btn" onClick={handleLogout} title="Déconnexion">
                  <AppIcon name="logout" size={20} strokeWidth={1.75} />
                </button>
              </>
            ) : (
              <>
                <Link to="/login" className="navbar-action-item">
                  <AppIcon name="user" size={22} strokeWidth={1.75} />
                  <span className="navbar-action-label">Connexion</span>
                </Link>
                <Link to="/signup" className="navbar-btn-signup">
                  <AppIcon name="userPlus" size={18} strokeWidth={1.75} />
                  Inscription
                </Link>
              </>
            )}
          </div>
        </div>
      </div>

      <div className="navbar-categories">
        <div className="container navbar-categories-inner">
          <Link to="/products" className="navbar-cat-link">
            <AppIcon name="grid" size={16} strokeWidth={1.75} />
            Tous
          </Link>
          {categories.map((cat) => (
            <Link key={cat.id} to={`/products?category=${cat.id}`} className="navbar-cat-link">
              <AppIcon name={cat.icon || "package"} size={16} strokeWidth={1.75} />
              {cat.label}
            </Link>
          ))}
          <Link to="/contact" className="navbar-cat-link">
            <AppIcon name="mail" size={16} strokeWidth={1.75} />
            Contact
          </Link>
        </div>
      </div>
    </nav>
  );
}
