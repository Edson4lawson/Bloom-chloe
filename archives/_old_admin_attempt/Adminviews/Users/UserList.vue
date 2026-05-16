<template>
  <div class="p-6">
    <!-- Header -->
    <div class="user-header flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Gestion des Clients</h1>
    </div>
  </div>
    




    <!-- Filters -->
    <div class="user-filters bg-white dark:bg-[rgb(43,44,43)] rounded-2xl shadow-sm p-4 mb-6 border border-slate-100 dark:border-slate-500">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input v-model="filters.search" placeholder="Rechercher par nom ou email..." class="border dark:border-slate-500 rounded-lg px-3 py-2 bg-slate-50 dark:bg-slate-900/50 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent/20">
        <select v-model="filters.role" class="border dark:border-slate-500 rounded-lg px-3 py-2 bg-slate-50 dark:bg-slate-900/50 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent/20">
          <option value="">Tous les rôles</option>
          <option value="client">Client</option>
          <option value="admin">Administrateur</option>
        </select>
        <!-- Statut retiré car non présent en BDD pour les users -->
      </div>
    </div>

    <!-- Users Table -->
    <div class="user-table-container bg-white dark:bg-[rgb(43,44,43)] rounded-2xl shadow-sm overflow-hidden border border-slate-100 dark:border-slate-500">
      <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-500">
        <thead class="bg-slate-50 dark:bg-slate-900/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Client</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Rôle</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Commandes</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Total dépensé</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Date d'inscription</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-[rgb(43,44,43)] divide-y divide-slate-100 dark:divide-slate-500">
          <tr v-for="user in filteredUsers" :key="user.id" class="user-row hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                  <span class="text-sm font-medium">
                    {{ user.name?.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-slate-900 dark:text-white">
                    {{ user.name }}
                  </div>
                  <div class="text-sm text-slate-500 dark:text-slate-400">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="user.role === 'admin' 
                ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' 
                : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'" 
                    class="px-2 py-1 text-xs rounded-full">
                {{ user.role === 'admin' ? 'Administrateur' : 'Client' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
              {{ user.total_orders || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
              {{ user.total_spent || 0 }}FCFA
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button @click="viewUserDetail(user)" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                Détails
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- User Detail Modal -->
    <div v-if="selectedUser" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-transparent dark:border-slate-500">
        <div class="sticky top-0 bg-white dark:bg-[rgb(43,44,43)] z-10 px-6 py-4 border-b border-slate-100 dark:border-slate-500 flex justify-between items-center">
          <h2 class="text-xl font-bold text-slate-800 dark:text-white">Détails du client</h2>
          <button @click="selectedUser = null" class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white">
            <X class="w-6 h-6" />
          </button>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-xl border border-slate-100 dark:border-slate-500">
              <h3 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Informations personnelles</h3>
              <div class="space-y-2 text-sm">
                <p class="text-slate-500 dark:text-slate-400">Nom: <span class="font-medium text-slate-900 dark:text-white">{{ selectedUser.name }}</span></p>
                <p class="text-slate-500 dark:text-slate-400">Email: <span class="font-medium text-slate-900 dark:text-white">{{ selectedUser.email }}</span></p>
                <p class="text-slate-500 dark:text-slate-400">Téléphone: <span class="font-medium text-slate-900 dark:text-white">{{ selectedUser.phone || 'N/A' }}</span></p>
                <p class="text-slate-500 dark:text-slate-400">Date d'inscription: <span class="font-medium text-slate-900 dark:text-white">{{ formatDate(selectedUser.created_at) }}</span></p>
              </div>
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-xl border border-slate-100 dark:border-slate-500">
              <h3 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Statistiques</h3>
              <div class="space-y-2 text-sm">
                <p class="text-slate-500 dark:text-slate-400">Nombre de commandes: <span class="font-medium text-slate-900 dark:text-white">{{ selectedUser.total_orders || 0 }}</span></p>
                <p class="text-slate-500 dark:text-slate-400">Total dépensé: <span class="font-medium text-slate-900 dark:text-white">{{ selectedUser.total_spent || 0 }}FCFA</span></p>
                <p class="text-slate-500 dark:text-slate-400">Dernière commande: <span class="font-medium text-slate-900 dark:text-white">{{ selectedUser.last_order_date ? formatDate(selectedUser.last_order_date) : 'Aucune' }}</span></p>
              </div>
            </div>
          </div>
        
        <div class="mt-8" v-if="selectedUser.recent_orders?.length">
          <h3 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Commandes récentes</h3>
          <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-500">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-500">
              <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">ID</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Date</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Montant</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Statut</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-[rgb(43,44,43)] divide-y divide-slate-100 dark:divide-slate-500">
                <tr v-for="order in selectedUser.recent_orders" :key="order.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                  <td class="px-4 py-3 text-sm text-slate-900 dark:text-white">#{{ order.id }}</td>
                  <td class="px-4 py-3 text-sm text-slate-900 dark:text-white">{{ formatDate(order.created_at) }}</td>
                  <td class="px-4 py-3 text-sm text-slate-900 dark:text-white">{{ order.total_amount }}FCFA</td>
                  <td class="px-4 py-3 text-sm">
                    <span :class="getStatusClass(order.status)" class="px-2 py-1 text-xs rounded-full">
                      {{ order.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import adminService from '../../Services/adminService.js'
import { X } from 'lucide-vue-next'
import { gsap } from 'gsap'

const users = ref([])
const selectedUser = ref(null)
const filters = ref({ search: '', role: '', status: '' })

const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const matchesSearch = !filters.value.search || 
      user.name?.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      user.email.toLowerCase().includes(filters.value.search.toLowerCase())
    const matchesRole = !filters.value.role || user.role === filters.value.role
    
    return matchesSearch && matchesRole
  })
})

const loadUsers = async () => {
  try {
    const response = await adminService.getClients()
    if (response.success) {
      users.value = response.clients
    }
  } catch (error) {
    console.error('Erreur lors du chargement des clients:', error)
  }
}

const viewUserDetail = (user) => {
  selectedUser.value = user
}

const toggleUserStatus = async (user) => {
  const action = user.is_active ? 'désactiver' : 'activer'
  if (confirm(`Êtes-vous sûr de vouloir ${action} ce client ?`)) {
    try {
      // Implémenter l'API pour changer le statut utilisateur
      user.is_active = !user.is_active
      // await adminService.toggleUserStatus(user.id, user.is_active)
    } catch (error) {
      console.error('Erreur lors du changement de statut:', error)
      user.is_active = !user.is_active // Revert on error
    }
  }
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    delivered: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR')
}

// --- GSAP Animations ---
let ctx = null

const animateRows = async () => {
  await nextTick()
  if (ctx) ctx.revert()
  
  ctx = gsap.context(() => {
    gsap.from(".user-row", {
      y: 20,
      opacity: 0,
      duration: 0.4,
      stagger: 0.05,
      ease: "power2.out",
      clearProps: "all"
    })
  })
}

watch(filteredUsers, () => {
  animateRows()
})

onMounted(async () => {
  await loadUsers()
  
  const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })
  
  tl.from(".user-header", {
    y: -30,
    opacity: 0,
    duration: 0.8
  })
  .from(".user-filters", {
    y: -20,
    opacity: 0,
    duration: 0.6
  }, "-=0.4")
  .from(".user-table-container", {
    y: 30,
    opacity: 0,
    duration: 0.8
  }, "-=0.4")
  
  animateRows()
})

onUnmounted(() => {
  if (ctx) ctx.revert()
})
</script>
<style scoped>
/* Styles spécifiques si nécessaire */
</style>