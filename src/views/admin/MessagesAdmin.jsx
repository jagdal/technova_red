import { Fragment, useEffect, useState } from 'react';
import { fetchAdminMessages, markMessageRead, deleteAdminMessage } from '../../services/api';
import AppIcon from '../../components/AppIcon';

export default function MessagesAdmin() {
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [expanded, setExpanded] = useState(null);

  const load = async () => {
    setLoading(true);
    try {
      const data = await fetchAdminMessages();
      setMessages(data.items || []);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { load(); }, []);

  const handleRead = async (msg) => {
    try {
      if (!msg.lu) await markMessageRead(msg.id_message);
      setExpanded(expanded === msg.id_message ? null : msg.id_message);
      if (!msg.lu) await load();
    } catch (err) {
      setError(err.message);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Supprimer ce message ?')) return;
    try {
      await deleteAdminMessage(id);
      if (expanded === id) setExpanded(null);
      await load();
    } catch (err) {
      setError(err.message);
    }
  };

  const unread = messages.filter((m) => !m.lu).length;

  return (
    <>
      <header className="admin-page-header">
        <h1>Messages de contact</h1>
        <p>{unread} message(s) non lu(s) sur {messages.length}</p>
      </header>

      {error && <div className="admin-alert error">{error}</div>}

      <div className="admin-card">
        {loading ? (
          <div className="admin-loading">Chargement...</div>
        ) : messages.length === 0 ? (
          <div className="admin-empty">Aucun message reçu pour le moment.</div>
        ) : (
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>Statut</th>
                  <th>Expéditeur</th>
                  <th>Sujet</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {messages.map((m) => (
                  <Fragment key={m.id_message}>
                    <tr style={!m.lu ? { fontWeight: 600 } : {}}>
                      <td>
                        <span className={`admin-badge ${m.lu ? 'read' : 'unread'}`}>
                          {m.lu ? 'Lu' : 'Non lu'}
                        </span>
                      </td>
                      <td>
                        <div>{m.nom}</div>
                        <small style={{ color: '#888' }}>{m.email}</small>
                      </td>
                      <td>{m.sujet}</td>
                      <td>{m.date_envoi}</td>
                      <td>
                        <button type="button" className="admin-btn-sm read" onClick={() => handleRead(m)}>
                          <AppIcon name="eye" size={14} strokeWidth={1.75} />
                          {expanded === m.id_message ? 'Masquer' : 'Lire'}
                        </button>
                        <button type="button" className="admin-btn-sm delete" onClick={() => handleDelete(m.id_message)}>
                          <AppIcon name="trash" size={14} strokeWidth={1.75} /> Supprimer
                        </button>
                      </td>
                    </tr>
                    {expanded === m.id_message && (
                      <tr>
                        <td colSpan={5}>
                          <div className="admin-message-detail">
                            <strong>Message :</strong>
                            <p>{m.message}</p>
                          </div>
                        </td>
                      </tr>
                    )}
                  </Fragment>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </>
  );
}
