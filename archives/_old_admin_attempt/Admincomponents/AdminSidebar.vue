<template>
  <div class="sidebar-container w-64 bg-black dark:bg-[rgb(43,44,43)] text-white flex flex-col h-screen shadow-xl border-r border-transparent dark:border-slate-500 transition-all duration-300">
    <!-- Logo -->
    <div class="sidebar-logo p-6 border-b border-slate-800 dark:border-slate-500 flex justify-center">
      <img src="/img/logo.webp" class="w-24 object-contain brightness-0 invert" alt="Logo Yubuy">
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
      <router-link
        v-for="item in menuItems"
        :key="item.name"
        :to="item.path"
        class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
        :class="isActive(item.path) 
          ? 'bg-accent/50 text-white shadow-lg shadow-accent/30' 
          : 'text-slate-400 hover:bg-slate-800 dark:hover:bg-slate-700/50 hover:text-white'"
      >
        <component 
          :is="item.icon" 
          class="w-5 h-5 mr-3 transition-colors"
          :class="isActive(item.path) ? 'text-white' : 'text-slate-500 group-hover:text-white'"
        />
        {{ item.name }}
      </router-link>
    </nav>

    <!-- Bottom Actions -->
    <div class="sidebar-bottom p-4 border-t border-slate-800 dark:border-slate-500">
      <router-link 
        to="/" 
        class="flex items-center px-4 py-3 text-sm font-medium text-slate-400 hover:text-white transition-colors"
      >
        <ExternalLink class="w-5 h-5 mr-3" />
        Voir le site
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { onMounted, onUnmounted } from 'vue'
import { gsap } from 'gsap'
import { 
  LayoutDashboard, 
  Package, 
  Tag, 
  ShoppingCart, 
  Users, 
  Layers, 
  Mail, 
  TrendingUp,
  ExternalLink
} from 'lucide-vue-next'

const route = useRoute()

const menuItems = [
  { name: 'Dashboard', path: '/yubuy-manager/dashboard', icon: LayoutDashboard },
  { name: 'Produits', path: '/yubuy-manager/products', icon: Package },
  { name: 'Offres', path: '/yubuy-manager/offres', icon: Tag },
  { name: 'Commandes', path: '/yubuy-manager/orders', icon: ShoppingCart },
  { name: 'Catégories', path: '/yubuy-manager/categories', icon: Layers },
  { name: 'Clients', path: '/yubuy-manager/users', icon: Users },
  { name: 'Ventes', path: '/yubuy-manager/sales', icon: TrendingUp },
  { name: 'Newsletter', path: '/yubuy-manager/newsletter', icon: Mail },
]

const isActive = (path) => {
  return route.path === path
}

let ctx;

onMounted(() => {
  ctx = gsap.context(() => {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })

    // Sidebar Container Entrance
    tl.fromTo('.sidebar-container', 
      { x: -50, opacity: 0 },
      { x: 0, opacity: 1, duration: 0.6 }
    )

    // Logo Animation
    tl.fromTo('.sidebar-logo', 
      { scale: 0.8, opacity: 0 },
      { scale: 1, opacity: 1, duration: 0.5 }, 
      '-=0.3'
    )

    // Menu Items Stagger
    tl.fromTo('.sidebar-item', 
      { x: -20, opacity: 0 },
      { x: 0, opacity: 1, duration: 0.5, stagger: 0.05 },
      '-=0.2'
    )

    // Bottom Action
    tl.fromTo('.sidebar-bottom', 
      { y: 20, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.5 },
      '-=0.2'
    )
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
  background: #334155;
  border-radius: 10px;
}
</style>
