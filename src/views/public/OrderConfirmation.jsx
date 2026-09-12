import { useEffect, useState } from "react";
import { Link, useNavigate, useSearchParams } from "react-router-dom";
import { confirmStripePayment } from "../../services/api";
import { useCart } from "../../context/CartContext";
import AppIcon from "../../components/AppIcon";
import { getOrderStatusLabel, getPaymentModeLabel } from "../../models/orderModel";
import "./OrderConfirmation.css";

export default function OrderConfirmation() {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const { syncFromApi } = useCart();
  const sessionId = searchParams.get("session_id");

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [result, setResult] = useState(null);

  useEffect(() => {
    if (!sessionId) {
      setError("Session de paiement introuvable.");
      setLoading(false);
      return;
    }

    confirmStripePayment(sessionId)
      .then(async (data) => {
        setResult(data);
        await syncFromApi();
      })
      .catch((err) => setError(err.message || "Impossible de confirmer le paiement."))
      .finally(() => setLoading(false));
  }, [sessionId, syncFromApi]);

  if (loading) {
    return (
      <main className="order-confirmation-page">
        <div className="container section">
          <div className="order-confirmation-card loading">
            <p>Confirmation de votre paiement en cours...</p>
          </div>
        </div>
      </main>
    );
  }

  if (error) {
    return (
      <main className="order-confirmation-page">
        <div className="container section">
          <div className="order-confirmation-card error">
            <AppIcon name="alertCircle" size={48} strokeWidth={1.5} />
            <h1>Paiement non confirmé</h1>
            <p>{error}</p>
            <div className="order-confirmation-actions">
              <Link to="/checkout" className="btn-primary">Retour au paiement</Link>
              <Link to="/mes-commandes" className="btn-secondary">Mes commandes</Link>
            </div>
          </div>
        </div>
      </main>
    );
  }

  return (
    <main className="order-confirmation-page">
      <div className="container section">
        <div className="order-confirmation-card success">
          <div className="order-confirmation-icon">
            <AppIcon name="checkCircle" size={56} strokeWidth={1.5} />
          </div>
          <h1>Commande confirmée !</h1>
          <p>Votre paiement Stripe a été accepté. Merci pour votre achat sur TechNova.</p>

          <dl className="order-confirmation-details">
            <div>
              <dt>N° commande</dt>
              <dd>#{result.id_commande}</dd>
            </div>
            <div>
              <dt>Montant payé</dt>
              <dd>{parseFloat(result.montant).toFixed(2)} €</dd>
            </div>
            <div>
              <dt>Mode de paiement</dt>
              <dd>{getPaymentModeLabel(result.mode_paiement)}</dd>
            </div>
            <div>
              <dt>Statut</dt>
              <dd>{getOrderStatusLabel(result.statut_commande)}</dd>
            </div>
          </dl>

          <div className="order-confirmation-actions">
            <button type="button" className="btn-primary" onClick={() => navigate("/mes-commandes")}>
              Voir mes commandes
            </button>
            <Link to="/products" className="btn-secondary">Continuer mes achats</Link>
          </div>
        </div>
      </div>
    </main>
  );
}
