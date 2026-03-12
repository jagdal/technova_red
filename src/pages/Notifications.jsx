import { useState } from "react";
import { Link } from "react-router-dom";
import "./Notifications.css";

const initialNotifications = [
  {
    id: 1,
    type: "order",
    title: "Order shipped",
    message: "Your order #TN-2847 has been shipped. Track your delivery.",
    time: "2 hours ago",
    read: false,
  },
  {
    id: 2,
    type: "promo",
    title: "Flash sale",
    message: "20% off on all iPhones — valid until Sunday.",
    time: "5 hours ago",
    read: false,
  },
  {
    id: 3,
    type: "product",
    title: "New arrival",
    message: "iPhone 17 Pro Max is now available. Check it out!",
    time: "1 day ago",
    read: true,
  },
  {
    id: 4,
    type: "order",
    title: "Order delivered",
    message: "Your order #TN-2801 was delivered successfully.",
    time: "2 days ago",
    read: true,
  },
  {
    id: 5,
    type: "account",
    title: "Password updated",
    message: "Your password was changed. If this wasn't you, contact support.",
    time: "3 days ago",
    read: true,
  },
];

export default function Notifications() {
  const [notifications, setNotifications] = useState(initialNotifications);

  const markAsRead = (id) => {
    setNotifications((prev) =>
      prev.map((n) => (n.id === id ? { ...n, read: true } : n))
    );
  };

  const markAllAsRead = () => {
    setNotifications((prev) => prev.map((n) => ({ ...n, read: true })));
  };

  const unreadCount = notifications.filter((n) => !n.read).length;

  const getIcon = (type) => {
    switch (type) {
      case "order":
        return (
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
            <path d="M3 6h18" />
            <path d="M16 10a4 4 0 01-8 0" />
          </svg>
        );
      case "promo":
        return (
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
          </svg>
        );
      case "product":
        return (
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" />
          </svg>
        );
      default:
        return (
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
        );
    }
  };

  return (
    <main className="notifications-page">
      <div className="container section">
        <div className="notifications-header">
          <h1 className="notifications-title">Notifications</h1>
          {unreadCount > 0 && (
            <button
              type="button"
              className="notifications-mark-all"
              onClick={markAllAsRead}
            >
              Mark all as read
            </button>
          )}
        </div>

        {notifications.length === 0 ? (
          <div className="notifications-empty">
            <p>No notifications yet.</p>
            <Link to="/products" className="btn-primary">Browse products</Link>
          </div>
        ) : (
          <ul className="notifications-list">
            {notifications.map((notif) => (
              <li
                key={notif.id}
                className={`notification-item ${notif.read ? "notification-item--read" : ""}`}
                onClick={() => !notif.read && markAsRead(notif.id)}
              >
                <div className="notification-icon">{getIcon(notif.type)}</div>
                <div className="notification-body">
                  <h3 className="notification-title">{notif.title}</h3>
                  <p className="notification-message">{notif.message}</p>
                  <span className="notification-time">{notif.time}</span>
                </div>
                {!notif.read && <span className="notification-dot" />}
              </li>
            ))}
          </ul>
        )}
      </div>
    </main>
  );
}
