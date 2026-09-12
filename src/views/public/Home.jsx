import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { fetchProducts, fetchCategories } from "../../services/api";
import { normalizeProduct, normalizeCategory } from "../../models/catalogModel";
import ProductCard from "../../components/ProductCard";
import CategoryCard from "../../components/CategoryCard";
import AppIcon from "../../components/AppIcon";
import "./Home.css";

const heroSlides = [
  {
    title: "La technologie Apple, à portée de main",
    subtitle: "iPhone, iPad et MacBook — neuf et reconditionné.",
    cta: "Voir le catalogue",
    ctaTo: "/products",
    image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80",
    imageAlt: "Produits tech premium",
  },
  {
    title: "Bienvenue sur TechNova",
    subtitle: "Votre boutique spécialisée produits Apple.",
    cta: "Découvrir",
    ctaTo: "/products",
    image: "https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=600&q=80",
    imageAlt: "iPhone et accessoires",
  },
];

export default function Home() {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);

  useEffect(() => {
    fetchCategories().then((data) => setCategories(data.map(normalizeCategory))).catch(() => {});
    fetchProducts({ limit: 8 })
      .then((data) => setProducts((data.items || []).map(normalizeProduct)))
      .catch(() => {});
  }, []);

  const goToSlide = (index) => {
    setCurrentSlide((index + heroSlides.length) % heroSlides.length);
  };
  
  return (
    <main>
      <section className="hero-section">
        <div className="container hero-container">
          <div className="hero-card">
            <div className="hero-card-inner">
              <div className="hero-content">
                <h1 className="hero-title">{heroSlides[currentSlide].title}</h1>
                <p className="hero-subtitle">{heroSlides[currentSlide].subtitle}</p>
                <Link to={heroSlides[currentSlide].ctaTo} className="btn-primary hero-cta">
                  {heroSlides[currentSlide].cta}
                </Link>
              </div>
              <div className="hero-image-wrap">
                <img
                  src={heroSlides[currentSlide].image}
                  alt={heroSlides[currentSlide].imageAlt}
                  className="hero-image"
                />
              </div>
            </div>
            <button type="button" className="hero-arrow hero-arrow-prev" onClick={() => goToSlide(currentSlide - 1)} aria-label="Précédent">
              <AppIcon name="chevronLeft" size={24} strokeWidth={1.75} />
            </button>
            <button type="button" className="hero-arrow hero-arrow-next" onClick={() => goToSlide(currentSlide + 1)} aria-label="Suivant">
              <AppIcon name="chevronRight" size={24} strokeWidth={1.75} />
            </button>
            <div className="hero-dots">
              {heroSlides.map((_, i) => (
                <button
                  key={i}
                  type="button"
                  className={`hero-dot ${i === currentSlide ? "hero-dot--active" : ""}`}
                  onClick={() => setCurrentSlide(i)}
                  aria-label={`Slide ${i + 1}`}
                />
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="section section-alt">
        <div className="container">
          <h2 className="section-title">Catégories</h2>
          <div className="category-grid">
            {categories.map((cat) => (
              <CategoryCard key={cat.id} category={cat} />
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <h2 className="section-title">Produits phares</h2>
          <div className="product-grid">
            {products.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        </div>
      </section>
    </main>
  );
}
