<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-slate-800 dark:text-white">Paramètres</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Configuration de votre boutique</p>
      </div>
    </div>

    <!-- Store Info -->
    <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-3xl shadow-sm border border-slate-100 dark:border-slate-500 p-8">
      <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
        <Store class="w-5 h-5 text-accent" />
        Informations de la boutique
      </h2>
      <form @submit.prevent="saveSettings" class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Nom de la boutique</label>
            <input v-model="settings.store_name" class="mt-1 w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 outline-none dark:text-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Email</label>
            <input v-model="settings.store_email" type="email" class="mt-1 w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 outline-none dark:text-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Téléphone</label>
            <input v-model="settings.store_phone" class="mt-1 w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 outline-none dark:text-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Devise</label>
            <input v-model="settings.currency" class="mt-1 w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 outline-none dark:text-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Adresse</label>
            <input v-model="settings.store_address" class="mt-1 w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 outline-none dark:text-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Frais de livraison (FCFA)</label>
            <input v-model.number="settings.shipping_fee" type="number" class="mt-1 w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 outline-none dark:text-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Livraison gratuite à partir de (FCFA)</label>
            <input v-model.number="settings.free_shipping_threshold" type="number" class="mt-1 w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 outline-none dark:text-white" />
          </div>
        </div>
        <div class="flex justify-end">
          <button type="submit" :disabled="saving" class="px-8 py-2.5 bg-accent text-white text-sm font-bold rounded-xl hover:bg-green-700 transition-all disabled:opacity-50">
            {{ saving ? 'Enregistrement...' : 'Sauvegarder' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Security -->
    <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-3xl shadow-sm border border-slate-100 dark:border-slate-500 p-8">
      <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
        <Shield class="w-5 h-5 text-accent" />
        Sécurité
      </h2>
      <div class="space-y-4">
        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/30 rounded-xl">
          <div>
            <h4 class="text-sm font-bold text-slate-800 dark:text-white">Rate Limiting</h4>
            <p class="text-xs text-slate-500">Protection contre les attaques brute-force</p>
          </div>
          <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Actif</span>
        </div>
        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/30 rounded-xl">
          <div>
            <h4 class="text-sm font-bold text-slate-800 dark:text-white">JWT Tokens</h4>
            <p class="text-xs text-slate-500">Authentification par tokens sécurisés</p>
          </div>
          <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Actif</span>
        </div>
        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/30 rounded-xl">
          <div>
            <h4 class="text-sm font-bold text-slate-800 dark:text-white">CORS Policy</h4>
            <p class="text-xs text-slate-500">Origines autorisées : localhost:5173, localhost:3000</p>
          </div>
          <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Actif</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Store, Shield } from 'lucide-vue-next'
import adminService from '@/services/adminService'

const settings = ref({
  store_name: 'Bloom by Chloé',
  store_email: 'egouassangni@mail.com',
  store_phone: '+229 01 56 78 37 70',
  store_address: 'Cotonou, Littoral, Bénin',
  currency: 'FCFA',
  shipping_fee: 2000,
  free_shipping_threshold: 50000
})

const saving = ref(false)

const loadSettings = async () => {
  const result = await adminService.getSettings()
  if (result.success && result.settings) {
    settings.value = { ...settings.value, ...result.settings }
  }
}

const saveSettings = async () => {
  saving.value = true
  try {
    await adminService.updateSettings(settings.value)
  } catch (err) {
    console.error('Settings save error:', err)
  } finally {
    saving.value = false
  }
}

onMounted(loadSettings)
</script>
