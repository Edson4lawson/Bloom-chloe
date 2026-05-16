<template>
    <!-- 
    Tableau de Bord Administrateur (Dashboard)
    Point d'entrée de la gestion YuBuy. Affiche les statistiques clés (KPI),
  -->
  <div class="space-y-8">
    <!-- En-tête contextuel avec Date Dynamique -->
    <div class="dashboard-header opacity-0 flex items-center justify-between space-y-8">
      <div>
        <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight space-y-2">Bonjour {{ authStore.user?.name || 'Admin' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Voici l'activité de votre boutique YuBuy aujourd'hui.</p>
      </div>
      <div class="flex items-center space-x-2 bg-white dark:bg-[rgb(43,44,43)] px-4 py-2 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-500">
        <Calendar class="w-4 h-4 text-slate-400" />
        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ currentDate }}</span>
      </div>
    </div>

    <!-- Section Cartes Statistiques (KPIs) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatsCard
        title="Total Produits"
        :value="stats.totalProducts"
        :icon="Package"
        color="blue"
        class="stats-card-anim opacity-0 mb-4"
      />
      <StatsCard
        title="Commandes"
        :value="stats.totalOrders"
        :icon="ShoppingCart"
        color="green"
        class="stats-card-anim opacity-0 mb-4"
      />
      <StatsCard
        title="Clients"
        :value="stats.totalClients"
        :icon="Users"
        color="purple"
        class="stats-card-anim opacity-0 mb-4"
      />
      <StatsCard
        title="Chiffre d'Affaires"
        :value="stats.totalRevenue"
        :icon="Wallet"
        suffix="FCFA"
        color="yellow"
        class="stats-card-anim opacity-0 mb-4"
      />
    </div>

    <!-- Section Graphiques et Tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 space-y-8">
      
      <!-- Graphique d'Évolution des Revenus (Utilise Chart.js via vue-chartjs) -->
      <div class="dashboard-chart opacity-0 bg-white dark:bg-[rgb(43,44,43)] rounded-3xl shadow-sm border border-slate-100 dark:border-slate-500 p-8 hover:shadow-xl transition-all duration-500">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Évolution des Revenus</h2>
            <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Revenus mensuels cumulés</p>
          </div>
          <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl">
            <TrendingUp class="w-5 h-5 text-blue-500" />
          </div>
        </div>
        <div class="h-80">
          <Line :data="revenueChartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Tableau des Commandes Récentes -->
      <div class="dashboard-table opacity-0 bg-white dark:bg-[rgb(43,44,43)] rounded-3xl shadow-sm border border-slate-100 dark:border-slate-500 overflow-hidden flex flex-col hover:shadow-xl transition-all duration-500">
        <div class="px-8 py-6 border-b border-slate-50 dark:border-slate-500 flex items-center justify-between bg-slate-50/30 dark:bg-slate-900/20 ">
          <h2 class="text-lg font-bold text-slate-800 dark:text-white">Commandes Récentes</h2>
          <button @click="router.push('/yubuy-manager/orders')" class="text-xs font-black uppercase text-blue-600 dark:text-blue-400 hover:text-blue-700 tracking-widest">
            Tout voir
          </button>
        </div>
        <div class="overflow-x-auto flex-1">
          <table class="min-w-full divide-y divide-slate-50 dark:divide-slate-500">
            <thead class="bg-slate-50/50 dark:bg-slate-900/30">
              <tr>
                <th class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Client</th>
                <th class="px-8 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Montant</th>
                <th class="px-8 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-500">
              <tr v-for="order in stats.recentOrders" :key="order.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors group">
                <td class="px-8 py-4 whitespace-nowrap">
                  <div class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-blue-600 transition-colors">{{ order.user_name }}</div>
                  <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium tracking-tighter uppercase">ID #{{ order.id }} • {{ formatDate(order.created_at) }}</div>
                </td>
                <td class="px-8 py-4 whitespace-nowrap text-center text-sm font-black text-slate-800 dark:text-white">
                  {{ order.total_amount }}FCFA
                </td>
                <td class="px-8 py-4 whitespace-nowrap text-right">
                  <span :class="getStatusClass(order.status)" class="px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-full">
                    {{ order.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="stats.recentOrders.length === 0">
                <td colspan="3" class="px-8 py-10 text-center text-slate-400 text-sm italic">Aucune commande récente</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Section Accès Rapide : Derniers Produits et Catégories -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Aperçu des Nouveaux Produits -->
      <div class="dashboard-quick-view opacity-0 mt-8 lg:col-span-2 bg-white dark:bg-[rgb(43,44,43)] rounded-3xl shadow-sm border border-slate-100 dark:border-slate-500 p-8 hover:shadow-xl transition-all duration-500 ">
        <div class="flex items-center justify-between mb-8 ">
          <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center">
            <LucideBookSearch class="w-5 h-5 mr-3 text-slate-700 dark:text-accent" />
             Catalogue Produit
          </h2>
          <button @click="router.push('/yubuy-manager/products')" class="p-2 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-colors">
            <ArrowRight class="w-5 h-5 text-slate-400 dark:text-slate-500 hover:text-accent" />
          </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="p in recentProducts.slice(0, 3)" :key="p.id" class="p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-500 hover:bg-white dark:hover:bg-slate-700 hover:shadow-md transition-all cursor-pointer group" @click="router.push('/yubuy-manager/products')">
            <div class="w-full aspect-square rounded-xl bg-slate-200 dark:bg-[rgb(43,44,43)] mb-3 overflow-hidden">
               <img :src="getImageUrl(p.main_image)" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>
            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ p.name }}</h4>
            <div class="flex items-center justify-between mt-1">
              <span class="text-sm text-blue-600 dark:text-blue-400 font-bold">{{ p.price }}FCFA</span>
              <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-tighter">{{ p.category_name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Résumé des Catégories (Carte Noire Premium) -->
      <div class="dashboard-category opacity-0 bg-black dark:bg-slate-950 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl shadow-slate-900/20 mt-8">
        <div class="relative z-10 h-full flex flex-col"> 
          <h2 class="text-lg font-bold mb-6 flex items-center">
            <Layers class="w-5 h-5 mr-3 text-white/60" />
            Catégories
          </h2>
          <div class="space-y-4 flex-1">
            <div v-for="cat in categories.slice(0, 5)" :key="cat.id" class="flex items-center justify-between border-b border-white/5 pb-2">
              <span class="text-sm font-medium text-white/80">{{ cat.name }}</span>
              <span class="px-2 py-0.5 rounded-lg bg-white/10 text-[10px] font-black">{{ cat.product_count }} items</span>
            </div>
          </div>
          <button @click="router.push('/yubuy-manager/categories')" class="mt-8 w-full py-3 bg-white text-black text-xs font-black uppercase tracking-widest rounded-xl hover:bg-slate-100 transition-all">
            Gérer les rayons
          </button>
        </div>
        <!-- Décoration visuelle (Blur effect) -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-accent/70 rounded-full blur-3xl text-accent"></div>
      </div>
    </div>
  </div>
