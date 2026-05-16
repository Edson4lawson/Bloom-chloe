<template>
  <Transition name="slide-right">
    <div v-if="isOpen" class="fixed inset-0 z-[200] overflow-hidden">
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>

      <!-- Drawer Content -->
      <div class="absolute inset-y-0 right-0 max-w-full flex">
        <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
          <!-- Header -->
          <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-purple-50/50">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-xl bg-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-100">
                <Icon icon="solar:bag-3-bold" class="w-6 h-6" />
              </div>
              <h2 class="text-xl font-bold text-gray-800">Votre Panier</h2>
            </div>
            <button @click="$emit('close')" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-white rounded-full transition-all">
              <Icon icon="solar:close-circle-bold" class="w-7 h-7" />
            </button>
          </div>

          <!-- Items List -->
          <div class="flex-1 overflow-y-auto py-6 px-6">
            <div v-if="cartStore.items.length === 0" class="h-full flex flex-col items-center justify-center text-center space-y-4">
              <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center">
                <Icon icon="solar:bag-smile-linear" class="w-12 h-12 text-gray-300" />
              </div>
              <div>
                <p class="text-xl font-bold text-gray-800">Votre panier est vide</p>
                <p class="text-gray-400">Découvrez nos produits et faites-vous plaisir !</p>
              </div>
              <button @click="$emit('close')" class="mt-4 px-8 py-3 bg-purple-600 text-white rounded-full font-bold shadow-lg shadow-purple-100 hover:bg-purple-700 transition-all">
                Voir les produits
              </button>
            </div>

            <div v-else class="space-y-6">
              <div v-for="item in cartStore.items" :key="item.id" class="flex items-center space-x-4 p-3 rounded-2xl border border-gray-50 hover:bg-gray-50 transition-colors group">
                <div class="w-20 h-20 flex-shrink-0 bg-purple-100 rounded-xl overflow-hidden">
                  <img :src="item.image || '/placeholder-perfume.jpg'" :alt="item.name" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="font-bold text-gray-800 truncate">{{ item.name }}</h4>
                  <p class="text-purple-600 font-bold mb-2">{{ item.price }}€</p>
                  
                  <!-- Quantity Controls -->
                  <div class="flex items-center space-x-3">
                    <div class="flex items-center border border-gray-200 rounded-lg p-1">
                      <button @click="cartStore.updateQuantity(item.id, item.quantity - 1)" 
                              class="p-1 text-gray-500 hover:text-purple-600 disabled:opacity-30"
                              :disabled="item.quantity <= 1">
                        <Icon icon="solar:minus-circle-linear" class="w-5 h-5" />
                      </button>
                      <span class="w-8 text-center text-sm font-bold text-gray-700">{{ item.quantity }}</span>
                      <button @click="cartStore.updateQuantity(item.id, item.quantity + 1)" 
                              class="p-1 text-gray-500 hover:text-purple-600">
                        <Icon icon="solar:add-circle-linear" class="w-5 h-5" />
                      </button>
                    </div>
                  </div>
                </div>
                <button @click="cartStore.removeFromCart(item.id)" class="p-2 text-gray-300 hover:text-red-500 transition-colors">
                  <Icon icon="solar:trash-bin-trash-linear" class="w-6 h-6" />
                </button>
              </div>
            </div>
          </div>

          <!-- Footer / Checkout -->
          <div v-if="cartStore.items.length > 0" class="p-6 bg-gray-50/50 border-t border-gray-100 space-y-4">
            <div class="space-y-2">
              <div class="flex justify-between text-gray-500 text-sm">
                <span>Sous-total</span>
                <span>{{ cartStore.subtotal }}€</span>
              </div>
              <div class="flex justify-between text-gray-500 text-sm">
                <span>Livraison</span>
                <span>{{ cartStore.shippingFee }}€</span>
              </div>
              <div class="flex justify-between text-gray-800 font-black text-xl pt-2">
                <span>Total</span>
                <span class="text-purple-600">{{ cartStore.cartTotal }}€</span>
              </div>
            </div>
            
            <button class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl font-bold flex items-center justify-center space-x-3 shadow-xl shadow-purple-200 hover:scale-[1.02] active:scale-95 transition-all">
              <span>Passer la commande</span>
              <Icon icon="solar:alt-arrow-right-linear" class="w-5 h-5" />
            </button>
            <p class="text-center text-[10px] text-gray-400">Paiement sécurisé et livraison garantie</p>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { Icon } from '@iconify/vue';
import { useCartStore } from '../stores/cart';

defineProps({
  isOpen: Boolean
});

defineEmits(['close']);

const cartStore = useCartStore();
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
