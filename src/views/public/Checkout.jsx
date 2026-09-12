import { useEffect, useState } from "react";
import { useNavigate, Link, useSearchParams } from "react-router-dom";
import { useCart } from "../../context/CartContext";
import { useAuth } from "../../context/AuthContext";
import { checkoutOrder, createStripeCheckoutSession } from "../../services/api";
import AppIcon from "../../components/AppIcon";
import "./Checkout.css";

export default function Checkout() {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const { cart, cartTotal, syncFromApi } = useCart();
  const { isAuthenticated, user } = useAuth();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [pendingOrderId, setPendingOrderId] = useState(null);
  const cancelled = searchParams.get("cancelled") === "1";

  useEffect(() => {
    syncFromApi();
  }, [syncFromApi]);

  if (!isAuthenticated) {
    return (
      <main className="checkout-page">
        <div className="container section">
          <p>Connectez-vous pour finaliser votre commande.</p>
          <Link to="/login" className="btn-primary">Se connecter</Link>
        </div>
      </main>
    );
  }

  if (cart.length === 0 && !pendingOrderId) {
    return (
      <main className="checkout-page">
        <div className="container section">
          <p>Votre panier est vide.</p>
          <Link to="/products" className="btn-primary">Voir les produits</Link>
        </div>
      </main>
    );
  }

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    try {
      let orderId = pendingOrderId;
      if (!orderId) {
        const commande = await checkoutOrder();
        orderId = commande.id_commande;
        setPendingOrderId(orderId);
      }
      const stripeSession = await createStripeCheckoutSession(orderId);
      window.location.href = stripeSession.checkout_url;
    } catch (err) {
      setError(err.message || "Erreur lors de la commande.");
      await syncFromApi();
      setLoading(false);
    }
  };

  return (
    <main className="checkout-page">
      <div className="container section">
        <h1 className="checkout-title">Finaliser la commande</h1>

        {cancelled && (
          <div className="checkout-alert cancelled">
            <AppIcon name="alertCircle" size={18} strokeWidth={1.75} />
            Paiement annulé. Vous pouvez réessayer quand vous le souhaitez.
          </div>
        )}

        <form className="checkout-layout" onSubmit={handleSubmit}>
          <div className="checkout-form">
            <h2 className="form-section-title">Livraison</h2>
            <p><strong>{user?.prenom} {user?.nom}</strong></p>
            <p>{user?.email}</p>
            <p>{user?.telephone}</p>
            <p>{user?.adresse}</p>

            <h2 className="form-section-title">Paiement sécurisé</h2>
            <div className="checkout-stripe-info">
              <AppIcon name="shield" size={20} strokeWidth={1.75} />
              <p>
                Vous serez redirigé vers <strong>Stripe Checkout</strong> pour
                payer par carte bancaire de manière sécurisée.
              </p>
            </div>
            {error && <p className="auth-error">{error}</p>}
          </div>

          <div className="checkout-summary">
            <h2 className="form-section-title">Récapitulatif</h2>
            <ul className="summary-list">
              {cart.map((item) => (
                <li key={item.id} className="summary-item">
                  <span>{item.name} × {item.quantity}</span>
                  <span>{(item.price * item.quantity).toFixed(2)} €</span>
                </li>
              ))}
            </ul>
            <p className="summary-total">
              Total : <strong>{cartTotal.toFixed(2)} €</strong>
            </p>
            <button type="submit" className="btn-primary checkout-btn" disabled={loading}>
              {loading ? "Redirection vers Stripe..." : "Confirmer et payer avec Stripe"}
            </button>
            <button
              type="button"
              className="checkout-back-link"
              onClick={() => navigate("/cart")}
              disabled={loading}
            >
              Retour au panier
            </button>
          </div>
        </form>
      </div>
    </main>
  );
}