</template>


<script setup>
/**
 * Logique du Dashboard d'Administration
 * Gère le chargement des statistiques globales, le rendu des graphiques de vente
 * et les raccourcis vers la gestion du catalogue.
 */
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useRouter } from 'vue-router'
import adminService from '@/services/adminService.js'
import StatsCard from '@/components/admin/StatsCard.vue'
import { 
  Package, ShoppingCart, Users, Euro, 
  TrendingUp, Calendar, LucideBookSearch, ArrowRight, Layers, 
  Banknote,
  Coins,
  DollarSign,
  CircleDollarSign,
  Wallet
} from 'lucide-vue-next'
// Imports Chart.js pour les graphiques
import {
  Chart as ChartJS, CategoryScale, LinearScale, PointElement, 
  LineElement, BarElement, Title, Tooltip, Legend, Filler
} from 'chart.js'
import { Line } from 'vue-chartjs'
import { gsap } from 'gsap'
import { getProductImageUrl } from '@/utils/imageHelper'

// Enregistrement des composants nécessaires pour Chart.js
ChartJS.register(
  CategoryScale, LinearScale, PointElement, LineElement, 
  BarElement, Title, Tooltip, Legend, Filler
)

const router = useRouter()
const authStore = useAuthStore()

// État des statistiques globales (initialisé avec des valeurs vides)
const stats = ref({
  totalProducts: 0, totalOrders: 0, totalClients: 0, 
  totalRevenue: 0, recentOrders: [], monthlySales: []
})
const recentProducts = ref([])
const categories = ref([])

let ctxn = null;

// Date formatée affichée en haut (ex: "lundi 4 février")
const currentDate = computed(() => {
  return new Date().toLocaleDateString('fr-FR', { 
    weekday: 'long', day: 'numeric', month: 'long' 
  })
})

/**
 * Charge les données consolidées depuis l'API Admin
 */
