<template>
  <Transition name="slide-right">
    <div v-if="isOpen" class="fixed inset-0 z-[200] overflow-hidden">
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>

      <!-- Drawer Content -->
      <div class="absolute inset-y-0 right-0 max-w-full flex">
        <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col border-l border-purple-100">
          <!-- Header -->
          <div class="px-6 py-5 border-b border-purple-100 flex items-center justify-between bg-purple-50/60">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-purple-200">
                <Icon icon="solar:heart-bold" class="w-5 h-5" />
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Vos Favoris</h2>
                <p class="text-[11px] text-gray-500 font-medium">{{ wishlistStore.totalItems }} coup{{ wishlistStore.totalItems > 1 ? 's' : '' }} de cœur</p>
              </div>
            </div>
            <button @click="$emit('close')" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-100/60 rounded-full transition-all">
              <Icon icon="solar:close-circle-bold" class="w-7 h-7" />
            </button>
          </div>

          <!-- Items List -->
          <div class="flex-1 overflow-y-auto py-6 px-6">
            <div v-if="wishlistStore.isEmpty" class="h-full flex flex-col items-center justify-center text-center space-y-4">
              <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center text-purple-300">
                <Icon icon="solar:heart-angle-linear" class="w-10 h-10" />
              </div>
              <div>
                <p class="text-lg font-bold text-gray-800">Votre liste est vide</p>
                <p class="text-xs text-gray-400 mt-1">Sauvegardez vos coups de cœur pour les retrouver à tout moment.</p>
              </div>
              <button @click="$emit('close')" class="mt-2 px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-full font-bold shadow-md shadow-purple-200 hover:shadow-lg transition-all text-xs uppercase tracking-wider">
                Découvrir nos produits
              </button>
            </div>

            <div v-else class="space-y-4">
              <div v-for="item in wishlistStore.items" :key="item.id" class="flex items-center space-x-3.5 p-3 rounded-2xl border border-gray-100 hover:border-purple-200 hover:bg-purple-50/20 transition-all group">
                <div class="w-18 h-18 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-100 relative">
                  <img :src="item.thumbnail || '/placeholder-perfume.jpg'" :alt="item.title" class="w-full h-full object-cover" />
                  <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <button @click="addToCart(item)" class="p-2 bg-white rounded-full text-purple-600 hover:text-purple-700 shadow-md hover:scale-110 transition-all" title="Ajouter au panier">
                      <Icon icon="solar:cart-plus-bold" class="w-4 h-4" />
                    </button>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <span class="text-[9px] uppercase font-black text-purple-600 tracking-wider">{{ item.category }}</span>
                  <h4 class="font-bold text-gray-800 text-sm truncate">{{ item.title }}</h4>
                  <p class="text-purple-600 font-black text-sm my-0.5">{{ Number(item.price).toLocaleString() }} FCFA</p>
                </div>
                <button @click="wishlistStore.removeFromWishlist(item.id)" class="p-2 text-gray-300 hover:text-purple-600 transition-colors" title="Retirer des favoris">
                  <Icon icon="solar:trash-bin-trash-linear" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>

          <!-- Footer Actions -->
          <div v-if="!wishlistStore.isEmpty" class="p-6 bg-gray-50/70 border-t border-gray-100 space-y-3">
            <button @click="addAllToCart" class="w-full py-3.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl font-bold flex items-center justify-center space-x-2 shadow-lg shadow-purple-200 hover:shadow-xl hover:from-purple-700 hover:to-indigo-700 active:scale-98 transition-all text-sm">
              <Icon icon="solar:cart-large-minimalistic-bold" class="w-4 h-4" />
              <span>Tout ajouter au panier</span>
            </button>
            <button @click="wishlistStore.clearWishlist" class="w-full py-2 text-gray-400 hover:text-red-500 text-xs font-bold transition-colors">
              Vider ma liste de favoris
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { toRef } from 'vue';
import { Icon } from '@iconify/vue';
import { useWishlistStore } from '../stores/wishlist';
import { useCartStore } from '../stores/cart';
import { useScrollLock } from '@/composables/useScrollLock';
import Swal from 'sweetalert2';

const props = defineProps({
  isOpen: Boolean
});

// Verrouillage du scroll en arrière-plan
useScrollLock(toRef(props, 'isOpen'));

const emit = defineEmits(['close']);

const wishlistStore = useWishlistStore();
const cartStore = useCartStore();

const addToCart = (item) => {
  cartStore.addToCart(item);
  Swal.fire({
    icon: 'success',
    title: 'Ajouté au panier',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 1500
  });
};

const addAllToCart = () => {
  wishlistStore.items.forEach(item => {
    cartStore.addToCart(item);
  });
  Swal.fire({
    icon: 'success',
    title: 'Favoris ajoutés au panier !',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000
  });
  emit('close');
};
</script>

<style scoped>
.slide-right-enter-active, .slide-right-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-right-enter-from, .slide-right-leave-to {
  opacity: 0;
}
.slide-right-enter-from .w-screen, .slide-right-leave-to .w-screen {
  transform: translateX(100%);
}
</style>
