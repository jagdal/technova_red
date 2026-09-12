import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useCart } from "../../context/CartContext";
import { useAuth } from "../../context/AuthContext";
import { useWishlist } from "../../context/WishlistContext";
import { fetchProductById } from "../../services/api";
import { normalizeProduct, FALLBACK_IMAGE } from "../../models/catalogModel";
import ProductReviews from "../../components/ProductReviews";
import AppIcon from "../../components/AppIcon";
import "./ProductDetails.css";

export default function ProductDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { addToCart } = useCart();
  const { isAuthenticated, isAdmin } = useAuth();
  const { isFavorite, toggleFavorite } = useWishlist();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [imgSrc, setImgSrc] = useState(FALLBACK_IMAGE);
  const [adding, setAdding] = useState(false);
  const [favLoading, setFavLoading] = useState(false);

  useEffect(() => {
    setLoading(true);
    fetchProductById(id)
      .then((data) => {
        const p = normalizeProduct(data);
        setProduct(p);
        setImgSrc(p.image);
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) {
    return (
      <main className="container section">
        <p>Chargement...</p>
      </main>
    );
  }

  if (error || !product) {
    return (
      <main className="container section">
        <p>{error || "Produit introuvable."}</p>
        <button className="btn-primary" onClick={() => navigate("/products")}>
          Retour au catalogue
        </button>
      </main>
    );
  }

  const handleAddToCart = async () => {
    if (!isAuthenticated) {
      navigate("/login", { state: { from: `/products/${id}` } });
      return;
    }
    setAdding(true);
    try {
      await addToCart(product, 1);
      navigate("/cart");
    } catch (err) {
      if (err.message === "LOGIN_REQUIRED") {
        navigate("/login", { state: { from: `/products/${id}` } });
      } else {
        alert(err.message);
      }
    } finally {
      setAdding(false);
    }
  };

  const handleToggleFavorite = async () => {
    if (!isAuthenticated || isAdmin) {
      navigate("/login", { state: { from: `/products/${id}` } });
      return;
    }
    setFavLoading(true);
    try {
      await toggleFavorite(product.id);
    } catch (err) {
      alert(err.message);
    } finally {
      setFavLoading(false);
    }
  };

  const favorite = product ? isFavorite(product.id) : false;

  return (
    <main className="product-details-page">
      <div className="container product-details-layout">
        <div className="product-details-image">
          <img src={imgSrc} alt={product.name} onError={() => setImgSrc(FALLBACK_IMAGE)} />
        </div>
        <div className="product-details-info">
          <span className="product-details-category">{product.category}</span>
          <h1 className="product-details-name">{product.name}</h1>
          {product.reviewCount > 0 && (
            <p className="product-details-rating">
              <AppIcon name="star" size={16} strokeWidth={1.75} />
              {product.rating}/5 · {product.reviewCount} avis
            </p>
          )}
          <p className="product-details-price">{product.price.toFixed(2)} €</p>
          <p className="product-details-desc">{product.description}</p>
          <p>Stock : {product.stock}</p>
          <div className="product-details-actions">
            <button
              className="btn-primary product-details-btn"
              onClick={handleAddToCart}
              disabled={adding || product.stock < 1}
            >
              {product.stock < 1 ? "Rupture de stock" : adding ? "Ajout..." : "Ajouter au panier"}
            </button>
            <button
              type="button"
              className={`btn-secondary product-fav-detail-btn ${favorite ? "active" : ""}`}
              onClick={handleToggleFavorite}
              disabled={favLoading}
            >
              <AppIcon name="heart" size={18} strokeWidth={1.75} />
              {favorite ? "Retirer des favoris" : "Ajouter aux favoris"}
            </button>
          </div>
        </div>
      </div>
      <div className="container">
        <ProductReviews productId={product.id} />
      </div>
    </main>
  );
}
