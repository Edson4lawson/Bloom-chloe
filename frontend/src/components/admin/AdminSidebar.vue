<template>
  <div class="sidebar-container w-64 bg-white dark:bg-bloom-dark-bg text-gray-800 dark:text-white flex flex-col h-screen shadow-xl border-r border-purple-100/60 dark:border-bloom-dark-border transition-all duration-300">
    <!-- Logo -->
    <div class="sidebar-logo p-6 border-b border-purple-100/60 dark:border-bloom-dark-border flex items-center justify-start px-6 gap-3">
      <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-bloom-dark-card flex items-center justify-center overflow-hidden shadow-sm border border-purple-100 dark:border-bloom-dark-border">
        <img :src="bloomIcon" class="w-full h-full object-cover" alt="Bloom Chloé Logo">
      </div>
      <div>
        <h2 class="text-xl font-black tracking-tight text-gray-900 dark:text-white leading-none">Bloom Chloé</h2>
        <p class="text-[9px] text-bloom-purple dark:text-bloom-purple-light uppercase tracking-[0.25em] font-bold mt-1">{{ authStore.user?.role || 'Admin' }}</p>
      </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
      <router-link
        v-for="item in menuItems"
        :key="item.name"
        :to="item.path"
        class="sidebar-item flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group"
        :class="isActive(item.path) 
          ? 'bg-purple-600 text-white shadow-md shadow-purple-500/20' 
          : 'text-gray-600 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-bloom-dark-card hover:text-purple-700 dark:hover:text-white'"
      >
        <component 
          :is="item.icon" 
          class="w-5 h-5 mr-3 transition-colors"
          :class="isActive(item.path) ? 'text-white' : 'text-gray-400 group-hover:text-purple-600 dark:text-gray-400 dark:group-hover:text-white'"
        />
        {{ item.name }}
      </router-link>
    </nav>

    <!-- Bottom Actions -->
    <div class="sidebar-bottom p-4 border-t border-purple-100/60 dark:border-bloom-dark-border space-y-1">
      <router-link 
        to="/" 
        class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-purple-700 dark:hover:text-white transition-colors rounded-xl hover:bg-purple-50 dark:hover:bg-bloom-dark-card"
      >
        <ExternalLink class="w-5 h-5 mr-3" />
        Voir le site
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { computed, onMounted, onUnmounted } from 'vue'
import { gsap } from 'gsap'
import { useAuthStore } from '@/stores/auth'
import bloomIcon from '@/assets/bloom-icone.png'
import {
  LayoutDashboard,
  Package,
  ShoppingCart,
  Users,
  BarChart3,
  Settings,
  ExternalLink
} from 'lucide-vue-next'

const route = useRoute()
const authStore = useAuthStore()

const userRole = computed(() => authStore.user?.role || 'customer')

const menuItems = computed(() => {
  const role = userRole.value

  const allItems = [
    { name: 'Dashboard', path: '/admin/dashboard', icon: LayoutDashboard, roles: ['admin', 'commercial', 'magasinier', 'comptable'] },
    { name: 'Produits', path: '/admin/products', icon: Package, roles: ['admin', 'magasinier'] },
    { name: 'Commandes', path: '/admin/orders', icon: ShoppingCart, roles: ['admin', 'commercial', 'comptable'] },
    { name: 'Clients', path: '/admin/users', icon: Users, roles: ['admin'] },
    { name: 'Analytiques', path: '/admin/analytics', icon: BarChart3, roles: ['admin', 'commercial', 'comptable'] },
    { name: 'Paramètres', path: '/admin/settings', icon: Settings, roles: ['admin'] },
  ]

  return allItems.filter(item => item.roles.includes(role))
})

const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/')
}

let ctx

onMounted(() => {
  ctx = gsap.context(() => {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })
    tl.fromTo('.sidebar-container', { x: -50, opacity: 0 }, { x: 0, opacity: 1, duration: 0.6 })
    tl.fromTo('.sidebar-logo', { scale: 0.8, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.5 }, '-=0.3')
    tl.fromTo('.sidebar-item', { x: -20, opacity: 0 }, { x: 0, opacity: 1, duration: 0.5, stagger: 0.05 }, '-=0.2')
    tl.fromTo('.sidebar-bottom', { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.5 }, '-=0.2')
  })
})

onUnmounted(() => {
  if (ctx) ctx.revert()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
</style>
