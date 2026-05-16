<template>
  <div class="min-h-screen bg-[#fdfaff] flex flex-col items-center justify-center px-4 py-20">
    <div class="text-center">
      <div class="w-32 h-32 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-8">
        <Icon icon="solar:ghost-smile-bold-duotone" class="w-16 h-16 text-purple-400" />
      </div>
      <h1 class="text-6xl font-black text-gray-800 mb-4">404</h1>
      <h2 class="text-2xl font-bold text-gray-600 mb-4">Page introuvable</h2>
      <p class="text-gray-500 mb-8 max-w-md mx-auto">
        La page que vous recherchez n'existe pas ou a été déplacée. Retournez à l'accueil pour continuer votre shopping.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <router-link to="/" class="px-8 py-3 bg-purple-600 text-white rounded-full font-bold hover:bg-purple-700 transition-all shadow-xl shadow-purple-200">
          Retour à l'accueil
        </router-link>
        <router-link to="/boutique" class="px-8 py-3 border-2 border-purple-200 text-purple-700 rounded-full font-bold hover:bg-purple-50 transition-all">
          Voir la boutique
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue';
</script>
