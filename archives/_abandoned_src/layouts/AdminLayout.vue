<template>
  <div class="flex h-screen bg-slate-50 dark:bg-[#0c0c0e]">
    <AdminSidebar />
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Bar -->
      <header class="h-16 bg-white dark:bg-[rgb(43,44,43)] border-b border-slate-100 dark:border-slate-700 flex items-center justify-between px-8 shadow-sm">
        <div class="flex items-center gap-4">
          <h1 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">YuBuy Manager</h1>
        </div>
        <div class="flex items-center gap-4">
          <ThemeToggle />
          <button @click="handleLogout" class="px-4 py-2 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-rose-600 transition-colors">
            Déconnexion
          </button>
        </div>
      </header>
      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-8">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminSidebar from '@/components/admin/AdminSidebar.vue'
import ThemeToggle from '@/components/admin/ThemeToggle.vue'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/yubuy-manager')
}
</script>
