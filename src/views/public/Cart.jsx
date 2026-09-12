import { Link, useNavigate } from "react-router-dom";
import { useCart } from "../../context/CartContext";
import { useAuth } from "../../context/AuthContext";
import "./Cart.css";

export default function Cart() {
  const { cart, removeFromCart, updateQuantity, cartTotal, loading } = useCart();
  const { isAuthenticated } = useAuth();
  const navigate = useNavigate();

  if (!isAuthenticated) {
    return (
      <main className="cart-page">
        <div className="container section">
          <h1 className="cart-title">Votre panier</h1>
          <p className="cart-empty">Connectez-vous pour accéder à votre panier.</p>
          <Link to="/login" className="btn-primary">Se connecter</Link>
        </div>
      </main>
    );
  }

  if (loading) {
    return (
      <main className="cart-page">
        <div className="container section">
          <p>Chargement du panier...</p>
        </div>
      </main>
    );
  }

  if (cart.length === 0) {
    return (
      <main className="cart-page">
        <div className="container section">
          <h1 className="cart-title">Votre panier</h1>
          <p className="cart-empty">Votre panier est vide.</p>
          <Link to="/products" className="btn-primary">Voir les produits</Link>
        </div>
      </main>
    );
  }

  const handleUpdate = async (id, qty) => {
    try {
      await updateQuantity(id, qty);
    } catch (err) {
      alert(err.message);
    }
  };

  const handleRemove = async (id) => {
    try {
      await removeFromCart(id);
    } catch (err) {
      alert(err.message);
    }
  };

  return (
    <main className="cart-page">
      <div className="container section">
        <h1 className="cart-title">Votre panier</h1>

        <div className="cart-list">
          {cart.map((item) => (
            <div key={item.id} className="cart-item">
              <div className="cart-item-image">
                <img src={item.image} alt={item.name} />
              </div>
              <div className="cart-item-details">
                <h3 className="cart-item-name">{item.name}</h3>
                <p className="cart-item-price">{item.price.toFixed(2)} €</p>
                <div className="cart-item-actions">
                  <div className="quantity-control">
                    <button onClick={() => handleUpdate(item.id, item.quantity - 1)}>−</button>
                    <span>{item.quantity}</span>
                    <button onClick={() => handleUpdate(item.id, item.quantity + 1)}>+</button>
                  </div>
                  <button className="cart-item-remove" onClick={() => handleRemove(item.id)}>
                    Supprimer
                  </button>
                </div>
              </div>
              <p className="cart-item-total">{(item.price * item.quantity).toFixed(2)} €</p>
            </div>
          ))}
        </div>

        <div className="cart-footer">
          <p className="cart-total">
            Total : <strong>{cartTotal.toFixed(2)} €</strong>
          </p>
          <button className="btn-primary cart-checkout-btn" onClick={() => navigate("/checkout")}>
            Passer la commande
          </button>
        </div>
      </div>
    </main>
  );
}
