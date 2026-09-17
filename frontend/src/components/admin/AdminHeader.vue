<template>
  <header class="h-16 bg-white dark:bg-bloom-dark-card border-b border-purple-100/60 dark:border-bloom-dark-border flex items-center justify-between px-8 shadow-sm">
    <div class="flex items-center gap-4">
      <button @click="$emit('toggle-sidebar')" class="lg:hidden p-2 text-gray-500 hover:text-gray-800 dark:hover:text-white rounded-lg hover:bg-purple-50 dark:hover:bg-slate-800 transition-colors">
        <Menu class="w-5 h-5" />
      </button>
      <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">{{ pageTitle }}</h2>
    </div>
    <div class="flex items-center gap-4">
      <ThemeToggle />
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
          {{ initials }}
        </div>
        <span class="hidden sm:block text-sm font-bold text-gray-700 dark:text-gray-300">{{ userName }}</span>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { Menu } from 'lucide-vue-next'
import ThemeToggle from './ThemeToggle.vue'

defineEmits(['toggle-sidebar'])

const route = useRoute()

const pageTitle = computed(() => {
  const titles = {
    'AdminDashboard': 'Dashboard',
    'AdminProducts': 'Produits',
    'AdminStock': 'Stock',
    'AdminOrders': 'Commandes',
    'AdminCustomers': 'Clients',
    'AdminInvoices': 'Factures',
    'AdminAnalytics': 'Analytiques',
    'AdminSettings': 'Paramètres'
  }
  return titles[route.name] || 'Bloom Chloé'
})

const userName = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.first_name || user.email || 'Admin'
})

const initials = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  if (user.first_name) return user.first_name.charAt(0).toUpperCase()
  if (user.email) return user.email.charAt(0).toUpperCase()
  return 'A'
})
</script>
