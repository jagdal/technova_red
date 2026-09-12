import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useCart } from "../context/CartContext";
import { useAuth } from "../context/AuthContext";
import { useWishlist } from "../context/WishlistContext";
import AppIcon from "./AppIcon";
import { FALLBACK_IMAGE } from "../models/catalogModel";
import "./ProductCard.css";

export default function ProductCard({ product }) {
  const { addToCart } = useCart();
  const { isAuthenticated, isAdmin } = useAuth();
  const { isFavorite, toggleFavorite } = useWishlist();
  const navigate = useNavigate();
  const [imgSrc, setImgSrc] = useState(product.image);
  const [adding, setAdding] = useState(false);
  const [favLoading, setFavLoading] = useState(false);
  const favorite = isFavorite(product.id);

  const handleAddToCart = async (e) => {
    e.preventDefault();
    if (!isAuthenticated) {
      navigate("/login", { state: { from: "/products" } });
      return;
    }
    setAdding(true);
    try {
      await addToCart(product, 1);
    } catch (err) {
      alert(err.message || "Impossible d'ajouter au panier.");
    } finally {
      setAdding(false);
    }
  };

  const handleToggleFavorite = async (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (!isAuthenticated || isAdmin) {
      navigate("/login", { state: { from: "/products" } });
      return;
    }
    setFavLoading(true);
    try {
      await toggleFavorite(product.id);
    } catch (err) {
      if (err.message === "LOGIN_REQUIRED") {
        navigate("/login", { state: { from: "/products" } });
      } else {
        alert(err.message);
      }
    } finally {
      setFavLoading(false);
    }
  };

  return (
    <article className="product-card">
      <Link to={`/products/${product.id}`} className="product-card-link">
        <div className="product-card-image">
          <button
            type="button"
            className={`product-fav-btn ${favorite ? "active" : ""}`}
            onClick={handleToggleFavorite}
            disabled={favLoading}
            aria-label={favorite ? "Retirer des favoris" : "Ajouter aux favoris"}
          >
            <AppIcon name="heart" size={18} strokeWidth={1.75} />
          </button>
          <img src={imgSrc} alt={product.name} onError={() => setImgSrc(FALLBACK_IMAGE)} />
        </div>
        <div className="product-card-body">
          <span className="product-card-category">{product.category}</span>
          <h3 className="product-card-name">{product.name}</h3>
          {product.reviewCount > 0 && (
            <p className="product-card-rating">
              <AppIcon name="star" size={14} strokeWidth={1.75} />
              {product.rating}/5 ({product.reviewCount})
            </p>
          )}
          <p className="product-card-price">{product.price.toFixed(2)} €</p>
        </div>
      </Link>
      <button className="btn-primary product-card-btn" onClick={handleAddToCart} disabled={adding}>
        <AppIcon name="cart" size={18} strokeWidth={1.75} />
        {adding ? "Ajout..." : "Ajouter au panier"}
      </button>
    </article>
  );
}
