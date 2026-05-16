<template>
  <Transition name="modal-fade">
    <div v-if="isOpen" class="fixed inset-0 z-[200] mt-0 md:mt-30 flex items-center justify-center px-4 pb-4 pt-24 md:p-6" role="dialog" aria-modal="true">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity" @click="close"></div>

      <!-- Modal Content -->
      <div class="relative w-250 h-130 bg-whitesmoke rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row animate-scale-up ">
        
        <!-- Close Button (Retour) -->
        <button @click="close" class="absolute top-4 right-4 z-30 p-2.5 bg-purple-50 rounded-full text-purple-600 hover:bg-purple-600 hover:text-white transition-all shadow-sm border border-purple-100 group">
          <Icon icon="solar:arrow-left-bold" class="w-6 h-6 transition-transform group-hover:-translate-x-1" />
        </button>

        <!-- Product Image Section (Left) -->
        <div class="w-full md:w-1/2 h-64 md:h-full relative flex-shrink-0 bg-gray-100">
          <OptimizedImage 
            :src="product.thumbnail" 
            :alt="product.title"
            imageClass="w-full h-full object-cover" 
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
          
          <!-- Badges -->
          <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
            <span v-if="product.discount" class="px-3 py-1 bg-purple-600 text-white text-[10px] font-black uppercase tracking-wider rounded-full shadow-lg">
              -{{ product.discount }}%
            </span>
            <span class="px-3 py-1 bg-white/90 backdrop-blur text-purple-900 text-[10px] font-black uppercase tracking-wider rounded-full shadow-lg">
              {{ product.category }}
            </span>
          </div>
        </div>

        <!-- Product Details Section (Right) -->
        <div class="w-full md:w-1/2 flex-1 overflow-y-auto bg-white/80 backdrop-blur-xl p-6 pt-16 md:p-12 md:pt-16 flex flex-col relative">
          <!-- Background Decoration -->
          <div class="absolute top-0 right-0 w-64 h-64 bg-purple-100/50 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

          <div class="mb-auto">
            <div class="flex items-center gap-2 text-yellow-500 mb-3">
              <div class="flex">
                <Icon v-for="i in 5" :key="i" :icon="i <= Math.round(product.rating) ? 'solar:star-bold' : 'solar:star-linear'" class="w-4 h-4 md:w-5 md:h-5" />
              </div>
              <span class="text-xs md:text-sm text-gray-400 font-medium">({{ product.stock }} en stock)</span>
            </div>

            <h2 class="text-2xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4 leading-tight tracking-tight">{{ product.title }}</h2>
            
            <div class="flex items-end gap-3 md:gap-4 mb-6 md:mb-8">
              <span class="text-3xl md:text-4xl font-black text-purple-600">{{ product.price }}Fcfa</span>
              <span v-if="product.discount" class="text-lg md:text-xl text-gray-400 line-through mb-1">
                {{ (product.price * (1 + product.discount/100))}}Fcfa
              </span>
            </div>

            <div class="prose prose-sm md:prose-lg text-gray-600 mb-8 leading-relaxed max-w-none">
              <p>{{ product.description }}</p>
             </div>

            <div class="grid grid-cols-2 gap-3 md:gap-4 mb-8">
              <div class="p-3 md:p-4 rounded-2xl bg-gray-50 border border-gray-100">
                <span class="block text-[10px] md:text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Qualité</span>
                <span class="block text-sm md:text-lg font-bold text-gray-900">100% Authentique</span>
              </div>
              <div class="p-3 md:p-4 rounded-2xl bg-gray-50 border border-gray-100">
                <span class="block text-[10px] md:text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Livraison</span>
                <span class="block text-sm md:text-lg font-bold text-gray-900">Partout au Benin</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-6 border-t border-gray-100 flex flex-col md:flex-row items-stretch md:items-center gap-4 mt-auto">
            <div class="flex items-center justify-between bg-gray-100 rounded-2xl p-2 md:w-auto">
              <button @click="quantity > 1 && quantity--" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-gray-600 shadow-sm hover:text-purple-600 transition-colors">
                <Icon icon="solar:minus-circle-linear" class="w-6 h-6" />
              </button>
              <span class="w-12 text-center font-bold text-gray-900">{{ quantity }}</span>
              <button @click="quantity++" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-gray-600 shadow-sm hover:text-purple-600 transition-colors">
                <Icon icon="solar:add-circle-linear" class="w-6 h-6" />
              </button>
            </div>

            <button @click="handleAddToCart" class="flex-1 flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold py-3.5 px-6 rounded-2xl shadow-xl hover:shadow-2xl hover:scale-[1.02] transition-all active:scale-95">
              <Icon icon="solar:bag-check-bold" class="w-6 h-6" />
              <span>AJOUTER - {{ (product.price * quantity) }} FCFA</span>
            </button>
            
            <button @click="toggleWishlist" class="hidden md:flex p-4 rounded-2xl bg-purple-50 text-purple-500 hover:bg-purple-200 hover:text-purple-600 transition-colors">
               <Icon :icon="wishlistStore.isInWishlist(product.id) ? 'solar:heart-bold' : 'solar:heart-linear'" class="w-6 h-6" />
            </button>
          </div>

        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Icon } from '@iconify/vue';
import OptimizedImage from './OptimizedImage.vue';
import { useCartStore } from '../stores/cart';
import { useWishlistStore } from '../stores/wishlist';
import Swal from 'sweetalert2';

const props = defineProps({
  isOpen: Boolean,
  product: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['close']);
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const quantity = ref(1);

watch(() => props.product, () => {
  quantity.value = 1;
});

const close = () => {
  emit('close');
};

const handleAddToCart = async () => {
  try {
    for(let i=0; i<quantity.value; i++){
        cartStore.addToCart(props.product);
    }
    
    // Success Modal
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    })

    Toast.fire({
      icon: 'success',
      title: `${props.product.title} ajouté au panier!`
    });
    
    close();
  } catch (error) {
    console.error(error);
  }
};

const toggleWishlist = () => {
  const added = wishlistStore.toggleWishlist(props.product);
  
  const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
  });

  if (added) {
    Toast.fire({ icon: 'success', title: 'Ajouté aux favoris' });
  } else {
    Toast.fire({ icon: 'info', title: 'Retiré des favoris' });
  }
};
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.animate-scale-up {
  animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes scaleUp {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>



