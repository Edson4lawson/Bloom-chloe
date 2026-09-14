<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-daba-navy dark:text-white">Gestion des Clients</h1>
        <p class="text-sm text-daba-slate dark:text-daba-slate-dark">{{ customers.length }} clients enregistrés</p>
      </div>
      <div class="flex items-center gap-3">
        <button 
          @click="loadCustomers" 
          :disabled="loading"
          class="p-2.5 bg-daba-cream dark:bg-[rgb(43,44,43)] border border-daba-cream-alt dark:border-slate-500 text-daba-slate dark:text-slate-300 rounded-xl hover:bg-daba-cream-alt dark:hover:bg-slate-700 transition-all disabled:opacity-50"
          title="Actualiser les clients"
        >
          <RotateCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
        </button>
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-daba-slate-dark" />
          <input v-model="searchQuery" type="text" placeholder="Rechercher un client..."
            class="pl-10 pr-4 py-2.5 bg-daba-cream dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-xl text-sm focus:ring-2 focus:ring-daba-orange/20 outline-none dark:text-white w-64" />
        </div>
      </div>
    </div>

    <div class="bg-daba-cream dark:bg-[rgb(43,44,43)] rounded-3xl border border-daba-cream-alt dark:border-slate-500 overflow-hidden shadow-sm relative">
      <!-- Top Loading Bar -->
      <div v-if="loading" class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-daba-orange via-daba-navy to-daba-orange animate-pulse z-10"></div>

      <table class="w-full">
        <thead>
          <tr class="bg-daba-cream-alt dark:bg-daba-dark-card/30 text-left">
            <th class="px-6 py-4 text-[10px] font-black text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Client</th>
            <th class="px-6 py-4 text-[10px] font-black text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Email</th>
            <th class="px-6 py-4 text-[10px] font-black text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Inscrit le</th>
            <th class="px-6 py-4 text-[10px] font-black text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Rôle</th>
            <th class="px-6 py-4 text-[10px] font-black text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loading Skeletons -->
          <tr v-if="loading" v-for="n in 5" :key="'skeleton-' + n" class="border-t border-daba-cream-alt dark:border-slate-700 animate-pulse">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-daba-cream-alt dark:bg-slate-700"></div>
                <div class="h-4 bg-daba-cream-alt dark:bg-slate-700 rounded w-28"></div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-daba-cream-alt dark:bg-slate-700 rounded w-36"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-daba-cream-alt dark:bg-slate-700 rounded w-20"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-5 bg-daba-cream-alt dark:bg-slate-700 rounded-full w-16"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-daba-cream-alt dark:bg-slate-700 rounded w-20"></div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-else-if="filteredCustomers.length === 0">
            <td colspan="5" class="px-6 py-16 text-center text-daba-slate dark:text-slate-400 text-sm font-medium">
              <div class="flex flex-col items-center justify-center space-y-2">
                <Users class="w-10 h-10 text-daba-slate-dark dark:text-slate-500 stroke-[1.5]" />
                <p>Aucun client trouvé</p>
              </div>
            </td>
          </tr>

          <!-- Data Rows -->
          <tr v-else v-for="customer in filteredCustomers" :key="customer.id" class="border-t border-daba-cream-alt dark:border-slate-700 hover:bg-daba-cream-alt/50 dark:hover:bg-slate-700/20 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3 cursor-pointer hover:bg-daba-cream-alt dark:hover:bg-slate-800/20 rounded-lg p-2 transition-colors" @click="viewCustomerOrders(customer)">
                <div class="w-9 h-9 rounded-full bg-daba-orange/10 flex items-center justify-center text-daba-orange text-xs font-bold uppercase">
                  {{ (customer.first_name || customer.email || '?').charAt(0) }}
                </div>
                <p class="text-sm font-bold text-daba-navy dark:text-white">{{ customer.first_name || '' }} {{ customer.last_name || '' }}</p>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-daba-slate dark:text-daba-slate-dark">{{ customer.email }}</td>
            <td class="px-6 py-4 text-sm text-daba-slate dark:text-daba-slate-dark">{{ formatDate(customer.created_at) }}</td>
            <td class="px-6 py-4">
              <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded-full"
                :class="customer.role === 'admin' ? 'bg-daba-cream-alt text-daba-orange dark:bg-daba-orange/20 dark:text-daba-orange' : 'bg-daba-cream-alt text-daba-navy dark:bg-daba-navy/20 dark:text-daba-slate-dark'">
                {{ customer.role || 'client' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <button @click="toggleRole(customer)"
                class="text-xs font-bold text-daba-orange hover:text-daba-orange/80 transition-colors">
                {{ customer.role === 'admin' ? 'Rétrograder' : 'Promouvoir Admin' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal des commandes du client -->
    <div v-if="selectedCustomer" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-daba-cream dark:bg-daba-dark-card rounded-3xl max-w-2xl w-full p-6 border border-daba-cream-alt dark:border-daba-dark-border max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-lg font-black text-daba-navy dark:text-white">
              Commandes de {{ selectedCustomer.first_name }} {{ selectedCustomer.last_name }}
            </h3>
            <p class="text-xs text-daba-slate dark:text-daba-slate-dark">{{ selectedCustomer.email }}</p>
          </div>
          <button @click="selectedCustomer = null" class="p-2 text-daba-slate hover:text-daba-navy dark:hover:text-white">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div v-if="loadingOrders" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-2 border-daba-cream-alt border-t-daba-orange"></div>
        </div>

        <div v-else-if="customerOrders.length === 0" class="text-center py-12 text-daba-slate dark:text-daba-slate-dark">
          Aucune commande pour ce client
        </div>

        <div v-else class="space-y-3">
          <div v-for="order in customerOrders" :key="order.id" 
            class="p-4 bg-daba-cream-alt/50 dark:bg-daba-dark-card/50 rounded-2xl border border-daba-cream-alt dark:border-daba-dark-border">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-2">
                <span class="font-bold text-sm text-daba-navy dark:text-white">#{{ order.id }}</span>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full" :class="getStatusClass(order.status)">
                  {{ order.status }}
                </span>
              </div>
              <span class="text-sm font-bold text-daba-navy dark:text-white">{{ order.total_amount }} FCFA</span>
            </div>
            <div class="text-xs text-daba-slate dark:text-daba-slate-dark">
              {{ formatDate(order.created_at) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Search, RotateCw, Users, X } from 'lucide-vue-next'
import adminService from '@/services/adminService'

const customers = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCustomer = ref(null)
const customerOrders = ref([])
const loadingOrders = ref(false)

const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  const q = searchQuery.value.toLowerCase()
  return customers.value.filter(c =>
    (c.first_name || '').toLowerCase().includes(q) ||
    (c.last_name || '').toLowerCase().includes(q) ||
    (c.email || '').toLowerCase().includes(q)
  )
})

const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' })
}

const toggleRole = async (customer) => {
  const newRole = customer.role === 'admin' ? 'client' : 'admin'
  try {
    await adminService.updateCustomerRole(customer.id, newRole)
    customer.role = newRole
  } catch (err) {
    console.error('Role update error:', err)
  }
}

const viewCustomerOrders = async (customer) => {
  selectedCustomer.value = customer
  loadingOrders.value = true
  customerOrders.value = []
  
  try {
    const result = await adminService.getOrders({ user_id: customer.id, per_page: 50 })
    if (result.success || result.orders) {
      customerOrders.value = result.orders || []
    }
  } catch (err) {
    console.error('Error loading customer orders:', err)
    customerOrders.value = []
  } finally {
    loadingOrders.value = false
  }
}

const getStatusClass = (status) => {
  const s = (status || '').toLowerCase()
  if (s.includes('completed') || s.includes('livré') || s.includes('delivered')) return 'bg-daba-cream-alt text-daba-green'
  if (s.includes('pending') || s.includes('en attente')) return 'bg-daba-cream-alt text-daba-orange'
  if (s.includes('cancel')) return 'bg-daba-cream-alt text-rose-600'
  return 'bg-daba-cream-alt text-daba-orange'
}

const loadCustomers = async () => {
  loading.value = true
  try {
    const result = await adminService.getCustomers()
    customers.value = result.customers || []
  } catch (err) {
    console.error('Load customers error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCustomers()
})
</script>
