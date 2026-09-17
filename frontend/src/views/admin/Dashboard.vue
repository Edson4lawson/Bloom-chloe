<template>
  <!-- 
    Tableau de Bord Administrateur (Dashboard) - Bloom Chloé
    Statistiques globales en temps réel, KPIs consolidés et monitoring du catalogue.
  -->
  <div class="space-y-8">
    <!-- En-tête contextuel avec Date et Statut Temps Réel -->
    <div class="dashboard-header flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="flex items-center space-x-3">
          <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
            Bonjour {{ authStore.user?.first_name ? authStore.user.first_name : 'Admin' }}
          </h2>
          <!-- Badge Live / En direct -->
          <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
            En direct
          </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mt-1">
          Supervision en temps réel des ventes, commandes et stocks de Bloom Chloé.
        </p>
      </div>

      <div class="flex items-center space-x-3">
        <!-- Bouton Actualiser -->
        <button 
          @click="loadStats(false)" 
          :disabled="isLoading"
          class="flex items-center space-x-2 bg-white dark:bg-bloom-dark-card hover:bg-purple-50 dark:hover:bg-bloom-dark-border/40 text-gray-700 dark:text-gray-200 px-3.5 py-2 rounded-2xl shadow-sm border border-purple-100/60 dark:border-bloom-dark-border transition-all cursor-pointer text-xs font-bold"
          title="Actualiser les données"
        >
          <RefreshCw class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" :class="{ 'animate-spin': isRefreshing || isLoading }" />
          <span>Actualiser</span>
        </button>

        <!-- Date du jour -->
        <div class="flex items-center space-x-2 bg-white dark:bg-bloom-dark-card px-4 py-2 rounded-2xl shadow-sm border border-purple-100/60 dark:border-bloom-dark-border">
          <Calendar class="w-4 h-4 text-purple-600 dark:text-purple-400" />
          <span class="text-sm font-bold text-gray-800 dark:text-white">{{ currentDate }}</span>
        </div>
      </div>
    </div>

    <!-- Section Cartes Statistiques (KPIs Complets) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatsCard
        title="Total Produits"
        :value="stats.totalProducts"
        :icon="Package"
        color="purple"
        class="stats-card-anim"
        :loading="isLoading"
      />
      <StatsCard
        title="Total Commandes"
        :value="stats.totalOrders"
        :icon="ShoppingCart"
        color="purple"
        class="stats-card-anim"
        :loading="isLoading"
      />
      <StatsCard
        title="Chiffre d'Affaires"
        :value="stats.totalRevenue"
        :icon="Wallet"
        suffix="FCFA"
        color="purple"
        class="stats-card-anim"
        :loading="isLoading"
      />
      <StatsCard
        title="Clients Inscrits"
        :value="stats.totalClients"
        :icon="Users"
        color="purple"
        class="stats-card-anim"
        :loading="isLoading"
      />

      <!-- Ligne 2 KPIs Opérationnels -->
      <StatsCard
        title="Commandes du Jour"
        :value="stats.todayOrders"
        :icon="Clock"
        color="purple"
        class="stats-card-anim"
        :loading="isLoading"
      />
      <StatsCard
        title="En Attente"
        :value="stats.pendingOrders"
        :icon="Hourglass"
        color="yellow"
        class="stats-card-anim"
        :loading="isLoading"
      />
      <StatsCard
        title="Alertes Stock (≤ 5)"
        :value="stats.stockAlerts"
        :icon="AlertTriangle"
        :color="stats.stockAlerts > 0 ? 'red' : 'green'"
        class="stats-card-anim"
        :loading="isLoading"
      />
      <StatsCard
        title="Panier Moyen"
        :value="stats.avgCart"
        :icon="Coins"
        suffix="FCFA"
        color="purple"
        class="stats-card-anim"
        :loading="isLoading"
      />
    </div>

    <!-- Section Graphiques et Commandes Récentes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      
      <!-- Graphique d'Évolution des Revenus -->
      <div class="dashboard-chart bg-white dark:bg-bloom-dark-card rounded-3xl shadow-sm border border-purple-100/60 dark:border-bloom-dark-border p-8 hover:shadow-xl transition-all duration-500">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Évolution des Revenus</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Revenus cumulés sur la période</p>
          </div>
          <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-2xl">
            <TrendingUp class="w-5 h-5 text-purple-600 dark:text-purple-400" />
          </div>
        </div>
        <div class="h-80">
          <Line 
            v-if="revenueChartData.labels.length > 0" 
            :key="chartRenderKey"
            :data="revenueChartData" 
            :options="chartOptions" 
          />
          <div v-else class="h-full flex items-center justify-center text-gray-400 italic text-sm">
            Aucune donnée de vente pour cette période
          </div>
        </div>
      </div>

      <!-- Tableau des Commandes Récentes -->
      <div class="dashboard-table bg-white dark:bg-bloom-dark-card rounded-3xl shadow-sm border border-purple-100/60 dark:border-bloom-dark-border overflow-hidden flex flex-col hover:shadow-xl transition-all duration-500">
        <div class="px-8 py-6 border-b border-gray-100 dark:border-bloom-dark-border flex items-center justify-between bg-purple-50/30 dark:bg-bloom-dark-bg/50">
          <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center space-x-2">
            <span>Commandes Récentes</span>
            <span v-if="stats.recentOrders.length > 0" class="text-xs font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
              {{ stats.recentOrders.length }}
            </span>
          </h2>
          <button @click="router.push('/admin/orders')" class="text-xs font-black uppercase text-purple-600 dark:text-purple-400 hover:text-purple-700 tracking-widest cursor-pointer">
            Tout voir
          </button>
        </div>
        
        <div class="overflow-x-auto flex-1">
          <table class="min-w-full divide-y divide-gray-100 dark:divide-bloom-dark-border">
            <thead class="bg-gray-50/50 dark:bg-bloom-dark-bg/30">
              <tr>
                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Client</th>
                <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
                <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-bloom-dark-border">
              <!-- Chargement Squelette -->
              <tr v-if="isLoading && stats.recentOrders.length === 0" v-for="n in 3" :key="n" class="animate-pulse">
                <td class="px-8 py-4"><div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-24 mb-1"></div><div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-16"></div></td>
                <td class="px-8 py-4"><div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-16 mx-auto"></div></td>
                <td class="px-8 py-4 text-right"><div class="h-6 bg-gray-100 dark:bg-gray-800 rounded-full w-20 ml-auto"></div></td>
              </tr>
              <!-- Aucune commande -->
              <tr v-else-if="stats.recentOrders.length === 0">
                <td colspan="3" class="px-8 py-12 text-center text-gray-400 text-sm font-medium italic">
                  Aucune commande récente
                </td>
              </tr>
              <!-- Liste des commandes -->
              <tr v-else v-for="order in stats.recentOrders" :key="order.id" class="hover:bg-purple-50/30 dark:hover:bg-gray-800/30 transition-colors group cursor-pointer" @click="router.push('/admin/orders')">
                <td class="px-8 py-4 whitespace-nowrap">
                  <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-purple-600 transition-colors">{{ order.user_name || 'Client' }}</div>
                  <div class="text-[10px] text-gray-400 font-medium tracking-tighter uppercase">ID #{{ order.id }} • {{ formatDate(order.created_at) }}</div>
                </td>
                <td class="px-8 py-4 whitespace-nowrap text-center text-sm font-black text-gray-900 dark:text-white">
                  {{ formatNumber(order.total_amount) }} FCFA
                </td>
                <td class="px-8 py-4 whitespace-nowrap text-right">
                  <span :class="getStatusClass(order.status)" class="px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-full">
                    {{ order.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Section Accès Rapide : Meilleurs Produits et Catégories -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Aperçu des Nouveaux Produits (Catalogue) -->
      <div class="dashboard-quick-view lg:col-span-2 bg-white dark:bg-bloom-dark-card rounded-3xl shadow-sm border border-purple-100/60 dark:border-bloom-dark-border p-8 hover:shadow-xl transition-all duration-500">
        <div class="flex items-center justify-between mb-8 ">
          <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
            <LucideBookSearch class="w-5 h-5 mr-3 text-purple-600" />
            Catalogue & Stocks Récents
          </h2>
          <button @click="router.push('/admin/products')" class="p-2 hover:bg-purple-50 dark:hover:bg-gray-800 rounded-xl transition-colors cursor-pointer" title="Voir tous les produits">
            <ArrowRight class="w-5 h-5 text-gray-400 hover:text-purple-600" />
          </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="p in recentProducts.slice(0, 3)" :key="p.id" class="p-4 rounded-2xl bg-purple-50/40 dark:bg-gray-800/30 border border-purple-100/50 dark:border-bloom-dark-border hover:bg-white dark:hover:bg-gray-800 hover:shadow-md transition-all cursor-pointer group" @click="router.push('/admin/products')">
            <div class="w-full aspect-square rounded-xl bg-gray-100 dark:bg-gray-800 mb-3 overflow-hidden">
               <img :src="getImageUrl(p.image_url)" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ p.name }}</h4>
            <div class="flex items-center justify-between mt-1">
              <span class="text-sm text-purple-600 dark:text-purple-400 font-bold">{{ formatNumber(p.price) }} FCFA</span>
              <span :class="(p.stock <= 5 || p.stock_quantity <= 5) ? 'text-rose-500 font-black' : 'text-gray-400 font-medium'" class="text-[10px] uppercase">
                Stock: {{ p.stock ?? p.stock_quantity ?? 0 }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Résumé des Catégories (Carte Prestige) -->
      <div class="dashboard-category bg-gradient-to-br from-slate-900 via-purple-950 to-slate-900 dark:bg-bloom-dark-card rounded-3xl p-8 text-white relative overflow-hidden shadow-xl shadow-purple-950/20">
        <div class="relative z-10 h-full flex flex-col justify-between"> 
          <div>
            <h2 class="text-lg font-bold mb-6 flex items-center">
              <Layers class="w-5 h-5 mr-3 text-purple-300" />
              Catégories de la Boutique
            </h2>
            <div class="space-y-4">
              <div v-for="cat in topCategories" :key="cat.id" class="flex items-center justify-between border-b border-white/10 pb-2">
                <span class="text-sm font-medium text-white/80">{{ cat.name }}</span>
                <span class="px-2 py-0.5 rounded-lg bg-white/10 text-[10px] font-black">{{ cat.product_count || 0 }} items</span>
              </div>
            </div>
          </div>
          <button @click="router.push('/admin/categories')" class="mt-8 w-full py-3 bg-white text-purple-900 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-purple-50 transition-all cursor-pointer">
            Gérer les catégories
          </button>
        </div>
        <!-- Décoration visuelle (Glow effect) -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-purple-600/30 rounded-full blur-3xl"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * Logique du Dashboard d'Administration - Bloom Chloé
 * Gestion réactive et temps réel des statistiques, du graphique des ventes,
 * des alertes stocks et de la synchronisation automatique (SSE + polling fallback).
 */
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useRouter } from 'vue-router'
import adminService from '@/services/adminService.js'
import { useNotifications } from '@/services/notificationService.js'
import StatsCard from '@/components/admin/StatsCard.vue'
import { 
  Package, ShoppingCart, Users, TrendingUp, Calendar, LucideBookSearch, 
  ArrowRight, Layers, Coins, Wallet, Clock, AlertTriangle, 
  Hourglass, RefreshCw
} from 'lucide-vue-next'
import { getProductImageUrl } from '@/utils/imageHelper.js'
import { gsap } from 'gsap'

