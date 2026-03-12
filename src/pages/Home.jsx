import { useState } from "react";
import { Link } from "react-router-dom";
import { products, categories } from "../data/products";
import ProductCard from "../components/ProductCard";
import CategoryCard from "../components/CategoryCard";
import "./Home.css";

const heroSlides = [
  {
    title: "Technology for your convenience",
    subtitle: "For your job, study or housework, everything you need is here.",
    cta: "Shop Now",
    ctaTo: "/products",
    image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80",
    imageAlt: "Premium headphones and tech",
  },
  {
    title: "Welcome to Technova",
    subtitle: "Next Generation Tech Store — iPhones, MacBooks & Tablets.",
    cta: "Shop Now",
    ctaTo: "/products",
    image: "https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=600&q=80",
    imageAlt: "iPhone and accessories",
  },
  {
    title: "Premium devices, best prices",
    subtitle: "Discover our latest tablets, phones and laptops with exclusive offers.",
    cta: "Explore Products",
    ctaTo: "/products",
    image: "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&q=80",
    imageAlt: "Laptop and workspace",
  },
];

export default function Home() {
  const [currentSlide, setCurrentSlide] = useState(0);

  const goToSlide = (index) => {
    setCurrentSlide((index + heroSlides.length) % heroSlides.length);
  };

  const next = () => goToSlide(currentSlide + 1);
  const prev = () => goToSlide(currentSlide - 1);

  return (
    <main>
      <section className="hero-section">
        <div className="container hero-container">
          <div className="hero-card">
            <div className="hero-card-inner">
              <div className="hero-content">
                <h1 className="hero-title">
                  {heroSlides[currentSlide].title}
                </h1>
                <p className="hero-subtitle">
                  {heroSlides[currentSlide].subtitle}
                </p>
                <Link
                  to={heroSlides[currentSlide].ctaTo}
                  className="btn-primary hero-cta"
                >
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

            <button
              type="button"
              className="hero-arrow hero-arrow-prev"
              onClick={prev}
              aria-label="Previous slide"
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M15 18l-6-6 6-6" />
              </svg>
            </button>
            <button
              type="button"
              className="hero-arrow hero-arrow-next"
              onClick={next}
              aria-label="Next slide"
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M9 18l6-6-6-6" />
              </svg>
            </button>

            <div className="hero-dots">
              {heroSlides.map((_, i) => (
                <button
                  key={i}
                  type="button"
                  className={`hero-dot ${i === currentSlide ? "hero-dot--active" : ""}`}
                  onClick={() => setCurrentSlide(i)}
                  aria-label={`Go to slide ${i + 1}`}
                />
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="section section-alt">
        <div className="container">
          <h2 className="section-title">Featured Categories</h2>
          <div className="category-grid">
            {categories.map((cat) => (
              <CategoryCard key={cat.id} category={cat} />
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <h2 className="section-title">Featured Products</h2>
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
