<template>
  <div class="space-y-8">
    <div class="sales-header flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Évolution des Ventes</h1>
      <div class="flex bg-white dark:bg-[rgb(43,44,43)] rounded-xl shadow-sm border border-slate-100 dark:border-slate-500 p-1">
        <button 
          v-for="d in [7, 14, 30]" 
          :key="d"
          @click="days = d; fetchStats()"
          class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all"
          :class="days === d ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'"
        >
          {{ d }} jours
        </button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="sales-card bg-white dark:bg-[rgb(43,44,43)] rounded-2xl p-6 border border-slate-100 dark:border-slate-500 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Chiffre d'Affaires ({{ days }}j)</p>
          <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
            <TrendingUp class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
          </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ totalRevenue.toFixed(2) }}FCFA</h3>
      </div>

      <div class="sales-card bg-white dark:bg-[rgb(43,44,43)] rounded-2xl p-6 border border-slate-100 dark:border-slate-500 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Nombre de Commandes</p>
          <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <ShoppingCart class="w-4 h-4 text-blue-600 dark:text-blue-400" />
          </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ totalOrders }}</h3>
      </div>

      <div class="sales-card bg-white dark:bg-[rgb(43,44,43)] rounded-2xl p-6 border border-slate-100 dark:border-slate-500 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Panier Moyen</p>
          <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
            <Activity class="w-4 h-4 text-purple-600 dark:text-purple-400" />
          </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ avgOrder.toFixed(2) }}FCFA</h3>
      </div>
    </div>

    <!-- Detailed Chart -->
    <div class="sales-chart bg-white dark:bg-[rgb(43,44,43)] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-500 p-8">
      <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 italic">Visualisation Graphique du Chiffre d'Affaires</h2>
      <div class="h-96">
        <Line :data="chartData" :options="chartOptions" />
      </div>
    </div>

    <!-- Data Table -->
    <div class="sales-table bg-white dark:bg-[rgb(43,44,43)] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-500 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-500">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Détails Journaliers</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-500">
          <thead class="bg-slate-50 dark:bg-slate-900/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Commandes</th>
              <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Revenu</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-[rgb(43,44,43)] divide-y divide-slate-100 dark:divide-slate-500">
            <tr v-for="item in stats" :key="item.date" class="sales-row hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 font-medium">
                {{ formatDate(item.date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white font-bold">
                {{ item.orders }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white text-right font-bold">
                {{ Number(item.revenue).toFixed(2) }}FCFA
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import adminService from '@/services/adminService'
import { TrendingUp, ShoppingCart, Activity } from 'lucide-vue-next'
import { gsap } from 'gsap'
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
import { Line } from 'vue-chartjs'

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

const days = ref(7)
const stats = ref([])

const fetchStats = async () => {
  try {
    const res = await adminService.getSalesStats(days.value)
    if (res.success) {
      stats.value = res.stats
    }
  } catch (err) {
    console.error(err)
  }
}

const totalRevenue = computed(() => stats.value.reduce((acc, s) => acc + Number(s.revenue), 0))
const totalOrders = computed(() => stats.value.reduce((acc, s) => acc + Number(s.orders), 0))
const avgOrder = computed(() => totalOrders.value > 0 ? totalRevenue.value / totalOrders.value : 0)

const chartData = computed(() => ({
  labels: stats.value.map(s => formatDate(s.date)),
  datasets: [
    {
      label: 'Chiffre d\'Affaires (FCFA)',
      data: stats.value.map(s => Number(s.revenue)),
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      fill: true,
      tension: 0.4,
      pointRadius: 2,
      pointBackgroundColor: '#fff',
      pointBorderWidth: 0.5,
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#1e293b',
      padding: 6,
      cornerRadius: 6
    }
  },
  scales: {
    y: { 
      beginAtZero: true,
      grid: { color: '#334155', drawBorder: false }, // Darker grid color
      ticks: { color: '#94a3b8' }
    },
    x: { 
      grid: { display: false },
      ticks: { color: '#94a3b8' }
    }
  }
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short'
  })
}

// --- GSAP Animations ---
let ctx = null

const animateRows = async () => {
  await nextTick()
  if (ctx) ctx.revert()
  
  ctx = gsap.context(() => {
    gsap.from(".sales-row", {
      y: 20,
      opacity: 0,
      duration: 0.4,
      stagger: 0.05,
      ease: "power2.out",
      clearProps: "all"
    })
  })
}

watch(stats, () => {
  animateRows()
})

onMounted(async () => {
  await fetchStats()
  
  const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })
  
  tl.from(".sales-header", {
    y: -30,
    opacity: 0,
    duration: 0.8
  })
  .from(".sales-card", {
    scale: 0.9,
    y: 30,
    opacity: 0,
    stagger: 0.1,
    duration: 0.6,
    ease: "back.out(1.5)"
  }, "-=0.4")
  .from(".sales-chart", {
    x: -30,
    opacity: 0,
    duration: 0.8
  }, "-=0.4")
  .from(".sales-table", {
    y: 30,
    opacity: 0,
    duration: 0.8
  }, "-=0.6")
  
  animateRows()
})

onUnmounted(() => {
  if (ctx) ctx.revert()
})
</script>
