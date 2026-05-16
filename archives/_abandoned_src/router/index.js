import { createRouter, createWebHistory } from 'vue-router'

// Pages publiques
const Home = () => import('@/views/Home.vue')
const CookiePolicy = () => import('@/views/CookiePolicy.vue')
const FAQ = () => import('@/views/FAQ.vue')
const PrivacyPolicy = () => import('@/views/PrivacyPolicy.vue')
const Returns = () => import('@/views/Returns.vue')
const Shipping = () => import('@/views/Shipping.vue')
const Terms = () => import('@/views/Terms.vue')

// Pages Admin
const AdminLogin = () => import('@/views/admin/AdminLogin.vue')
const AdminLayout = () => import('@/layouts/AdminLayout.vue')
const Dashboard = () => import('@/views/admin/Dashboard.vue')
const AdminProducts = () => import('@/views/admin/AdminProducts.vue')
const AdminOrders = () => import('@/views/admin/AdminOrders.vue')
const AdminCustomers = () => import('@/views/admin/AdminCustomers.vue')

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/cookie-policy',
    name: 'CookiePolicy',
    component: CookiePolicy
  },
  {
    path: '/faq',
    name: 'FAQ',
    component: FAQ
  },
  {
    path: '/privacy',
    name: 'PrivacyPolicy',
    component: PrivacyPolicy
  },
  {
    path: '/returns',
    name: 'Returns',
    component: Returns
  },
  {
    path: '/shipping',
    name: 'Shipping',
    component: Shipping
  },
  {
    path: '/terms',
    name: 'Terms',
    component: Terms
  },
  // Admin Login
  {
    path: '/yubuy-manager',
    name: 'AdminLogin',
    component: AdminLogin
  },
  // Admin Dashboard (avec layout sidebar)
  {
    path: '/yubuy-manager',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
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
      }
    ]
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

// Navigation guard pour les routes admin
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('access_token')
    if (!token) {
      next({ name: 'AdminLogin' })
      return
    }
  }
  next()
})

export default router
