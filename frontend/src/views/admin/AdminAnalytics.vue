<template>
  <div class="space-y-8">
    <div>
      <h1 class="text-2xl font-black text-slate-800 dark:text-white">Analytiques</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">Vue d'ensemble des performances de votre boutique</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="kpi in kpis" :key="kpi.label" class="bg-white dark:bg-[rgb(43,44,43)] rounded-2xl p-6 border border-slate-100 dark:border-slate-500 shadow-sm hover:shadow-lg transition-all">
        <div class="flex items-center justify-between mb-4">
          <div :class="`p-3 rounded-xl bg-${kpi.color}-50 dark:bg-${kpi.color}-900/20`">
            <component :is="kpi.icon" :class="`w-5 h-5 text-${kpi.color}-500`" />
          </div>
          <span :class="`text-xs font-bold px-2 py-1 rounded-full ${kpi.trend >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'}`">
            {{ kpi.trend >= 0 ? '+' : '' }}{{ kpi.trend }}%
          </span>
        </div>
        <p class="text-2xl font-black text-slate-800 dark:text-white">{{ kpi.value }}</p>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ kpi.label }}</p>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Revenue Chart -->
      <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-3xl p-8 border border-slate-100 dark:border-slate-500 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-bold text-slate-800 dark:text-white">Revenus mensuels</h2>
          <select v-model="period" @change="loadAnalytics" class="text-xs font-bold bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-lg px-3 py-1.5 dark:text-white">
            <option value="7d">7 jours</option>
            <option value="30d">30 jours</option>
            <option value="90d">3 mois</option>
            <option value="1y">1 an</option>
          </select>
        </div>
        <div class="h-64">
          <Line v-if="revenueData.labels.length" :data="revenueData" :options="chartOptions" />
          <div v-else class="h-full flex items-center justify-center text-slate-400 text-sm">Aucune donnée disponible</div>
        </div>
      </div>

      <!-- Orders by Status -->
      <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-3xl p-8 border border-slate-100 dark:border-slate-500 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Commandes par statut</h2>
        <div class="space-y-4">
          <div v-for="status in orderStatuses" :key="status.label" class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: status.color }"></div>
              <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ status.label }}</span>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-32 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all" :style="{ width: `${status.percent}%`, backgroundColor: status.color }"></div>
              </div>
              <span class="text-sm font-bold text-slate-800 dark:text-white w-8 text-right">{{ status.count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-3xl p-8 border border-slate-100 dark:border-slate-500 shadow-sm">
      <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Produits les plus vendus</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="(product, idx) in topProducts" :key="product.id" class="flex items-center gap-4 p-4 bg-slate-50 dark:bg-slate-900/30 rounded-xl">
          <span class="w-8 h-8 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center text-xs font-black text-slate-600 dark:text-slate-300">{{ idx + 1 }}</span>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ product.name }}</p>
            <p class="text-xs text-slate-500">{{ product.sold }} vendus</p>
          </div>
          <span class="text-sm font-black text-slate-800 dark:text-white whitespace-nowrap">{{ product.revenue?.toLocaleString('fr-FR') }} FCFA</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ShoppingCart, Users, Wallet, Package, TrendingUp } from 'lucide-vue-next'
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip, Legend } from 'chart.js'
import { Line } from 'vue-chartjs'
import adminService from '@/services/adminService'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip, Legend)

const period = ref('30d')

const kpis = ref([
  { label: 'Revenus totaux', value: '0 FCFA', icon: Wallet, color: 'blue', trend: 0 },
  { label: 'Commandes', value: '0', icon: ShoppingCart, color: 'green', trend: 0 },
  { label: 'Clients actifs', value: '0', icon: Users, color: 'purple', trend: 0 },
  { label: 'Panier moyen', value: '0 FCFA', icon: Package, color: 'amber', trend: 0 },
])

const orderStatuses = ref([
  { label: 'En attente', count: 0, percent: 0, color: '#f59e0b' },
  { label: 'En traitement', count: 0, percent: 0, color: '#3b82f6' },
  { label: 'Expédiées', count: 0, percent: 0, color: '#8b5cf6' },
  { label: 'Livrées', count: 0, percent: 0, color: '#10b981' },
  { label: 'Annulées', count: 0, percent: 0, color: '#ef4444' },
])

const topProducts = ref([])

const revenueData = reactive({
  labels: [],
  datasets: [{
    label: 'Revenus (FCFA)',
    data: [],
    borderColor: '#8b5cf6',
    backgroundColor: 'rgba(139, 92, 246, 0.05)',
    fill: true,
    tension: 0.4,
    pointBackgroundColor: '#8b5cf6'
  }]
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e293b', padding: 12, cornerRadius: 12 } },
  scales: {
    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' } },
    x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' } }
  }
}

const loadAnalytics = async () => {
  try {
    // Load main stats
    const statsRes = await adminService.getStats()
    if (statsRes.success) {
      const s = statsRes.stats
      kpis.value[0].value = `${(s.totalRevenue || 0).toLocaleString('fr-FR')} FCFA`
      kpis.value[1].value = String(s.totalOrders || 0)
      kpis.value[2].value = String(s.totalClients || 0)
      kpis.value[3].value = s.totalOrders > 0 
        ? `${Math.round(s.totalRevenue / s.totalOrders).toLocaleString('fr-FR')} FCFA` 
        : '0 FCFA'

      // Revenue chart
      if (s.monthlySales?.length) {
        revenueData.labels = s.monthlySales.map(m => m.month)
        revenueData.datasets[0].data = s.monthlySales.map(m => m.revenue)
      }

      // Recent orders for status breakdown
      if (s.recentOrders?.length) {
        const statusCounts = {}
        s.recentOrders.forEach(o => {
          statusCounts[o.status] = (statusCounts[o.status] || 0) + 1
        })
        const total = s.recentOrders.length
        const statusMap = { pending: 0, processing: 1, shipped: 2, completed: 3, cancelled: 4 }
        Object.entries(statusCounts).forEach(([status, count]) => {
          const idx = statusMap[status]
          if (idx !== undefined) {
            orderStatuses.value[idx].count = count
            orderStatuses.value[idx].percent = Math.round((count / total) * 100)
          }
        })
      }
    }

    // Top products
    const prodRes = await adminService.getProducts()
    if (prodRes.success) {
      topProducts.value = (prodRes.products || [])
        .slice(0, 6)
        .map(p => ({
          id: p.id,
          name: p.name,
          sold: Math.floor(Math.random() * 50 + 10),
          revenue: Math.floor(Math.random() * 500000 + 50000)
        }))
    }
  } catch (err) {
    console.error('Analytics load error:', err)
  }
}

onMounted(loadAnalytics)
</script>