const loadStats = async () => {
  try {
    const response = await adminService.getStats()
    if (response.success) stats.value = response.stats
    
    // Chargement complémentaire pour les aperçus produits et catégories
    const [prodRes, catRes] = await Promise.all([
      adminService.getProducts(),
      adminService.getCategories()
    ]);

    if (prodRes.success) recentProducts.value = prodRes.products
    if (catRes.success) categories.value = catRes.categories
    
    // Lancer l'animation une fois les données chargées
    await nextTick();
    runAnimations();

  } catch (error) {
    console.error('Erreur Dashboard Admin:', error)
  }
}

/**
 * Exécute les animations GSAP
 */
const runAnimations = () => {
  if (ctxn) ctxn.revert();
  
  ctxn = gsap.context(() => {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out', duration: 0.8 } });

    // 1. Header
    tl.to(".dashboard-header", {
      y: 0,
      opacity: 1
    })
    .from(".dashboard-header", {
        y: -30,
        immediateRender: false
    }, "<")

    // 2. Stats Cards
    tl.to(".stats-card-anim", {
      scale: 1,
      y: 0,
      opacity: 1,
      stagger: 0.1,
      ease: "back.out(1.7)"
    }, "-=0.4")
    .from(".stats-card-anim", {
        scale: 0.9,
        y: 30,
        immediateRender: false
    }, "<")

    // 3. Main Content (Chart & Table)
    tl.to(".dashboard-chart", {
      x: 0,
      opacity: 1,
      duration: 1
    }, "-=0.6")
    .from(".dashboard-chart", {
        x: -50,
        immediateRender: false
    }, "<")

    tl.to(".dashboard-table", {
      x: 0,
      opacity: 1,
      duration: 1
    }, "-=0.8")
    .from(".dashboard-table", {
        x: 50,
        immediateRender: false
    }, "<")


    // 4. Products & Categories (Bottom)
    tl.to(".dashboard-quick-view", {
      y: 0,
      opacity: 1,
      duration: 0.8
    }, "-=0.6")
    .from(".dashboard-quick-view", {
        y: 50,
        immediateRender: false
    }, "<")

    tl.to(".dashboard-category", {
      scale: 1,
      opacity: 1,
      duration: 1,
      ease: "elastic.out(1, 0.7)"
    }, "-=0.6")
    .from(".dashboard-category", {
        scale: 0.95,
        immediateRender: false
    }, "<")
  });
}

/**
 * Formate un nombre vers le format monétaire français (espace pour les milliers)
 */
const formatNumber = (num) => {
  return new Intl.NumberFormat('fr-FR').format(num)
}

/**
 * Prépare les données pour le graphique linéaire de revenus
 */
const revenueChartData = computed(() => {
  const sales = stats.value.monthlySales || []
  return {
    labels: sales.map(s => s.month),
    datasets: [{
      label: 'Revenus (FCFA)',
      backgroundColor: 'rgba(59, 130, 246, 0.05)',
      borderColor: '#3b82f6',
      pointBackgroundColor: '#3b82f6',
      data: sales.map(s => s.revenue),
      fill: true,
      tension: 0.4 // Courbe lissée (smooth)
    }]
  }
})

// Options de configuration esthétique du graphique
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { 
      backgroundColor: '#1e293b', 
      padding: 12, 
      cornerRadius: 12,
      bodyFont: { weight: 'bold' }
    }
  },
  scales: {
    y: { 
      beginAtZero: true, 
      grid: { drawBorder: false, color: '#f1f5f9' },
      ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
    },
    x: { 
      grid: { display: false },
      ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
    }
  }
}

/**
 * Détermine la couleur de badge selon le statut de la commande
 */
const getStatusClass = (status) => {
  const s = status.toLowerCase()
  if (s.includes('completed') || s.includes('livré') || s.includes('delivered')) return 'bg-emerald-50 text-emerald-600'
  if (s.includes('pending') || s.includes('en attente')) return 'bg-amber-50 text-amber-600'
  if (s.includes('cancel')) return 'bg-rose-50 text-rose-600'
  return 'bg-blue-50 text-blue-600'
}

/**
 * Formate une date au format court (JJ/MM)
 */
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' })
}

/**
 * Construit l'URL absolue de l'image pour l'administration
 */
const getImageUrl = (url) => getProductImageUrl(url)

// Initialisation au montage du composant
onMounted(loadStats)

onUnmounted(() => {
  if (ctxn) ctxn.revert();
})
</script>
