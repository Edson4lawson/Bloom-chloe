import { createRouter, createWebHistory } from 'vue-router'

// Pages publiques
const Home = () => import('@/views/Home.vue')
const CookiePolicy = () => import('@/views/CookiePolicy.vue')
const FAQ = () => import('@/views/FAQ.vue')
const PrivacyPolicy = () => import('@/views/PrivacyPolicy.vue')
const Returns = () => import('@/views/Returns.vue')
const Shipping = () => import('@/views/Shipping.vue')
const Terms = () => import('@/views/Terms.vue')
const ProductDetail = () => import('@/views/ProductDetail.vue')
// const ShopPage = () => import('@/views/ShopPage.vue') // Retiré car intégré dans Home
const AccountPage = () => import('@/views/AccountPage.vue')
const OrderConfirmation = () => import('@/views/OrderConfirmation.vue')

// Pages Admin
const AdminLogin = () => import('@/views/admin/AdminLogin.vue')
const AdminLayout = () => import('@/layouts/AdminLayout.vue')
const Dashboard = () => import('@/views/admin/Dashboard.vue')
const AdminProducts = () => import('@/views/admin/AdminProducts.vue')
const AdminOrders = () => import('@/views/admin/AdminOrders.vue')
const AdminCustomers = () => import('@/views/admin/AdminCustomers.vue')
const AdminSettings = () => import('@/views/admin/AdminSettings.vue')
const AdminAnalytics = () => import('@/views/admin/AdminAnalytics.vue')
const CategoryManager = () => import('@/views/admin/Categories/CategoryManager.vue')

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { title: 'Accueil — Bloom by Chloé' }
  },
  {
    path: '/boutique',
    redirect: { name: 'Home', hash: '#products' }
  },
  {
    path: '/produit/:slug',
    name: 'ProductDetail',
    component: ProductDetail,
    meta: { title: 'Produit — Bloom by Chloé' }
  },
  {
    path: '/mon-compte',
    name: 'Account',
    component: AccountPage,
    meta: { requiresAuth: true, title: 'Mon Compte — Bloom by Chloé' }
  },
  {
    path: '/commande-confirmee/:orderId',
    name: 'OrderConfirmation',
    component: OrderConfirmation,
    meta: { title: 'Commande confirmée — Bloom by Chloé' }
  },
  {
    path: '/cookie-policy',
    name: 'CookiePolicy',
    component: CookiePolicy,
    meta: { title: 'Politique cookies — Bloom by Chloé' }
  },
  {
    path: '/faq',
    name: 'FAQ',
    component: FAQ,
    meta: { title: 'FAQ — Bloom by Chloé' }
  },
  {
    path: '/privacy',
    name: 'PrivacyPolicy',
    component: PrivacyPolicy,
    meta: { title: 'Confidentialité — Bloom by Chloé' }
  },
  {
    path: '/returns',
    name: 'Returns',
    component: Returns,
    meta: { title: 'Retours — Bloom by Chloé' }
  },
  {
    path: '/shipping',
    name: 'Shipping',
    component: Shipping,
    meta: { title: 'Livraison — Bloom by Chloé' }
  },
  {
    path: '/terms',
    name: 'Terms',
    component: Terms,
    meta: { title: 'CGV — Bloom by Chloé' }
  },
  // Admin Login — Route séparée
  {
    path: '/bloom-manager/login',
    alias: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin,
    meta: { title: 'Admin Login — Bloom Manager' }
  },
  // Admin Dashboard (avec layout sidebar) — Plus de conflit de route
  {
    path: '/bloom-manager',
    alias: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: '',
        redirect: { name: 'AdminDashboard' }
      },
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: Dashboard
      },
      {
        path: 'products',
        name: 'AdminProducts',
        component: AdminProducts
      },
      {
        path: 'orders',
        name: 'AdminOrders',
        component: AdminOrders
      },
      {
        path: 'users',
        name: 'AdminCustomers',
        component: AdminCustomers
      },
      {
        path: 'settings',
        name: 'AdminSettings',
        component: AdminSettings
      },
      {
        path: 'analytics',
        name: 'AdminAnalytics',
        component: AdminAnalytics
      },
      {
        path: 'categories',
        name: 'AdminCategories',
        component: CategoryManager
      }
    ]
  },
  // Redirection de l'ancien chemin admin
  {
    path: '/yubuy-manager/:pathMatch(.*)*',
    redirect: to => {
      return { path: to.path.replace('/yubuy-manager', '/bloom-manager') }
    }
  },

  // Catch-all 404
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    if (to.hash) return { el: to.hash, behavior: 'smooth' }
    return { top: 0 }
  }
})

// Navigation guard
router.beforeEach((to, from, next) => {
  // Update document title
  if (to.meta.title) {
    document.title = to.meta.title
  }

  // Auth guard
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('access_token')
    if (!token) {
      if (to.meta.requiresAdmin) {
        next({ name: 'AdminLogin' })
      } else {
        next({ name: 'Home' })
      }
      return
    }
  }

  // Admin guard
  if (to.meta.requiresAdmin) {
    const user = JSON.parse(localStorage.getItem('user') || '{}')
    if (user.role !== 'admin') {
      next({ name: 'Home' })
      return
    }
  }

  next()
})

export default router