// Chart.js imports
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

const router = useRouter()
const authStore = useAuthStore()
const { addNotification } = useNotifications()

// Variable globale pour gérer l'EventSource SSE et le Polling
let eventSource = null
let pollingTimer = null

// État réactif des statistiques
const stats = ref({
  totalProducts: 0,
  totalOrders: 0,
  totalRevenue: 0,
  totalClients: 0,
  recentOrders: [],
  monthlySales: [],
  stockAlerts: 0,
  pendingOrders: 0,
  todayOrders: 0,
  avgCart: 0,
  paidInvoices: 0,
  pendingInvoices: 0
})

const isLoading = ref(true)
const isRefreshing = ref(false)
const recentProducts = ref([])
const topCategories = ref([])
const chartRenderKey = ref(0)

// Formateur de la date courante (ex: "Mercredi 17 Septembre 2026")
const currentDate = computed(() => {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  const dateStr = new Date().toLocaleDateString('fr-FR', options)
  return dateStr.charAt(0).toUpperCase() + dateStr.slice(1)
})

let ctxn = null

/**
 * Charge les statistiques réelles depuis l'API backend
 */
const loadStats = async (silent = false) => {
  if (!silent) {
    if (stats.value.totalProducts === 0) {
      isLoading.value = true
    } else {
      isRefreshing.value = true
    }
  }

  try {
    const res = await adminService.getStats()
    const d = res?.stats || res || {}

    stats.value = {
      totalProducts: d.totalProducts ?? d.total_products ?? 0,
      totalOrders: d.totalOrders ?? d.total_orders ?? d.orders_count ?? 0,
      totalRevenue: d.totalRevenue ?? d.total_revenue ?? d.revenue_total ?? 0,
      totalClients: d.totalClients ?? d.total_users ?? d.customers_count ?? 0,
      recentOrders: d.recentOrders ?? d.recent_orders ?? [],
      monthlySales: d.monthlySales ?? d.sales_chart ?? d.monthly_sales ?? [],
      stockAlerts: d.stockAlerts ?? d.stock_alerts ?? 0,
      pendingOrders: d.pendingOrders ?? d.pending_orders ?? 0,
      todayOrders: d.todayOrders ?? d.today_orders ?? 0,
      avgCart: d.avgCart ?? d.avg_cart ?? 0,
      paidInvoices: d.paidInvoices ?? d.paid_invoices ?? 0,
      pendingInvoices: d.pendingInvoices ?? d.pending_invoices ?? 0
    }

    chartRenderKey.value++

    // Charger les produits récents
    const productsRes = await adminService.getProducts({ per_page: 6 })
    if (productsRes && productsRes.products) {
      recentProducts.value = productsRes.products
    }

    // Charger les catégories
    const catsRes = await adminService.getCategories()
    if (catsRes && catsRes.categories) {
      topCategories.value = catsRes.categories.slice(0, 4)
    }

    // Animation GSAP lors du tout premier chargement
    if (!silent && !ctxn) {
      nextTick(() => {
        ctxn = gsap.context(() => {
          const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })
          tl.fromTo('.dashboard-header', 
            { y: -20, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.5 }
          )
          .fromTo('.stats-card-anim', 
            { scale: 0.9, opacity: 0, y: 20 }, 
            { scale: 1, opacity: 1, y: 0, duration: 0.4, stagger: 0.08 }, 
            '-=0.2'
          )
        })
      })
    }

  } catch (err) {
    console.error('Erreur lors du chargement des statistiques du Dashboard:', err)
  } finally {
    isLoading.value = false
    isRefreshing.value = false
  }
}

