import { useEffect, useState } from 'react';
import {
  fetchAdminProducts,
  fetchCategories,
  createAdminProduct,
  updateAdminProduct,
  deleteAdminProduct,
} from '../../services/api';
import AppIcon from '../../components/AppIcon';
import { resolveProductImage } from '../../models/catalogModel';

const EMPTY_FORM = {
  libelleProduit: '',
  description: '',
  prix: '',
  stock: '',
  refCategorie: '',
  urlImage: '',
};

export default function ProductsAdmin() {
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [modalOpen, setModalOpen] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState(EMPTY_FORM);

  const load = async () => {
    setLoading(true);
    try {
      const [prods, cats] = await Promise.all([fetchAdminProducts(), fetchCategories()]);
      setProducts(prods);
      setCategories(cats);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { load(); }, []);

  const openCreate = () => {
    setEditing(null);
    setForm({
      ...EMPTY_FORM,
      refCategorie: categories[0]?.refCategorie || categories[0]?.ref_categorie || '',
    });
    setModalOpen(true);
  };

  const openEdit = (p) => {
    const catId = p.categorie?.refCategorie ?? p.categorie?.ref_categorie ?? p.ref_categorie;
    setEditing(p);
    setForm({
      libelleProduit: p.libelleProduit || p.libelle_produit || '',
      description: p.description || '',
      prix: p.prix || '',
      stock: String(p.stock ?? ''),
      refCategorie: catId || '',
      urlImage: p.urlImage || p.url_image || '',
    });
    setModalOpen(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setSuccess('');
    try {
      const payload = {
        ...form,
        prix: parseFloat(form.prix),
        stock: parseInt(form.stock, 10),
        refCategorie: parseInt(form.refCategorie, 10),
      };
      if (editing) {
        const id = editing.refProduit ?? editing.ref_produit;
        await updateAdminProduct(id, payload);
        setSuccess('Produit mis à jour.');
      } else {
        await createAdminProduct(payload);
        setSuccess('Produit créé.');
      }
      setModalOpen(false);
      await load();
    } catch (err) {
      setError(err.message);
    }
  };

  const handleDelete = async (p) => {
    const name = p.libelleProduit || p.libelle_produit;
    if (!window.confirm(`Supprimer « ${name} » ?`)) return;
    try {
      const id = p.refProduit ?? p.ref_produit;
      await deleteAdminProduct(id);
      setSuccess('Produit supprimé.');
      await load();
    } catch (err) {
      setError(err.message);
    }
  };

  const getCategoryName = (p) => {
    if (p.categorie?.nomCategorie) return p.categorie.nomCategorie;
    if (p.categorie?.nom_categorie) return p.categorie.nom_categorie;
    const catId = p.categorie?.refCategorie ?? p.categorie?.ref_categorie;
    const cat = categories.find((c) => (c.refCategorie ?? c.ref_categorie) === catId);
    return cat?.nomCategorie || cat?.nom_categorie || '—';
  };

  return (
    <>
      <header className="admin-page-header">
        <h1>Gestion des produits</h1>
        <p>Créer, modifier et supprimer les produits du catalogue</p>
      </header>

      {error && <div className="admin-alert error">{error}</div>}
      {success && <div className="admin-alert success">{success}</div>}

      <div className="admin-card">
        <div className="admin-card-header">
          <h2>{products.length} produit(s)</h2>
          <button type="button" className="admin-btn-primary" onClick={openCreate}>
            + Nouveau produit
          </button>
        </div>

        {loading ? (
          <div className="admin-loading">Chargement...</div>
        ) : (
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Produit</th>
                  <th>Catégorie</th>
                  <th>Prix</th>
                  <th>Stock</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {products.map((p) => {
                  const id = p.refProduit ?? p.ref_produit;
                  const name = p.libelleProduit || p.libelle_produit;
                  const stock = p.stock ?? 0;
                  const categoryName = getCategoryName(p);
                  const image = resolveProductImage(name, categoryName, p.urlImage || p.url_image);
                  return (
                    <tr key={id}>
                      <td>
                        <img
                          src={image}
                          alt={name}
                          style={{ width: 48, height: 48, objectFit: 'cover', borderRadius: 8 }}
                        />
                      </td>
                      <td><strong>{name}</strong></td>
                      <td>{getCategoryName(p)}</td>
                      <td>{p.prix} €</td>
                      <td>
                        {stock}
                        {stock < 5 && <span className="admin-badge low-stock" style={{ marginLeft: 6 }}>Faible</span>}
                      </td>
                      <td>
                        <button type="button" className="admin-btn-sm edit" onClick={() => openEdit(p)}>
                          <AppIcon name="edit" size={14} strokeWidth={1.75} /> Modifier
                        </button>
                        <button type="button" className="admin-btn-sm delete" onClick={() => handleDelete(p)}>
                          <AppIcon name="trash" size={14} strokeWidth={1.75} /> Supprimer
                        </button>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {modalOpen && (
        <div className="admin-modal-overlay" onClick={() => setModalOpen(false)}>
          <div className="admin-modal" onClick={(e) => e.stopPropagation()}>
            <div className="admin-modal-header">
              <h3>{editing ? 'Modifier le produit' : 'Nouveau produit'}</h3>
              <button type="button" className="admin-modal-close" onClick={() => setModalOpen(false)} aria-label="Fermer">
                <AppIcon name="close" size={20} strokeWidth={1.75} />
              </button>
            </div>
            <form onSubmit={handleSubmit}>
              <div className="admin-modal-body">
                <div className="admin-form-group">
                  <label>Libellé</label>
                  <input value={form.libelleProduit} onChange={(e) => setForm({ ...form, libelleProduit: e.target.value })} required />
                </div>
                <div className="admin-form-group">
                  <label>Description</label>
                  <textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} required />
                </div>
                <div className="admin-form-group">
                  <label>Prix (€)</label>
                  <input type="number" step="0.01" min="0" value={form.prix} onChange={(e) => setForm({ ...form, prix: e.target.value })} required />
                </div>
                <div className="admin-form-group">
                  <label>Stock</label>
                  <input type="number" min="0" value={form.stock} onChange={(e) => setForm({ ...form, stock: e.target.value })} required />
                </div>
                <div className="admin-form-group">
                  <label>URL image</label>
                  <input
                    type="url"
                    placeholder="https://..."
                    value={form.urlImage}
                    onChange={(e) => setForm({ ...form, urlImage: e.target.value })}
                  />
                </div>
                <div className="admin-form-group">
                  <label>Catégorie</label>
                  <select value={form.refCategorie} onChange={(e) => setForm({ ...form, refCategorie: e.target.value })} required>
                    {categories.map((c) => {
                      const id = c.refCategorie ?? c.ref_categorie;
                      const name = c.nomCategorie ?? c.nom_categorie;
                      return <option key={id} value={id}>{name}</option>;
                    })}
                  </select>
                </div>
              </div>
              <div className="admin-modal-footer">
                <button type="button" className="admin-btn-cancel" onClick={() => setModalOpen(false)}>Annuler</button>
                <button type="submit" className="admin-btn-primary">{editing ? 'Enregistrer' : 'Créer'}</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  );
}
