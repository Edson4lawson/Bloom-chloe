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
                <Icon icon="solar:bag-3-bold" class="w-5 h-5" />
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Votre Panier</h2>
                <p class="text-[11px] text-gray-500 font-medium">{{ cartStore.totalItems }} article{{ cartStore.totalItems > 1 ? 's' : '' }}</p>
              </div>
            </div>
            <button @click="$emit('close')" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-100/60 rounded-full transition-all">
              <Icon icon="solar:close-circle-bold" class="w-7 h-7" />
            </button>
          </div>

          <!-- Items List -->
          <div class="flex-1 overflow-y-auto py-6 px-6">
            <div v-if="cartStore.items.length === 0" class="h-full flex flex-col items-center justify-center text-center space-y-4">
              <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center text-purple-300">
                <Icon icon="solar:bag-smile-linear" class="w-10 h-10" />
              </div>
              <div>
                <p class="text-lg font-bold text-gray-800">Votre panier est vide</p>
                <p class="text-xs text-gray-400 mt-1">Découvrez notre collection et faites-vous plaisir !</p>
              </div>
              <button @click="$emit('close')" class="mt-2 px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-full font-bold shadow-md shadow-purple-200 hover:shadow-lg transition-all text-xs uppercase tracking-wider">
                Découvrir nos produits
              </button>
            </div>

            <div v-else class="space-y-4">
              <div v-for="item in cartStore.items" :key="item.id" class="flex items-center space-x-3.5 p-3 rounded-2xl border border-gray-100 hover:border-purple-200 hover:bg-purple-50/20 transition-all group">
                <div class="w-18 h-18 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-100">
                  <img :src="item.thumbnail || '/placeholder-perfume.jpg'" :alt="item.title" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="font-bold text-gray-800 text-sm truncate">{{ item.title }}</h4>
                  <p class="text-purple-600 font-black text-sm my-1">{{ Number(item.price).toLocaleString() }} FCFA</p>
                  
                  <!-- Quantity Controls -->
                  <div class="flex items-center space-x-2">
                    <div class="flex items-center border border-gray-200 rounded-lg bg-gray-50/50 p-0.5">
                      <button @click="cartStore.updateQuantity(item.id, item.quantity - 1)" 
                              class="p-1 text-gray-500 hover:text-purple-600 disabled:opacity-30 transition-colors"
                              :disabled="item.quantity <= 1">
                        <Icon icon="solar:minus-linear" class="w-3.5 h-3.5" />
                      </button>
                      <span class="w-7 text-center text-xs font-bold text-gray-800">{{ item.quantity }}</span>
                      <button @click="cartStore.updateQuantity(item.id, item.quantity + 1)" 
                              class="p-1 text-gray-500 hover:text-purple-600 transition-colors">
                        <Icon icon="solar:add-linear" class="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </div>
                </div>
                <button @click="cartStore.removeFromCart(item.id)" class="p-2 text-gray-300 hover:text-red-500 transition-colors" title="Supprimer">
                  <Icon icon="solar:trash-bin-trash-linear" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>

          <!-- Footer / Checkout -->
          <div v-if="cartStore.items.length > 0" class="p-6 bg-gray-50/70 border-t border-gray-100 space-y-4">
            <div class="space-y-2">
              <div class="flex justify-between text-gray-500 text-xs">
                <span>Sous-total</span>
                <span class="font-bold text-gray-700">{{ Number(cartStore.subtotal).toLocaleString() }} FCFA</span>
              </div>
              <div class="flex justify-between text-gray-500 text-xs">
                <span>Livraison</span>
                <span class="font-bold text-emerald-600">{{ cartStore.shippingFee ? Number(cartStore.shippingFee).toLocaleString() + ' FCFA' : 'Gratuite' }}</span>
              </div>
              <div class="flex justify-between text-gray-900 font-black text-lg pt-2 border-t border-gray-200">
                <span>Total</span>
                <span class="text-purple-600">{{ Number(cartStore.cartTotal).toLocaleString() }} FCFA</span>
              </div>
            </div>
            
            <button @click="openPaymentModal" class="w-full py-3.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl font-bold flex items-center justify-center space-x-2 shadow-lg shadow-purple-200 hover:shadow-xl hover:from-purple-700 hover:to-indigo-700 active:scale-98 transition-all text-sm">
              <span>Passer la commande</span>
              <Icon icon="solar:alt-arrow-right-linear" class="w-4 h-4" />
            </button>
            <p class="text-center text-[10px] text-gray-400 font-medium">Paiement sécurisé Mobile Money MTN, Moov, Celtis & Carte</p>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, toRef } from 'vue';
import { Icon } from '@iconify/vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import { orderService } from '../services/api';
import { useScrollLock } from '@/composables/useScrollLock';
import Swal from 'sweetalert2';

const props = defineProps({
  isOpen: Boolean
});

// Verrouillage du scroll en arrière-plan lorsque le panier est ouvert
useScrollLock(toRef(props, 'isOpen'));

const emit = defineEmits(['close', 'request-login', 'open-payment', 'request-payment-after-login']);

const cartStore = useCartStore();
const authStore = useAuthStore();

const isSubmitting = ref(false);

const openPaymentModal = async () => {
  if (cartStore.items.length === 0) {
    Swal.fire({
      title: 'Panier vide',
      text: 'Votre panier est vide. Ajoutez des produits avant de passer commande.',
      icon: 'warning',
      confirmButtonColor: '#9333ea'
    });
    return;
  }

  // Vérifier si l'utilisateur est connecté
  if (!authStore.isAuthenticated) {
    emit('close');
    
    Swal.fire({
      title: 'Identification Requise',
      text: 'Veuillez vous connecter ou créer un compte pour procéder au paiement.',
      icon: 'info',
      confirmButtonText: 'Se connecter',
      confirmButtonColor: '#9333ea',
      showCancelButton: true,
      cancelButtonText: 'Continuer mes achats',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        emit('request-payment-after-login');
        emit('request-login');
      }
    });
    return;
  }

  if (isSubmitting.value) return;
  isSubmitting.value = true;

  emit('close');
  
  Swal.fire({
    title: 'Traitement...',
    text: 'Création de votre commande en cours',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  try {
    const shippingAddress =
      (authStore.user?.address && authStore.user.address.trim()) ||
      (authStore.user?.city && authStore.user.city.trim()) ||
      'Cotonou, Bénin';

    const orderData = {
      shipping_address: shippingAddress,
      payment_method: 'mobile_money',
      items: cartStore.items.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.price
      })),
      customer_note: ''
    };

    const response = await orderService.create(orderData);
    
    if (response.data?.order_id) {
      Swal.close();
      emit('open-payment', {
        amount: cartStore.cartTotal,
        orderId: response.data.order_id
      });
    } else {
      throw new Error('Erreur lors de la création de la commande');
    }
  } catch (err) {
    Swal.fire({
      title: 'Erreur',
      text: err.response?.data?.error || 'Impossible de créer la commande. Veuillez réessayer.',
      icon: 'error',
      confirmButtonColor: '#9333ea'
    });
  } finally {
    isSubmitting.value = false;
  }
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
