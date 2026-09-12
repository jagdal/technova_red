import { useEffect, useState } from 'react';
import { fetchAdminUsers, deleteAdminClient } from '../../services/api';
import AppIcon from '../../components/AppIcon';

export default function UsersAdmin() {
  const [data, setData] = useState({ clients: [], admins: [] });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [tab, setTab] = useState('clients');

  const load = async () => {
    setLoading(true);
    try {
      setData(await fetchAdminUsers());
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { load(); }, []);

  const handleDelete = async (client) => {
    if (!window.confirm(`Supprimer le client ${client.prenom} ${client.nom} ?`)) return;
    try {
      await deleteAdminClient(client.id);
      setSuccess('Client supprimé.');
      await load();
    } catch (err) {
      setError(err.message);
    }
  };

  const users = tab === 'clients' ? data.clients : data.admins;

  return (
    <>
      <header className="admin-page-header">
        <h1>Utilisateurs</h1>
        <p>Gérez les clients et administrateurs de la plateforme</p>
      </header>

      {error && <div className="admin-alert error">{error}</div>}
      {success && <div className="admin-alert success">{success}</div>}

      <div style={{ display: 'flex', gap: '0.5rem', marginBottom: '1.25rem' }}>
        <button
          type="button"
          className={`admin-btn-primary${tab !== 'clients' ? '' : ''}`}
          style={{ background: tab === 'clients' ? '#ff3b3b' : '#e5e7eb', color: tab === 'clients' ? '#fff' : '#444' }}
          onClick={() => setTab('clients')}
        >
          Clients ({data.clients?.length || 0})
        </button>
        <button
          type="button"
          style={{ background: tab === 'admins' ? '#ff3b3b' : '#e5e7eb', color: tab === 'admins' ? '#fff' : '#444', border: 'none', borderRadius: 8, padding: '0.6rem 1.25rem', fontWeight: 600, cursor: 'pointer' }}
          onClick={() => setTab('admins')}
        >
          Administrateurs ({data.admins?.length || 0})
        </button>
      </div>

      <div className="admin-card">
        {loading ? (
          <div className="admin-loading">Chargement...</div>
        ) : (
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Email</th>
                  {tab === 'clients' && <th>Téléphone</th>}
                  <th>Type</th>
                  {tab === 'clients' && <th>Actions</th>}
                </tr>
              </thead>
              <tbody>
                {users.length === 0 ? (
                  <tr><td colSpan={5} className="admin-empty">Aucun utilisateur.</td></tr>
                ) : users.map((u) => (
                  <tr key={`${u.type}-${u.id}`}>
                    <td><strong>{u.prenom ? `${u.prenom} ${u.nom}` : u.nom}</strong></td>
                    <td>{u.email}</td>
                    {tab === 'clients' && <td>{u.telephone || '—'}</td>}
                    <td>
                      <span className={`admin-badge ${u.type}`}>{u.type === 'admin' ? 'Admin' : 'Client'}</span>
                    </td>
                    {tab === 'clients' && (
                      <td>
                        <button type="button" className="admin-btn-sm delete" onClick={() => handleDelete(u)}>
                          <AppIcon name="trash" size={14} strokeWidth={1.75} /> Supprimer
                        </button>
                      </td>
                    )}
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </>
  );
}
