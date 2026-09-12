import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { fetchMyOrders } from '../../services/api';
import { normalizeOrder } from '../../models/orderModel';
import { FALLBACK_IMAGE } from '../../models/catalogModel';
import AppIcon from '../../components/AppIcon';
import './MyOrders.css';

export default function MyOrders() {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [expandedId, setExpandedId] = useState(null);

  useEffect(() => {
    fetchMyOrders()
      .then((data) => setOrders((Array.isArray(data) ? data : []).map(normalizeOrder)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <main className="my-orders-page">
        <div className="container section">
          <p>Chargement de vos commandes...</p>
        </div>
      </main>
    );
  }

  return (
    <main className="my-orders-page">
      <div className="container section">
        <header className="my-orders-header">
          <h1>
            <AppIcon name="orders" size={28} strokeWidth={1.75} />
            Mes commandes
          </h1>
          <p>Retrouvez l&apos;historique et le détail de vos achats.</p>
        </header>

        {error && <div className="my-orders-alert error">{error}</div>}

        {orders.length === 0 ? (
          <div className="my-orders-empty">
            <AppIcon name="package" size={48} strokeWidth={1.5} />
            <p>Vous n&apos;avez pas encore passé de commande.</p>
            <Link to="/products" className="btn-primary">Découvrir le catalogue</Link>
          </div>
        ) : (
          <div className="my-orders-list">
            {orders.map((order) => {
              const isOpen = expandedId === order.id;
              return (
                <article key={order.id} className="order-card">
                  <button
                    type="button"
                    className="order-card-header"
                    onClick={() => setExpandedId(isOpen ? null : order.id)}
                  >
                    <div>
                      <h2>Commande #{order.id}</h2>
                      <p className="order-card-meta">
                        {order.date} · {order.lines.length} article(s)
                      </p>
                    </div>
                    <div className="order-card-summary">
                      <span className={`order-status status-${order.status}`}>{order.statusLabel}</span>
                      <strong>{order.total.toFixed(2)} €</strong>
                      <AppIcon name={isOpen ? 'chevronUp' : 'chevronDown'} size={18} strokeWidth={1.75} />
                    </div>
                  </button>

                  {isOpen && (
                    <div className="order-card-body">
                      {order.payment && (
                        <p className="order-payment">
                          Paiement : {order.payment.modeLabel} ({order.payment.amount.toFixed(2)} €)
                        </p>
                      )}
                      <ul className="order-lines">
                        {order.lines.map((line) => (
                          <li key={line.refProduit} className="order-line">
                            <img
                              src={line.image || FALLBACK_IMAGE}
                              alt={line.name}
                              onError={(e) => { e.currentTarget.src = FALLBACK_IMAGE; }}
                            />
                            <div className="order-line-info">
                              <Link to={`/products/${line.refProduit}`}>{line.name}</Link>
                              <span>× {line.quantity}</span>
                            </div>
                            <strong>{line.subtotal.toFixed(2)} €</strong>
                          </li>
                        ))}
                      </ul>
                    </div>
                  )}
                </article>
              );
            })}
          </div>
        )}
      </div>
    </main>
  );
}
