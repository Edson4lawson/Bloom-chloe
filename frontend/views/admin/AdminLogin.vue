<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center p-4">
    <!-- Background Effects -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
      <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <div class="relative z-10 w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-10">
        <h1 class="text-4xl font-black text-white tracking-tight mb-2">YuBuy</h1>
        <p class="text-sm text-slate-400 font-medium uppercase tracking-[0.3em]">Espace Administrateur</p>
      </div>

      <!-- Login Card -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-10 shadow-2xl">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Error Alert -->
          <div v-if="error" class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-400 text-sm font-medium text-center">
            {{ error }}
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest pl-2">Adresse email</label>
            <input 
              v-model="email" 
              type="email" 
              required
              placeholder="admin@bloom-chloe.com"
              class="w-full bg-white/5 border-2 border-white/10 focus:border-purple-500/50 rounded-2xl py-4 px-6 text-white font-bold placeholder-slate-600 outline-none transition-all"
            />
          </div>

          <!-- Password -->
          <div class="space-y-2">
            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest pl-2">Mot de passe</label>
            <input 
              v-model="password" 
              type="password" 
              required
              placeholder="••••••••"
              class="w-full bg-white/5 border-2 border-white/10 focus:border-purple-500/50 rounded-2xl py-4 px-6 text-white font-bold placeholder-slate-600 outline-none transition-all"
            />
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            :disabled="loading"
            class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-2xl shadow-purple-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
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
      <div class="text-center mt-6">
        <router-link to="/" class="text-sm text-slate-500 hover:text-white transition-colors font-medium">
          ← Retour au site
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

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
    // Fixed: redirect to /yubuy-manager/dashboard (separate from login route)
    router.push('/yubuy-manager/dashboard')
  } catch (err) {
    error.value = typeof err === 'string' ? err : 'Identifiants invalides'
  } finally {
    loading.value = false
  }
}
</script>
