<template>
  <div class="min-h-screen bg-purple-50/40 dark:bg-bloom-dark-bg flex items-center justify-center p-4 transition-colors duration-500">
    <!-- Background Effects -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
      <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-400/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <div class="relative z-10 w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-10">
        <div class="inline-flex items-center gap-3 mb-4">
           <img :src="bloomIcon" class="w-12 h-12 rounded-xl shadow-lg border border-purple-100 dark:border-bloom-dark-border" alt="Bloom Chloé">
           <div class="text-left">
             <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-none">Bloom Chloé</h1>
             <p class="text-[10px] text-purple-600 dark:text-purple-400 font-black uppercase tracking-[0.3em] mt-1">Administration</p>
           </div>
        </div>
      </div>

      <!-- Login Card -->
      <div class="bg-white dark:bg-bloom-dark-card border border-purple-100 dark:border-bloom-dark-border rounded-3xl p-10 shadow-2xl shadow-purple-500/10 dark:shadow-none">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Error Alert -->
          <div v-if="error" class="p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-2xl text-rose-600 dark:text-rose-400 text-sm font-bold text-center">
            {{ error }}
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <label class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400 tracking-widest pl-2">Adresse email</label>
            <input
              v-model="email"
              type="email"
              required
              placeholder="admin@bloomchloe.com"
              class="w-full bg-purple-50/40 dark:bg-bloom-dark-bg/50 border-2 border-purple-100 dark:border-bloom-dark-border focus:border-purple-600 rounded-2xl py-4 px-6 text-gray-900 dark:text-white font-bold placeholder-gray-400 outline-none transition-all"
            />
          </div>

          <!-- Password -->
          <div class="space-y-2">
            <label class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400 tracking-widest pl-2">Mot de passe</label>
            <input 
              v-model="password" 
              type="password" 
              required
              placeholder="••••••••"
              class="w-full bg-purple-50/40 dark:bg-bloom-dark-bg/50 border-2 border-purple-100 dark:border-bloom-dark-border focus:border-purple-600 rounded-2xl py-4 px-6 text-gray-900 dark:text-white font-bold placeholder-gray-400 outline-none transition-all"
            />
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            :disabled="loading"
            class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-purple-500/25 dark:shadow-none hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
          >
            <span v-if="!loading">Se connecter</span>
            <span v-else class="flex items-center justify-center gap-2">
              <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
              Connexion...
            </span>
          </button>
        </form>
      </div>

      <!-- Back to site -->
      <div class="text-center mt-8">
        <router-link to="/" class="text-sm text-gray-500 hover:text-purple-600 dark:hover:text-white transition-colors font-bold">
          ← Retour à la boutique
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import bloomIcon from '@/assets/bloom-icone.png'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref(null)

const handleLogin = async () => {
  loading.value = true
  error.value = null
  
  try {
    await authStore.login(email.value, password.value)
    router.push('/admin/dashboard')
  } catch (err) {
    error.value = typeof err === 'string' ? err : 'Identifiants invalides'
  } finally {
    loading.value = false
  }
}
</script>
