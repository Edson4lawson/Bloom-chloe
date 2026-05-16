<template>
  <Transition name="slide-right">
    <div v-if="isOpen" class="fixed inset-0 z-[200] overflow-hidden">
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>

      <!-- Drawer Content -->
      <div class="absolute inset-y-0 right-0 max-w-full flex">
        <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
          <!-- Header -->
          <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-pink-50/50">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-xl bg-pink-500 flex items-center justify-center text-white shadow-lg shadow-pink-100">
                <Icon icon="solar:heart-bold" class="w-6 h-6" />
              </div>
              <h2 class="text-xl font-bold text-gray-800">Vos Favoris</h2>
            </div>
            <button @click="$emit('close')" class="p-2 text-gray-400 hover:text-pink-600 hover:bg-white rounded-full transition-all">
              <Icon icon="solar:close-circle-bold" class="w-7 h-7" />
            </button>
          </div>

          <!-- Items List -->
          <div class="flex-1 overflow-y-auto py-6 px-6">
            <div v-if="wishlistStore.isEmpty" class="h-full flex flex-col items-center justify-center text-center space-y-4">
              <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center">
                <Icon icon="solar:heart-angle-linear" class="w-12 h-12 text-gray-300" />
              </div>
              <div>
                <p class="text-xl font-bold text-gray-800">Votre liste est vide</p>
                <p class="text-gray-400">Sauvegardez vos coups de cœur pour plus tard.</p>
              </div>
              <button @click="$emit('close')" class="mt-4 px-8 py-3 bg-pink-500 text-white rounded-full font-bold shadow-lg shadow-pink-100 hover:bg-pink-600 transition-all">
                Découvrir
              </button>
            </div>

            <div v-else class="space-y-6">
              <div v-for="item in wishlistStore.items" :key="item.id" class="flex items-center space-x-4 p-3 rounded-2xl border border-gray-50 hover:bg-gray-50 transition-colors group">
                <div class="w-20 h-20 flex-shrink-0 bg-pink-50 rounded-xl overflow-hidden relative">
                  <img :src="item.image || '/placeholder-perfume.jpg'" :alt="item.name" class="w-full h-full object-cover" />
                  <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                     <button @click="addToCart(item)" class="p-1.5 bg-white rounded-full text-purple-600 hover:text-purple-700 shadow-sm" title="Ajouter au panier">
                        <Icon icon="solar:cart-plus-bold" class="w-5 h-5" />
                     </button>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">{{ item.category }}</span>
                  <h4 class="font-bold text-gray-800 truncate">{{ item.name }}</h4>
                  <p class="text-pink-600 font-bold mb-2">{{ item.price }}€</p>
                </div>
                <button @click="wishlistStore.removeFromWishlist(item.id)" class="p-2 text-gray-300 hover:text-pink-500 transition-colors">
                  <Icon icon="solar:trash-bin-trash-linear" class="w-6 h-6" />
                </button>
              </div>
            </div>
          </div>

          <!-- Footer Actions -->
          <div v-if="!wishlistStore.isEmpty" class="p-6 bg-gray-50/50 border-t border-gray-100 space-y-4">
            <button @click="addAllToCart" class="w-full py-4 bg-white border-2 border-purple-100 text-purple-700 rounded-2xl font-bold flex items-center justify-center space-x-3 hover:bg-purple-50 transition-all">
              <Icon icon="solar:cart-large-minimalistic-bold" class="w-5 h-5" />
              <span>Tout ajouter au panier</span>
            </button>
            <button @click="wishlistStore.clearWishlist" class="w-full py-3 text-gray-400 hover:text-red-500 text-sm font-bold transition-colors">
              Vider la liste
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { Icon } from '@iconify/vue';
import { useWishlistStore } from '../stores/wishlist';
import { useCartStore } from '../stores/cart';
import Swal from 'sweetalert2';

defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close']);

const wishlistStore = useWishlistStore();
const cartStore = useCartStore();

const addToCart = async (item) => {
    await cartStore.addToCart(item);
    Swal.fire({
      icon: 'success',
      title: 'Ajouté au panier',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 1500
    });
};

const addAllToCart = async () => {
    for (const item of wishlistStore.items) {
        await cartStore.addToCart(item);
    }
    Swal.fire({
      icon: 'success',
      title: 'Tout a été ajouté !',
      text: 'Vos produits favoris sont dans le panier.',
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
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-right-enter-from, .slide-right-leave-to {
  opacity: 0;
}
.slide-right-enter-from .w-screen, .slide-right-leave-to .w-screen {
  transform: translateX(100%);
}
</style>
