import { useState } from "react";
import "./Contact.css";

export default function Contact() {
  const [form, setForm] = useState({
    name: "",
    email: "",
    message: "",
  });

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    alert("Thank you! We'll get back to you soon.");
    setForm({ name: "", email: "", message: "" });
  };

  return (
    <main className="contact-page">
      <div className="container section">
        <h1 className="contact-title">Contact Technova</h1>

        <div className="contact-layout">
          <div className="contact-form-wrap">
            <form className="contact-form" onSubmit={handleSubmit}>
              <div className="form-group">
                <label htmlFor="name">Name</label>
                <input
                  id="name"
                  name="name"
                  type="text"
                  value={form.name}
                  onChange={handleChange}
                  placeholder="Your name"
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
                  placeholder="your@email.com"
                />
              </div>
              <div className="form-group">
                <label htmlFor="message">Message</label>
                <textarea
                  id="message"
                  name="message"
                  value={form.message}
                  onChange={handleChange}
                  placeholder="How can we help?"
                  rows="5"
                />
              </div>
              <button type="submit" className="btn-primary contact-submit">
                Send Message
              </button>
            </form>
          </div>

          <div className="contact-info">
            <h2 className="contact-info-title">Get in Touch</h2>
            <div className="contact-info-item">
              <strong>Email</strong>
              <p>support@technova.com</p>
            </div>
            <div className="contact-info-item">
              <strong>Phone</strong>
              <p>+1 (800) 123-4567</p>
            </div>
            <div className="contact-info-item">
              <strong>Address</strong>
              <p>123 Tech Street, Silicon Valley, CA 94000</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}
