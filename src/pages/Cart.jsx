import { Link } from "react-router-dom";
import { useCart } from "./CartContext";
import "./Cart.css";

export default function Cart() {
  const { cart, removeFromCart, updateQuantity, cartTotal } = useCart();

  if (cart.length === 0) {
    return (
      <main className="cart-page">
        <div className="container section">
          <h1 className="cart-title">Your Cart</h1>
          <p className="cart-empty">Your cart is empty.</p>
          <Link to="/products" className="btn-primary">
            Shop Products
          </Link>
        </div>
      </main>
    );
  }

  return (
    <main className="cart-page">
      <div className="container section">
        <h1 className="cart-title">Your Cart</h1>

        <div className="cart-list">
          {cart.map((item) => (
            <div key={item.id} className="cart-item">
              <div className="cart-item-image">
                <img src={item.image} alt={item.name} />
              </div>
              <div className="cart-item-details">
                <h3 className="cart-item-name">{item.name}</h3>
                <p className="cart-item-price">${item.price}</p>
                <div className="cart-item-actions">
                  <div className="quantity-control">
                    <button
                      onClick={() => updateQuantity(item.id, item.quantity - 1)}
                    >
                      −
                    </button>
                    <span>{item.quantity}</span>
                    <button
                      onClick={() => updateQuantity(item.id, item.quantity + 1)}
                    >
                      +
                    </button>
                  </div>
                  <button
                    className="cart-item-remove"
                    onClick={() => removeFromCart(item.id)}
                  >
                    Remove
                  </button>
                </div>
              </div>
              <p className="cart-item-total">
                ${(item.price * item.quantity).toFixed(2)}
              </p>
            </div>
          ))}
        </div>

        <div className="cart-footer">
          <p className="cart-total">
            Total: <strong>${cartTotal.toFixed(2)}</strong>
          </p>
          <Link to="/checkout" className="btn-primary cart-checkout-btn">
            Proceed to Checkout
          </Link>
        </div>
      </div>
    </main>
  );
}
