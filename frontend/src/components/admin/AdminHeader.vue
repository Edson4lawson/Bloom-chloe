<template>
  <header class="h-16 bg-white dark:bg-[rgb(43,44,43)] border-b border-slate-100 dark:border-slate-700 flex items-center justify-between px-8 shadow-sm">
    <div class="flex items-center gap-4">
      <button @click="$emit('toggle-sidebar')" class="lg:hidden p-2 text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
        <Menu class="w-5 h-5" />
      </button>
      <h2 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">{{ pageTitle }}</h2>
    </div>
    <div class="flex items-center gap-4">
      <ThemeToggle />
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center text-white text-xs font-bold">
          {{ initials }}
        </div>
        <span class="hidden sm:block text-sm font-bold text-slate-700 dark:text-slate-300">{{ userName }}</span>
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
    'AdminOrders': 'Commandes',
    'AdminCustomers': 'Clients',
    'AdminAnalytics': 'Analytiques',
    'AdminSettings': 'Paramètres'
  }
  return titles[route.name] || 'Bloom Manager'
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

