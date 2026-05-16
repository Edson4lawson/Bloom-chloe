<template>
  <div class="min-h-screen bg-[#fdfaff] flex items-center justify-center px-4 py-20">
    <div class="text-center max-w-lg">
      <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
        <Icon icon="solar:check-circle-bold" class="w-14 h-14 text-emerald-600" />
      </div>
      <h1 class="text-3xl font-black text-gray-800 mb-3">Commande confirmée ! 🎉</h1>
      <p class="text-gray-500 mb-2">Merci pour votre achat chez <span class="font-bold text-purple-600">Bloom by Chloé</span>.</p>
      <p class="text-gray-500 mb-8">Votre commande <span class="font-bold text-gray-800">#{{ orderId }}</span> a bien été enregistrée.</p>
      
      <div class="bg-white rounded-2xl p-6 border border-purple-50 shadow-sm mb-8">
        <div class="flex items-center justify-center gap-8 text-sm">
          <div class="text-center">
            <Icon icon="solar:box-minimalistic-bold-duotone" class="w-8 h-8 text-purple-600 mx-auto mb-1" />
            <span class="text-gray-500">En préparation</span>
          </div>
          <div class="w-12 h-0.5 bg-gray-200"></div>
          <div class="text-center opacity-40">
            <Icon icon="solar:delivery-bold-duotone" class="w-8 h-8 text-purple-600 mx-auto mb-1" />
            <span class="text-gray-500">En livraison</span>
          </div>
          <div class="w-12 h-0.5 bg-gray-200"></div>
          <div class="text-center opacity-40">
            <Icon icon="solar:check-circle-bold-duotone" class="w-8 h-8 text-purple-600 mx-auto mb-1" />
            <span class="text-gray-500">Livré</span>
          </div>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <router-link to="/mon-compte" class="px-6 py-3 bg-purple-600 text-white rounded-full font-bold hover:bg-purple-700 transition-all">
          Suivre ma commande
        </router-link>
        <router-link to="/boutique" class="px-6 py-3 border-2 border-purple-200 text-purple-700 rounded-full font-bold hover:bg-purple-50 transition-all">
          Continuer mes achats
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { Icon } from '@iconify/vue';

const route = useRoute();
const orderId = route.params.orderId || '—';
</script>
