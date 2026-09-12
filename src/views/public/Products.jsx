import { useState, useMemo, useEffect } from "react";
import { useSearchParams } from "react-router-dom";
import { fetchProducts, fetchCategories } from "../../services/api";
import { normalizeProduct, normalizeCategory } from "../../models/catalogModel";
import ProductCard from "../../components/ProductCard";
import "./Products.css";

const PRICE_RANGES = [
  { label: "Tous", min: 0, max: Infinity },
  { label: "Moins de 500 €", min: 0, max: 500 },
  { label: "500 € - 1000 €", min: 500, max: 1000 },
  { label: "1000 € - 2000 €", min: 1000, max: 2000 },
  { label: "Plus de 2000 €", min: 2000, max: Infinity },
];

export default function Products() {
  const [searchParams] = useSearchParams();
  const categoryFromUrl = searchParams.get("category") || "";
  const searchQuery = searchParams.get("q") || "";

  const [categories, setCategories] = useState([]);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [selectedCategory, setSelectedCategory] = useState(categoryFromUrl || "all");
  const [priceRange, setPriceRange] = useState("Tous");

  useEffect(() => {
    fetchCategories()
      .then((data) => setCategories(data.map(normalizeCategory)))
      .catch(() => setCategories([]));
  }, []);

  useEffect(() => {
    if (categoryFromUrl) setSelectedCategory(categoryFromUrl);
  }, [categoryFromUrl]);

  useEffect(() => {
    const range = PRICE_RANGES.find((r) => r.label === priceRange);
    const params = {
      q: searchQuery || undefined,
      refCategorie: selectedCategory !== "all" ? Number(selectedCategory) : undefined,
      prixMin: range && range.min > 0 ? range.min : undefined,
      prixMax: range && range.max !== Infinity ? range.max : undefined,
      limit: 50,
    };

    setLoading(true);
    fetchProducts(params)
      .then((data) => setProducts((data.items || []).map(normalizeProduct)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [selectedCategory, priceRange, searchQuery]);

  const heading = useMemo(() => {
    if (searchQuery) return `Résultats pour « ${searchQuery} »`;
    return "Produits";
  }, [searchQuery]);

  return (
    <main className="products-page">
      <div className="container products-layout">
        <aside className="products-sidebar">
          <h3 className="sidebar-title">Filtres</h3>

          <div className="filter-group">
            <label className="filter-label">Catégorie</label>
            <select
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
              className="filter-select"
            >
              <option value="all">Toutes</option>
              {categories.map((cat) => (
                <option key={cat.id} value={cat.id}>
                  {cat.label}
                </option>
              ))}
            </select>
          </div>

          <div className="filter-group">
            <label className="filter-label">Prix</label>
            <select
              value={priceRange}
              onChange={(e) => setPriceRange(e.target.value)}
              className="filter-select"
            >
              {PRICE_RANGES.map((r) => (
                <option key={r.label} value={r.label}>
                  {r.label}
                </option>
              ))}
            </select>
          </div>
        </aside>

        <div className="products-main">
          <h1 className="products-heading">{heading}</h1>
          {loading && <p>Chargement...</p>}
          {error && <p className="auth-error">{error}</p>}
          {!loading && !error && (
            <div className="product-grid">
              {products.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          )}
          {!loading && !error && products.length === 0 && (
            <p className="products-empty">Aucun produit ne correspond à vos filtres.</p>
          )}
        </div>
      </div>
    </main>
  );
}
