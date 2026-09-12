import { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { fetchProductReviews, submitProductReview } from '../services/api';
import AppIcon from './AppIcon';
import './ProductReviews.css';

function StarRating({ value, onChange, readonly = false }) {
  return (
    <div className={`star-rating ${readonly ? 'readonly' : ''}`}>
      {[1, 2, 3, 4, 5].map((star) => (
        <button
          key={star}
          type="button"
          className={star <= value ? 'active' : ''}
          onClick={() => !readonly && onChange?.(star)}
          disabled={readonly}
          aria-label={`${star} étoile${star > 1 ? 's' : ''}`}
        >
          <AppIcon name="star" size={18} strokeWidth={1.75} />
        </button>
      ))}
    </div>
  );
}

export default function ProductReviews({ productId }) {
  const { isAuthenticated, isAdmin } = useAuth();
  const [reviews, setReviews] = useState([]);
  const [average, setAverage] = useState(null);
  const [count, setCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [note, setNote] = useState(5);
  const [comment, setComment] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const load = () => {
    setLoading(true);
    fetchProductReviews(productId)
      .then((data) => {
        setReviews(data.items || []);
        setAverage(data.moyenne);
        setCount(data.nb_avis || 0);
      })
      .catch(() => {
        setReviews([]);
        setAverage(null);
        setCount(0);
      })
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    load();
  }, [productId]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setSuccess('');
    setSubmitting(true);
    try {
      await submitProductReview({ refProduit: Number(productId), note, commentaire: comment });
      setComment('');
      setSuccess('Merci pour votre avis !');
      load();
    } catch (err) {
      setError(err.message);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <section className="product-reviews">
      <header className="product-reviews-header">
        <h2>Avis clients</h2>
        {count > 0 && (
          <p>
            <StarRating value={Math.round(average || 0)} readonly />
            <span>{average}/5 · {count} avis</span>
          </p>
        )}
      </header>

      {isAuthenticated && !isAdmin && (
        <form className="review-form" onSubmit={handleSubmit}>
          <h3>Donner votre avis</h3>
          <label>Note</label>
          <StarRating value={note} onChange={setNote} />
          <label htmlFor="comment">Commentaire</label>
          <textarea
            id="comment"
            value={comment}
            onChange={(e) => setComment(e.target.value)}
            placeholder="Partagez votre expérience avec ce produit..."
            required
            maxLength={500}
          />
          {error && <p className="review-error">{error}</p>}
          {success && <p className="review-success">{success}</p>}
          <button type="submit" className="btn-primary" disabled={submitting}>
            {submitting ? 'Envoi...' : 'Publier mon avis'}
          </button>
        </form>
      )}

      {loading ? (
        <p>Chargement des avis...</p>
      ) : reviews.length === 0 ? (
        <p className="reviews-empty">Aucun avis pour le moment. Soyez le premier !</p>
      ) : (
        <ul className="reviews-list">
          {reviews.map((review) => (
            <li key={review.id_avis} className="review-item">
              <div className="review-item-top">
                <strong>{review.auteur}</strong>
                <StarRating value={review.note} readonly />
              </div>
              <p>{review.commentaire}</p>
              <span className="review-date">{review.date_avis}</span>
            </li>
          ))}
        </ul>
      )}
    </section>
  );
}