/**
 * Formate un nombre avec séparateur de milliers
 */
const formatNumber = (num) => {
  return (num || 0).toLocaleString('fr-FR')
}

/**
 * Prépare les données pour le graphique linéaire de revenus
 */
const revenueChartData = computed(() => {
  try {
    let sales = [...(stats.value?.monthlySales || [])]
    
    if (sales.length === 1) {
      sales = [{ month: 'Début', revenue: 0 }, ...sales]
    }
    
    const ctx = document.createElement('canvas').getContext('2d')
    const gradient = ctx.createLinearGradient(0, 0, 0, 300)
    gradient.addColorStop(0, 'rgba(147, 51, 234, 0.35)')
    gradient.addColorStop(1, 'rgba(147, 51, 234, 0)')
    
    return {
      labels: sales.map(s => s.month || ''),
      datasets: [{
        label: 'Revenus (FCFA)',
        data: sales.map(s => parseFloat(s.revenue ?? s.total ?? 0)),
        borderColor: '#9333ea',
        borderWidth: 3,
        pointBackgroundColor: '#fff',
        pointBorderColor: '#9333ea',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 7,
        backgroundColor: gradient,
        fill: true,
        tension: 0.35
      }]
    }
  } catch (e) {
    console.error('Erreur revenueChartData:', e)
    return { labels: [], datasets: [] }
  }
})

