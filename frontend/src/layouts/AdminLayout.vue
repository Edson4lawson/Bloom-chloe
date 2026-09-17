<template>
  <div class="flex h-screen bg-purple-50/30 dark:bg-bloom-dark-bg">
    <AdminSidebar />
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Bar -->
      <header class="h-16 bg-white dark:bg-bloom-dark-card border-b border-purple-100/60 dark:border-bloom-dark-border flex items-center justify-between px-8 shadow-sm">
        <div class="flex items-center gap-4">
          <h1 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Bloom Chloé <span class="text-bloom-purple capitalize font-semibold text-sm">({{ authStore.user?.role || 'Admin' }})</span></h1>
        </div>
        <div class="flex items-center gap-4">
          <ThemeToggle />
          <button @click="handleLogout" class="px-4 py-2 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-bloom-purple transition-colors">
            Déconnexion
          </button>
        </div>
      </header>
      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-8">
        <router-view />
      </main>
    </div>
    <!-- Global Notifications -->
    <NotificationContainer />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminSidebar from '@/components/admin/AdminSidebar.vue'
import ThemeToggle from '@/components/admin/ThemeToggle.vue'
import NotificationContainer from '@/components/admin/NotificationContainer.vue'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/admin/login')
}
</script>
