import { Link } from "react-router-dom";
import AppIcon from "./AppIcon";
import "./CategoryCard.css";
import { CATEGORY_UI } from "../models/catalogModel";

export default function CategoryCard({ category }) {
  const meta = CATEGORY_UI[category.name] || {
    label: category.label || category.name,
    slug: category.slug || "other",
    icon: category.icon || "package",
  };

  return (
    <Link
      to={`/products?category=${category.id}`}
      className={`category-card category-card--${meta.slug}`}
    >
      <div className="category-card-icon">
        <AppIcon name={meta.icon} size={24} strokeWidth={1.75} />
      </div>
      <div className="category-card-body">
        <h3 className="category-card-name">{meta.label}</h3>
        <p className="category-card-desc">Découvrir la gamme {meta.label}</p>
      </div>
    </Link>
  );
}
