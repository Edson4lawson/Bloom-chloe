<template>
  <div class="p-6">
    <!-- Header -->
    <div class="order-header flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des Commandes</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Suivez et mettez à jour les commandes de vos clients</p>
      </div>
      <button 
        @click="loadOrders" 
        :disabled="loading"
        class="p-2.5 bg-white dark:bg-bloom-dark-card border border-purple-100/60 dark:border-bloom-dark-border text-gray-600 dark:text-gray-300 rounded-xl hover:bg-purple-50 dark:hover:bg-gray-800 transition-all disabled:opacity-50 cursor-pointer"
        title="Actualiser les commandes"
      >
        <RotateCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
      </button>
    </div>

    <!-- Filters -->
    <div class="order-filters bg-white dark:bg-bloom-dark-card rounded-2xl shadow-sm p-4 mb-6 border border-purple-100/60 dark:border-bloom-dark-border">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input v-model="filters.search" placeholder="Rechercher par ID ou client..." class="border border-gray-200 dark:border-bloom-dark-border rounded-lg px-3 py-2 bg-gray-50 dark:bg-bloom-dark-bg dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
        <select v-model="filters.status" class="border border-gray-200 dark:border-bloom-dark-border rounded-lg px-3 py-2 bg-gray-50 dark:bg-bloom-dark-bg dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
          <option value="">Tous les statuts</option>
          <option value="pending">En attente</option>
          <option value="processing">En traitement</option>
          <option value="shipped">Expédiée</option>
          <option value="completed">Livrée</option>
          <option value="cancelled">Annulée</option>
        </select>
        <select v-model="filters.canal" class="border border-gray-200 dark:border-bloom-dark-border rounded-lg px-3 py-2 bg-gray-50 dark:bg-bloom-dark-bg dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
          <option value="">Tous les canaux</option>
          <option value="site">Site web</option>
          <option value="whatsapp">WhatsApp</option>
        </select>
        <input v-model="filters.date" type="date" class="border border-gray-200 dark:border-bloom-dark-border rounded-lg px-3 py-2 bg-gray-50 dark:bg-bloom-dark-bg dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
      </div>
    </div>

    <!-- Orders Table -->
    <div class="order-table-container bg-white dark:bg-bloom-dark-card rounded-2xl shadow-sm overflow-hidden border border-purple-100/60 dark:border-bloom-dark-border relative">
      <!-- Top Loading Bar -->
      <div v-if="loading" class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-600 animate-pulse z-10"></div>

      <table class="min-w-full divide-y divide-gray-100 dark:divide-bloom-dark-border">
        <thead class="bg-gray-50 dark:bg-bloom-dark-bg/60">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Canal</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Client</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Montant</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Statut</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-bloom-dark-card divide-y divide-gray-100 dark:divide-bloom-dark-border">
          <!-- Loading Skeletons -->
          <tr v-if="loading" v-for="n in 5" :key="'skeleton-' + n" class="animate-pulse">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-12"></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="h-5 bg-gray-100 dark:bg-gray-800 rounded-full w-16"></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap space-y-2">
              <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-32"></div>
              <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-44"></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-20"></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="h-6 bg-gray-100 dark:bg-gray-800 rounded-full w-24"></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-20"></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-14"></div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-else-if="filteredOrders.length === 0">
            <td colspan="7" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400 text-sm font-medium">
              <div class="flex flex-col items-center justify-center space-y-2">
                <ShoppingCart class="w-10 h-10 text-gray-300 dark:text-gray-600 stroke-[1.5]" />
                <p>Aucune commande trouvée</p>
              </div>
            </td>
          </tr>

          <!-- Data Rows -->
          <tr v-else v-for="order in filteredOrders" :key="order.id" class="order-row hover:bg-purple-50/30 dark:hover:bg-gray-800/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
              #{{ order.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getCanalClass(order.canal)" class="px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-full flex items-center gap-1 w-fit">
                <component :is="getCanalIcon(order.canal)" class="w-3 h-3" />
                {{ order.canal || 'site' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900 dark:text-white">{{ order.user_name }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">{{ order.user_email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ order.total_amount?.toLocaleString('fr-FR') }} FCFA</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <select 
                v-model="order.status" 
                @change="updateOrderStatus(order.id, order.status)"
                :class="getStatusClass(order.status)"
                class="text-xs rounded-full px-2 py-1 border-0 cursor-pointer"
              >
                <option value="pending">En attente</option>
                <option value="processing">En traitement</option>
                <option value="shipped">Expédiée</option>
                <option value="completed">Livrée</option>
                <option value="cancelled">Annulée</option>
              </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
              {{ formatDate(order.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button @click="viewOrderDetail(order.id)" class="text-purple-600 hover:text-purple-700 dark:text-purple-400 font-bold cursor-pointer">
                Détails
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Order Details Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-bloom-dark-card rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 border border-purple-100/60 dark:border-bloom-dark-border">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">Détails de la commande #{{ selectedOrder.id }}</h2>
          <button @click="selectedOrder = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="space-y-6">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3 class="font-bold text-sm text-gray-500 dark:text-gray-400 uppercase mb-2">Informations client</h3>
              <div class="bg-purple-50/40 dark:bg-bloom-dark-bg/60 p-4 rounded-xl space-y-2 border border-purple-100/50 dark:border-bloom-dark-border">
                <div class="flex items-center gap-2">
                  <User class="w-4 h-4 text-purple-600" />
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedOrder.user_name }}</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ selectedOrder.user_email }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ selectedOrder.shipping_address }}</div>
              </div>
            </div>

            <div>
              <h3 class="font-bold text-sm text-gray-500 dark:text-gray-400 uppercase mb-2">Détails commande</h3>
              <div class="bg-purple-50/40 dark:bg-bloom-dark-bg/60 p-4 rounded-xl space-y-2 border border-purple-100/50 dark:border-bloom-dark-border">
                <div class="flex justify-between text-xs">
                  <span class="text-gray-500 dark:text-gray-400">Date:</span>
                  <span class="font-medium text-gray-900 dark:text-white">{{ formatDate(selectedOrder.created_at) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                  <span class="text-gray-500 dark:text-gray-400">Statut:</span>
                  <span :class="getStatusClass(selectedOrder.status)" class="px-2 py-0.5 rounded-full text-[10px] font-bold">
                    {{ selectedOrder.status }}
                  </span>
                </div>
                <div class="flex justify-between text-xs">
                  <span class="text-gray-500 dark:text-gray-400">Canal:</span>
                  <span class="font-medium text-gray-900 dark:text-white uppercase">{{ selectedOrder.canal || 'site' }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold border-t border-purple-100 dark:border-bloom-dark-border pt-2 mt-2">
                  <span class="text-gray-900 dark:text-white">Total:</span>
                  <span class="text-purple-600 dark:text-purple-400">{{ selectedOrder.total_amount?.toLocaleString('fr-FR') }} FCFA</span>
                </div>
              </div>
            </div>
          </div>

          <div>
            <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">Articles commandés</h3>
            <div class="overflow-x-auto rounded-xl border border-purple-100/60 dark:border-bloom-dark-border">
              <table class="min-w-full divide-y divide-gray-100 dark:divide-bloom-dark-border">
                <thead class="bg-gray-50 dark:bg-bloom-dark-bg/60">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Produit</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Prix unitaire</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Quantité</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Total</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-bloom-dark-card divide-y divide-gray-100 dark:divide-bloom-dark-border">
                  <tr v-for="item in selectedOrder.items" :key="item.id">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ item.product_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ item.price?.toLocaleString('fr-FR') }} FCFA</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ item.quantity }}</td>
                    <td class="px-4 py-3 text-sm font-bold text-purple-600 dark:text-purple-400">{{ (item.price * item.quantity).toLocaleString('fr-FR') }} FCFA</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import adminService from '@/services/adminService.js'
