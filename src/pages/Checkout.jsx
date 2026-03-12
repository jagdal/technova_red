import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import { useCart } from "./CartContext";
import "./Checkout.css";

// Cette page permet de finaliser l'achat en saisissant les informations de livraison et en affichant le résumé de la commande.

export default function Checkout() {
  const navigate = useNavigate();
  const { cart, cartTotal } = useCart();
  const [form, setForm] = useState({
    fullName: "",
    email: "",
    address: "",
    phone: "",
  });

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Simple validation
    if (!form.fullName || !form.email || !form.address || !form.phone) {
      alert("Please fill in all fields.");
      return;
    }
    alert("Order placed successfully! Thank you for shopping with Technova.");
    navigate("/");
  };

  if (cart.length === 0) {
    return (
      <main className="checkout-page">
        <div className="container section">
          <p>Your cart is empty.</p>
          <Link to="/products" className="btn-primary">
            Shop Products
          </Link>
        </div>
      </main>
    );
  }

  return (
    <main className="checkout-page">
      <div className="container section">
        <h1 className="checkout-title">Checkout</h1>

        <form className="checkout-layout" onSubmit={handleSubmit}>
          <div className="checkout-form">
            <h2 className="form-section-title">Shipping Details</h2>
            <div className="form-group">
              <label htmlFor="fullName">Full Name</label>
              <input
                id="fullName"
                name="fullName"
                type="text"
                value={form.fullName}
                onChange={handleChange}
                placeholder="John Doe"
                required
              />
            </div>
            <div className="form-group">
              <label htmlFor="email">Email</label>
              <input
                id="email"
                name="email"
                type="email"
                value={form.email}
                onChange={handleChange}
                placeholder="john@example.com"
                required
              />
            </div>
            <div className="form-group">
              <label htmlFor="address">Address</label>
              <textarea
                id="address"
                name="address"
                value={form.address}
                onChange={handleChange}
                placeholder="Street, City, State, ZIP"
                rows="3"
                required
              />
            </div>
            <div className="form-group">
              <label htmlFor="phone">Phone</label>
              <input
                id="phone"
                name="phone"
                type="tel"
                value={form.phone}
                onChange={handleChange}
                placeholder="+1 234 567 8900"
                required
              />
            </div>
          </div>

          <div className="checkout-summary">
            <h2 className="form-section-title">Order Summary</h2>
            <ul className="summary-list">
              {cart.map((item) => (
                <li key={item.id} className="summary-item">
                  <span>
                    {item.name} × {item.quantity}
                  </span>
                  <span>${(item.price * item.quantity).toFixed(2)}</span>
                </li>
              ))}
            </ul>
            <p className="summary-total">
              Total: <strong>${cartTotal.toFixed(2)}</strong>
            </p>
            <button type="submit" className="btn-primary checkout-btn">
              Place Order
            </button>
          </div>
        </form>
      </div>
    </main>
  );
}
