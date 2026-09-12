import { useState } from "react";
import { Link, useNavigate, useLocation } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";
import "./Login.css";

export default function Login() {
  const navigate = useNavigate();
  const location = useLocation();
  const { login, loading } = useAuth();
  const [form, setForm] = useState({ email: "", password: "" });
  const [error, setError] = useState("");

  const from = location.state?.from || "/";

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    setError("");
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.email.trim()) {
      setError("Veuillez saisir votre email.");
      return;
    }
    if (!form.password) {
      setError("Veuillez saisir votre mot de passe.");
      return;
    }
    try {
      const data = await login(form.email.trim(), form.password);
      navigate(data.user?.type === 'admin' ? '/admin' : from);
    } catch (err) {
      setError(err.message || "Identifiants invalides.");
    }
  };

  return (
    <main className="auth-page">
      <div className="auth-card">
        <h1 className="auth-title">Connexion</h1>
        <p className="auth-subtitle">Bienvenue sur TechNova</p>

        <form className="auth-form" onSubmit={handleSubmit}>
          {error && <p className="auth-error">{error}</p>}
          <div className="form-group">
            <label htmlFor="login-email">Email</label>
            <input
              id="login-email"
              name="email"
              type="email"
              value={form.email}
              onChange={handleChange}
              placeholder="vous@exemple.com"
              autoComplete="email"
            />
          </div>
          <div className="form-group">
            <label htmlFor="login-password">Mot de passe</label>
            <input
              id="login-password"
              name="password"
              type="password"
              value={form.password}
              onChange={handleChange}
              placeholder="••••••••"
              autoComplete="current-password"
            />
          </div>
          <button type="submit" className="btn-primary auth-submit" disabled={loading}>
            {loading ? "Connexion..." : "Se connecter"}
          </button>
        </form>

        <p className="auth-footer">
          Pas encore de compte ? <Link to="/signup">Créer un compte</Link>
        </p>
      </div>
    </main>
  );
}