// Options du graphique
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      mode: 'index',
      intersect: false,
      backgroundColor: 'rgba(17, 24, 39, 0.9)',
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: 'rgba(255, 255, 255, 0.1)',
      borderWidth: 1,
      padding: 12,
      displayColors: false,
      callbacks: {
        label: function(context) {
          return new Intl.NumberFormat('fr-FR').format(context.raw) + ' FCFA'
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(148, 163, 184, 0.1)',
        drawBorder: false
      },
      ticks: {
        color: '#94a3b8',
        font: { size: 11, weight: 'bold' },
        callback: (value) => value >= 1000 ? (value/1000) + 'k' : value
      }
    },
    x: {
      grid: { display: false },
      ticks: {
        color: '#94a3b8',
        font: { size: 11, weight: 'bold' }
      }
    }
  }
}

/**
 * Détermine la couleur de badge selon le statut de la commande
 */
const getStatusClass = (status) => {
  const s = (status || '').toLowerCase()
  if (s.includes('completed') || s.includes('livré') || s.includes('delivered')) return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'
  if (s.includes('pending') || s.includes('en attente')) return 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'
  if (s.includes('cancel') || s.includes('annulé')) return 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400'
  if (s.includes('shipped') || s.includes('expédié') || s.includes('processing')) return 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400'
  return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
}