import { X, User, Package, Globe, MessageSquare, RotateCw, ShoppingCart } from 'lucide-vue-next'
import { gsap } from 'gsap'

const orders = ref([])
const selectedOrder = ref(null)
const filters = ref({ search: '', status: '', date: '', canal: '' })
const loading = ref(true)

const filteredOrders = computed(() => {
  return orders.value.filter(order => {
    const matchesSearch = !filters.value.search || 
      order.id.toString().includes(filters.value.search) ||
      (order.user_name && order.user_name.toLowerCase().includes(filters.value.search.toLowerCase()))
    const matchesStatus = !filters.value.status || order.status === filters.value.status
    const matchesDate = !filters.value.date || 
      (order.created_at && order.created_at.startsWith(filters.value.date))
    const matchesCanal = !filters.value.canal || order.canal === filters.value.canal
    
    return matchesSearch && matchesStatus && matchesDate && matchesCanal
  })
})

const loadOrders = async () => {
  loading.value = true
  try {
    const response = await adminService.getOrders(filters.value)
    orders.value = response.orders || []
  } catch (error) {
    console.error('Erreur chargement commandes:', error)
  } finally {
    loading.value = false
  }
}

const updateOrderStatus = async (orderId, newStatus) => {
  try {
    await adminService.updateOrderStatus(orderId, newStatus)
  } catch (error) {
    console.error('Erreur mise à jour statut:', error)
    await loadOrders()
  }
}

const viewOrderDetail = async (orderId) => {
  try {
    const response = await adminService.getOrder(orderId)
    selectedOrder.value = response.order
  } catch (error) {
    console.error('Erreur chargement détail commande:', error)
  }
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
    processing: 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    shipped: 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
    completed: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
    cancelled: 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
}

const formatDate = (dateString) => {
  if (!dateString) return '—'
  return new Date(dateString).toLocaleDateString('fr-FR')
}

const getCanalClass = (canal) => {
  const classes = {
    site: 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
    whatsapp: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'
  }
  return classes[canal] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
}

const getCanalIcon = (canal) => {
  const icons = {
    site: Globe,
    whatsapp: MessageSquare
  }
  return icons[canal] || Globe
}

// --- GSAP Animations ---
let ctx = null

const animateRows = async () => {
  await nextTick()
  if (ctx) ctx.revert()
  
  ctx = gsap.context(() => {
    if (document.querySelector(".order-row")) {
      gsap.from(".order-row", {
        y: 20,
        opacity: 0,
        duration: 0.4,
        stagger: 0.05,
        ease: "power2.out",
        clearProps: "all"
      })
    }
  })
}

watch(filteredOrders, () => {
  animateRows()
})

onMounted(async () => {
  await loadOrders()
  
  const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })
  
  if (document.querySelector(".order-header")) {
    tl.from(".order-header", { y: -30, opacity: 0, duration: 0.8 })
  }
  if (document.querySelector(".order-filters")) {
    tl.from(".order-filters", { y: -20, opacity: 0, duration: 0.6 }, "-=0.4")
  }
  if (document.querySelector(".order-table-container")) {
    tl.from(".order-table-container", { y: 30, opacity: 0, duration: 0.8 }, "-=0.4")
  }
  
  animateRows()
})

onUnmounted(() => {
  if (ctx) ctx.revert()
})
</script>
<style scoped>
</style>
