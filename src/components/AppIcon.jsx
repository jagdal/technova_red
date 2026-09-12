import {
  Search,
  ShoppingBag,
  User,
  UserPlus,
  LogOut,
  LayoutDashboard,
  Mail,
  Package,
  Smartphone,
  Tablet,
  Laptop,
  LayoutGrid,
  BarChart3,
  Users,
  MessageSquare,
  Zap,
  X,
  ChevronLeft,
  ChevronRight,
  Heart,
  Bell,
  MapPin,
  Phone,
  Trash2,
  Pencil,
  Eye,
  Home,
  ShoppingCart,
  Shield,
  ExternalLink,
  Star,
  ClipboardList,
  ChevronDown,
  ChevronUp,
  CheckCircle2,
  AlertCircle,
} from 'lucide-react';
import './AppIcon.css';

const ICON_MAP = {
  search: Search,
  cart: ShoppingBag,
  shoppingCart: ShoppingCart,
  user: User,
  userPlus: UserPlus,
  logout: LogOut,
  dashboard: LayoutDashboard,
  mail: Mail,
  package: Package,
  smartphone: Smartphone,
  tablet: Tablet,
  laptop: Laptop,
  grid: LayoutGrid,
  chart: BarChart3,
  users: Users,
  message: MessageSquare,
  zap: Zap,
  close: X,
  chevronLeft: ChevronLeft,
  chevronRight: ChevronRight,
  heart: Heart,
  bell: Bell,
  mapPin: MapPin,
  phone: Phone,
  trash: Trash2,
  edit: Pencil,
  eye: Eye,
  home: Home,
  shield: Shield,
  external: ExternalLink,
  star: Star,
  orders: ClipboardList,
  chevronDown: ChevronDown,
  chevronUp: ChevronUp,
  checkCircle: CheckCircle2,
  alertCircle: AlertCircle,
};

/**
 * Icônes outline professionnelles (style Lucide).
 */
export default function AppIcon({
  name,
  size = 20,
  strokeWidth = 1.75,
  className = '',
  ...props
}) {
  const Icon = ICON_MAP[name] || Package;
  return (
    <Icon
      size={size}
      strokeWidth={strokeWidth}
      className={`app-icon ${className}`.trim()}
      aria-hidden="true"
      {...props}
    />
  );
}

export { ICON_MAP };
