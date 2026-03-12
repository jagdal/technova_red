import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useCart } from "./CartContext";
import { products } from "../data/products";
import "./ProductDetails.css";
const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=85";

export default function ProductDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { addToCart } = useCart();
  const product = products.find((p) => p.id === Number(id));
  const [imgSrc, setImgSrc] = useState(product?.image || FALLBACK_IMAGE);

  useEffect(() => {
    if (product?.image) setImgSrc(product.image);
  }, [product?.id, product?.image]);

  if (!product) {
    return (
      <main className="container section">
        <p>Product not found.</p>
        <button className="btn-primary" onClick={() => navigate("/products")}>
          Back to Products
        </button>
      </main>
    );
  }

  const handleAddToCart = () => {
    addToCart(product);
    navigate("/cart");
  };

  const handleImageError = () => {
    setImgSrc(FALLBACK_IMAGE);
  };

  return (
    <main className="product-details-page">
      <div className="container product-details-layout">
        <div className="product-details-image">
          <img
            src={imgSrc}
            alt={product.name}
            onError={handleImageError}
          />
        </div>
        <div className="product-details-info">
          <span className="product-details-category">{product.category}</span>
          <h1 className="product-details-name">{product.name}</h1>
          <p className="product-details-price">${product.price}</p>
          <p className="product-details-desc">{product.description}</p>
          <button className="btn-primary product-details-btn" onClick={handleAddToCart}>
            Add to Cart
          </button>
        </div>
      </div>
    </main>
  );
}
