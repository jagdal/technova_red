import { Link } from 'react-router-dom';
import { useWishlist } from '../../context/WishlistContext';
import { useAuth } from '../../context/AuthContext';
import { normalizeProduct } from '../../models/catalogModel';
import ProductCard from '../../components/ProductCard';
import AppIcon from '../../components/AppIcon';
import './Wishlist.css';

export default function Wishlist() {
  const { items, loading } = useWishlist();
  const { user } = useAuth();

  const products = items.map((item) =>
    normalizeProduct({
      ref_produit: item.ref_produit,
      libelle_produit: item.libelle_produit,
      prix: item.prix,
      stock: item.stock,
      nom_categorie: item.nom_categorie,
      url_image: item.url_image,
      description: '',
    }),
  );

  return (
    <main className="wishlist-page">
      <div className="container section">
        <header className="wishlist-header">
          <h1>
            <AppIcon name="heart" size={28} strokeWidth={1.75} />
            Mes favoris
          </h1>
          <p>
            {user?.prenom ? `${user.prenom}, ` : ''}
            retrouvez ici les produits que vous avez sauvegardés.
          </p>
        </header>

        {loading ? (
          <p>Chargement...</p>
        ) : products.length === 0 ? (
          <div className="wishlist-empty">
            <AppIcon name="heart" size={48} strokeWidth={1.5} />
            <p>Votre liste de favoris est vide.</p>
            <Link to="/products" className="btn-primary">Parcourir le catalogue</Link>
          </div>
        ) : (
          <div className="wishlist-grid">
            {products.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )}
      </div>
    </main>
  );
}
