import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";
import "./Signup.css";

export default function Signup() {
  const navigate = useNavigate();
  const { register, loading } = useAuth();
  const [form, setForm] = useState({
    prenomClient: "",
    nomClient: "",
    email: "",
    password: "",
    confirmPassword: "",
    telephone: "",
    adresse: "",
  });
  const [error, setError] = useState("");

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    setError("");
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.prenomClient.trim() || !form.nomClient.trim()) {
      setError("Veuillez saisir votre prénom et nom.");
      return;
    }
    if (!form.email.trim()) {
      setError("Veuillez saisir votre email.");
      return;
    }
    if (form.password.length < 8) {
      setError("Le mot de passe doit contenir au moins 8 caractères.");
      return;
    }
    if (form.password !== form.confirmPassword) {
      setError("Les mots de passe ne correspondent pas.");
      return;
    }
    if (!form.telephone.trim() || !form.adresse.trim()) {
      setError("Veuillez renseigner votre téléphone et adresse.");
      return;
    }

    try {
      await register({
        prenomClient: form.prenomClient.trim(),
        nomClient: form.nomClient.trim(),
        email: form.email.trim(),
        motDePasse: form.password,
        telephone: form.telephone.trim(),
        adresse: form.adresse.trim(),
      });
      navigate("/products");
    } catch (err) {
      const msg = err.data?.errors
        ? Object.values(err.data.errors).join(" ")
        : err.message;
      setError(msg || "Impossible de créer le compte.");
    }
  };

  return (
    <main className="auth-page">
      <div className="auth-card signup-card">
        <h1 className="auth-title">Inscription</h1>
        <p className="auth-subtitle">Créez votre compte TechNova</p>

        <form className="auth-form" onSubmit={handleSubmit}>
          {error && <p className="auth-error">{error}</p>}
          <div className="form-group">
            <label htmlFor="signup-prenom">Prénom</label>
            <input id="signup-prenom" name="prenomClient" type="text" value={form.prenomClient} onChange={handleChange} />
          </div>
          <div className="form-group">
            <label htmlFor="signup-nom">Nom</label>
            <input id="signup-nom" name="nomClient" type="text" value={form.nomClient} onChange={handleChange} />
          </div>
          <div className="form-group">
            <label htmlFor="signup-email">Email</label>
            <input id="signup-email" name="email" type="email" value={form.email} onChange={handleChange} />
          </div>
          <div className="form-group">
            <label htmlFor="signup-phone">Téléphone</label>
            <input id="signup-phone" name="telephone" type="tel" value={form.telephone} onChange={handleChange} />
          </div>
          <div className="form-group">
            <label htmlFor="signup-address">Adresse</label>
            <textarea id="signup-address" name="adresse" rows="2" value={form.adresse} onChange={handleChange} />
          </div>
          <div className="form-group">
            <label htmlFor="signup-password">Mot de passe</label>
            <input id="signup-password" name="password" type="password" value={form.password} onChange={handleChange} />
          </div>
          <div className="form-group">
            <label htmlFor="signup-confirm">Confirmer le mot de passe</label>
            <input id="signup-confirm" name="confirmPassword" type="password" value={form.confirmPassword} onChange={handleChange} />
          </div>
          <button type="submit" className="btn-primary auth-submit" disabled={loading}>
            {loading ? "Création..." : "Créer mon compte"}
          </button>
        </form>

        <p className="auth-footer">
          Déjà un compte ? <Link to="/login">Se connecter</Link>
        </p>
      </div>
    </main>
  );
}
