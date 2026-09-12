import { useState } from "react";
import { submitContactMessage } from "../../services/api";
import AppIcon from "../../components/AppIcon";
import "./Contact.css";

export default function Contact() {
  const [form, setForm] = useState({
    nom: "",
    email: "",
    sujet: "",
    message: "",
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    setError("");
    setSuccess("");
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    setSuccess("");
    try {
      const result = await submitContactMessage(form);
      setSuccess(result.message || "Message envoyé avec succès.");
      setForm({ nom: "", email: "", sujet: "", message: "" });
    } catch (err) {
      const msg = err.data?.errors
        ? Object.values(err.data.errors).join(" ")
        : err.message;
      setError(msg || "Impossible d'envoyer le message.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <main className="contact-page">
      <div className="container section">
        <h1 className="contact-title">Contact TechNova</h1>

        <div className="contact-layout">
          <div className="contact-form-wrap">
            <form className="contact-form" onSubmit={handleSubmit}>
              {error && <p className="auth-error">{error}</p>}
              {success && <p style={{ color: '#15803d', marginBottom: '1rem' }}>{success}</p>}
              <div className="form-group">
                <label htmlFor="nom">Nom</label>
                <input
                  id="nom"
                  name="nom"
                  type="text"
                  value={form.nom}
                  onChange={handleChange}
                  placeholder="Votre nom"
                  required
                />
              </div>
              <div className="form-group">
                <label htmlFor="email">Email</label>
                <input
                  id="email"
                  name="email"
                  type="email"
                  value={form.email}
                  onChange={handleChange}
                  placeholder="vous@exemple.com"
                  required
                />
              </div>
              <div className="form-group">
                <label htmlFor="sujet">Sujet</label>
                <input
                  id="sujet"
                  name="sujet"
                  type="text"
                  value={form.sujet}
                  onChange={handleChange}
                  placeholder="Objet de votre message"
                  required
                />
              </div>
              <div className="form-group">
                <label htmlFor="message">Message</label>
                <textarea
                  id="message"
                  name="message"
                  value={form.message}
                  onChange={handleChange}
                  placeholder="Comment pouvons-nous vous aider ?"
                  rows="5"
                  required
                />
              </div>
              <button type="submit" className="btn-primary contact-submit" disabled={loading}>
                {loading ? "Envoi..." : "Envoyer le message"}
              </button>
            </form>
          </div>

          <div className="contact-info">
            <h2 className="contact-info-title">Nous contacter</h2>
            <div className="contact-info-item">
              <strong><AppIcon name="mail" size={16} strokeWidth={1.75} /> Email</strong>
              <p>support@technova.com</p>
            </div>
            <div className="contact-info-item">
              <strong><AppIcon name="phone" size={16} strokeWidth={1.75} /> Téléphone</strong>
              <p>+33 1 23 45 67 89</p>
            </div>
            <div className="contact-info-item">
              <strong><AppIcon name="mapPin" size={16} strokeWidth={1.75} /> Adresse</strong>
              <p>Paris, France</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}
