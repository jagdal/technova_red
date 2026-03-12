import { Link } from "react-router-dom";
import "./CategoryCard.css";

const categoryMeta = {
  Tablet: {
    label: "Tablets",
    description: "Perfect for creativity, browsing, and entertainment.",
    icon: "📱",
  },
  iPhone: {
    label: "iPhones",
    description: "Flagship phones with pro cameras and blazing speed.",
    icon: "📲",
  },
  Mac: {
    label: "MacBooks",
    description: "Powerful laptops for work, code, and design.",
    icon: "💻",
  },
};

export default function CategoryCard({ category }) {
  const meta = categoryMeta[category.id] ?? categoryMeta.Tablet;

  return (
    <Link
      to={`/products?category=${category.id}`}
      className={`category-card category-card--${category.slug}`}
    >
      <div className="category-card-icon">
        <span>{meta.icon}</span>
      </div>
      <div className="category-card-body">
        <h3 className="category-card-name">{meta.label}</h3>
        <p className="category-card-desc">{meta.description}</p>
      </div>
    
    </Link>
    
  );
}
