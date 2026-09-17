<template>
  <div class="min-h-screen bg-purple-50/50 dark:bg-gray-900 flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
      <div class="mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/30 mb-6">
          <Icon icon="lucide:shield-alert" class="w-10 h-10 text-red-600 dark:text-red-400" />
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-2">Accès non autorisé</h1>
        <p class="text-gray-600 dark:text-gray-400">
          Vous n'avez pas les permissions nécessaires pour accéder à cette page.
        </p>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-purple-100 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-center gap-2 mb-4">
          <Icon icon="lucide:user" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
          <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
            Votre rôle : <span class="font-bold text-purple-600 dark:text-purple-400">{{ userRole }}</span>
          </span>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500">
          Contactez votre administrateur si vous pensez qu'il s'agit d'une erreur.
        </p>
      </div>

      <div class="space-y-3">
        <router-link
          to="/admin/dashboard"
          class="inline-flex items-center justify-center w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg shadow-purple-200 dark:shadow-none"
        >
          <Icon icon="lucide:layout-dashboard" class="w-5 h-5 mr-2" />
          Retour au Dashboard
        </router-link>
        <router-link
          to="/"
          class="inline-flex items-center justify-center w-full px-6 py-3 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl transition-all duration-200 border border-gray-200 dark:border-gray-700"
        >
          <Icon icon="lucide:home" class="w-5 h-5 mr-2" />
          Retour à l'accueil
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const userRole = computed(() => authStore.user?.role || 'Non connecté')
</script>
