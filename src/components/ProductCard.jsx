import { useState } from "react";
import { Link } from "react-router-dom";
import { useCart } from "../pages/CartContext";
import "./ProductCard.css";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&q=85";

export default function ProductCard({ product }) {
  const { addToCart } = useCart();
  const [imgSrc, setImgSrc] = useState(product.image);

  const handleImageError = () => {
    setImgSrc(FALLBACK_IMAGE);
  };

  const handleAddToCart = (e) => {
    e.preventDefault();
    addToCart(product);
  };

  return (
    <article className="product-card">
      <Link to={`/products/${product.id}`} className="product-card-link">
        <div className="product-card-image">
          <img
            src={imgSrc}
            alt={product.name}
            onError={handleImageError}
          />
        </div>
        <div className="product-card-body">
          <span className="product-card-category">{product.category}</span>
          <h3 className="product-card-name">{product.name}</h3>
          <p className="product-card-price">${product.price}</p>
        </div>
      </Link>
      <button
        className="btn-primary product-card-btn"
        onClick={handleAddToCart}
      >
        Add to Cart
      </button>
    </article>
  );
}