/**
 * Formate une date au format court (JJ/MM HH:mm)
 */
const formatDate = (dateString) => {
  if (!dateString) return ''
  const d = new Date(dateString)
  return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }) + ' ' + d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}

/**
 * URL image produit
 */
const getImageUrl = (url) => getProductImageUrl(url)

/**
 * Initialisation du flux SSE (Live updates)
 */
const initSSE = () => {
  const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8080'
  const token = localStorage.getItem('access_token')
  if (!token) return

  if (eventSource) {
    eventSource.close()
  }

  try {
    eventSource = new EventSource(`${apiUrl}/admin/stream.php?token=${token}`)
    
    eventSource.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data)
        
        if (data.type === 'new_order') {
          addNotification({ 
            type: 'success', 
            title: 'Nouvelle commande !',
            message: data.message, 
            duration: 8000 
          })
          loadStats(true)
        } else if (data.type === 'stats_update') {
          loadStats(true)
        }
      } catch (e) {
        console.error('Erreur traitement SSE', e)
      }
    }

    eventSource.addEventListener('auth_error', async () => {
      console.warn('SSE Auth error, rafraîchissement du token...')
      if (eventSource) eventSource.close()
      const newToken = await authStore.refreshToken()
      if (newToken) initSSE()
    })

    eventSource.onerror = () => {
      // Reconnexion automatique gérée nativement par EventSource + polling de secours
    }
  } catch (err) {
    console.warn('Impossible d\'initialiser SSE, le polling prend le relais', err)
  }
}

/**
 * Gestionnaire pour réactualiser lors du retour sur l'onglet
 */
const handleWindowFocus = () => {
  loadStats(true)
}

// Initialisation au montage du composant
onMounted(async () => {
  await loadStats()

  // Démarrage du flux SSE
  setTimeout(() => {
    initSSE()
  }, 1000)

  // Polling automatique de secours toutes les 20 secondes
  pollingTimer = setInterval(() => {
    loadStats(true)
  }, 20000)

  // Rafraîchissement automatique quand l'onglet redevient actif
  window.addEventListener('focus', handleWindowFocus)
})
 
onUnmounted(() => {
  if (ctxn) ctxn.revert()
  if (eventSource) eventSource.close()
  if (pollingTimer) clearInterval(pollingTimer)
  window.removeEventListener('focus', handleWindowFocus)
})
</script>
