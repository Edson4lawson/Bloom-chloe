<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-slate-800 dark:text-white">Gestion des Clients</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ customers.length }} clients enregistrés</p>
      </div>
      <div class="relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input v-model="searchQuery" type="text" placeholder="Rechercher un client..."
          class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl text-sm focus:ring-2 focus:ring-accent/20 outline-none dark:text-white w-64" />
      </div>
    </div>

    <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-3xl border border-slate-100 dark:border-slate-500 overflow-hidden shadow-sm">
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-accent"></div>
      </div>

      <table v-else class="w-full">
        <thead>
          <tr class="bg-slate-50 dark:bg-slate-900/30 text-left">
            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Client</th>
            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Email</th>
            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Inscrit le</th>
            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rôle</th>
            <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in filteredCustomers" :key="customer.id" class="border-t border-slate-50 dark:border-slate-700 hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-accent/10 flex items-center justify-center text-accent text-xs font-bold uppercase">
                  {{ (customer.first_name || customer.email || '?').charAt(0) }}
                </div>
                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ customer.first_name || '' }} {{ customer.last_name || '' }}</p>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ customer.email }}</td>
            <td class="px-6 py-4 text-xs text-slate-500">{{ formatDate(customer.created_at) }}</td>
            <td class="px-6 py-4">
              <span :class="customer.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600'"
                class="px-2 py-1 text-[10px] font-bold rounded-full uppercase">{{ customer.role || 'client' }}</span>
            </td>
            <td class="px-6 py-4">
              <button @click="toggleRole(customer)" class="text-xs font-bold text-accent hover:text-green-700 transition-colors">
                {{ customer.role === 'admin' ? 'Retirer admin' : 'Rendre admin' }}
              </button>
            </td>
          </tr>
          <tr v-if="filteredCustomers.length === 0">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">Aucun client trouvé</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Search } from 'lucide-vue-next'
import adminService from '@/services/adminService'

const customers = ref([])
const loading = ref(true)
const searchQuery = ref('')

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

onMounted(async () => {
  loading.value = true
  try {
    const result = await adminService.getCustomers()
    customers.value = result.customers || []
  } catch (err) {
    console.error('Load customers error:', err)
  } finally {
    loading.value = false
  }
})
</script>
