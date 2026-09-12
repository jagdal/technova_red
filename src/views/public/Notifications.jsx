import { useState } from "react";
import { Link } from "react-router-dom";
import AppIcon from "../../components/AppIcon";
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
    const icons = {
      order: 'package',
      promo: 'bell',
      product: 'shoppingCart',
      account: 'shield',
    };
    return <AppIcon name={icons[type] || 'bell'} size={20} strokeWidth={1.75} />;
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
