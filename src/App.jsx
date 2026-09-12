import { BrowserRouter, Routes, Route, useLocation } from "react-router-dom";
import { AuthProvider } from "./context/AuthContext";
import { CartProvider } from "./context/CartContext";
import { WishlistProvider } from "./context/WishlistContext";
import Navbar from "./components/Navbar";
import Footer from "./components/Footer";
import AdminRoute from "./components/AdminRoute";
import ClientRoute from "./components/ClientRoute";
import AdminLayout from "./components/AdminLayout";
import Home from "./views/public/Home";
import Products from "./views/public/Products";
import ProductDetails from "./views/public/ProductDetails";
import Cart from "./views/public/Cart";
import Checkout from "./views/public/Checkout";
import OrderConfirmation from "./views/public/OrderConfirmation";
import Contact from "./views/public/Contact";
import Login from "./views/public/Login";
import Signup from "./views/public/Signup";
import MyOrders from "./views/public/MyOrders";
import Wishlist from "./views/public/Wishlist";
import Notifications from "./views/public/Notifications";
import AdminDashboard from "./views/admin/AdminDashboard";
import ProductsAdmin from "./views/admin/ProductsAdmin";
import UsersAdmin from "./views/admin/UsersAdmin";
import MessagesAdmin from "./views/admin/MessagesAdmin";

function PublicShell({ children }) {
  const location = useLocation();
  const isAdmin = location.pathname.startsWith("/admin");

  if (isAdmin) return children;

  return (
    <>
      <Navbar />
      {children}
      <Footer />
    </>
  );
}

function App() {
  return (
    <AuthProvider>
      <CartProvider>
        <WishlistProvider>
        <BrowserRouter>
          <PublicShell>
            <Routes>
              <Route path="/admin" element={<AdminRoute><AdminLayout /></AdminRoute>}>
                <Route index element={<AdminDashboard />} />
                <Route path="products" element={<ProductsAdmin />} />
                <Route path="users" element={<UsersAdmin />} />
                <Route path="messages" element={<MessagesAdmin />} />
              </Route>
              <Route path="/" element={<Home />} />
              <Route path="/products" element={<Products />} />
              <Route path="/products/:id" element={<ProductDetails />} />
              <Route path="/cart" element={<Cart />} />
              <Route path="/checkout" element={<Checkout />} />
              <Route path="/commande/confirmation" element={<ClientRoute><OrderConfirmation /></ClientRoute>} />
              <Route path="/contact" element={<Contact />} />
              <Route path="/login" element={<Login />} />
              <Route path="/signup" element={<Signup />} />
              <Route path="/mes-commandes" element={<ClientRoute><MyOrders /></ClientRoute>} />
              <Route path="/favoris" element={<ClientRoute><Wishlist /></ClientRoute>} />
              <Route path="/notifications" element={<Notifications />} />
            </Routes>
          </PublicShell>
        </BrowserRouter>
        </WishlistProvider>
      </CartProvider>
    </AuthProvider>
  );
}

export default App;
