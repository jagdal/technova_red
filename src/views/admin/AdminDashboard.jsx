import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { fetchDashboard } from '../../services/api';

export default function AdminDashboard() {
  const [data, setData] = useState(null);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchDashboard()
      .then(setData)
      .catch((err) => setError(err.message));
  }, []);

  if (error) return <div className="admin-alert error">{error}</div>;
  if (!data) return <div className="admin-loading">Chargement du tableau de bord...</div>;

  const stats = [
    { label: 'Commandes', value: data.nb_commandes, color: 'blue' },
    { label: 'Produits', value: data.nb_produits, color: 'green' },
    { label: 'Clients', value: data.nb_clients, color: 'purple' },
    { label: 'Messages non lus', value: data.messages_non_lus, color: 'orange' },
    { label: "Chiffre d'affaires", value: `${data.chiffre_affaires} €`, color: '' },
    { label: 'Stock faible', value: data.produits_stock_faible, color: 'orange' },
  ];

  return (
    <>
      <header className="admin-page-header">
        <h1>Tableau de bord</h1>
        <p>Vue d'ensemble de votre boutique TechNova</p>
      </header>

      <div className="admin-stats-grid">
        {stats.map((s) => (
          <div key={s.label} className={`admin-stat-card ${s.color}`}>
            <div className="admin-stat-label">{s.label}</div>
            <div className="admin-stat-value">{s.value}</div>
          </div>
        ))}
      </div>

      <div className="admin-card">
        <div className="admin-card-header">
          <h2>Dernières commandes</h2>
          <Link to="/admin/products" className="admin-btn-primary" style={{ textDecoration: 'none' }}>
            Gérer les produits
          </Link>
        </div>
        {data.dernieres_commandes?.length > 0 ? (
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>N°</th>
                  <th>Date</th>
                  <th>Total</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                {data.dernieres_commandes.map((c) => (
                  <tr key={c.id_commande}>
                    <td>#{c.id_commande}</td>
                    <td>{c.date_commande}</td>
                    <td>{c.total} €</td>
                    <td><span className="admin-badge client">{c.statut}</span></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="admin-empty">Aucune commande pour le moment.</div>
        )}
      </div>
    </>
  );
}
