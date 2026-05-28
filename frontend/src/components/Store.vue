<template>
  <div :id="id">
    <div class="relative py-20 flex flex-col items-center justify-center text-center overflow-hidden">
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-32 bg-purple-600/5 blur-[100px] z-0"></div>
      <div class="relative z-10 space-y-4">
        <div class="flex items-center justify-center gap-4 md:gap-8">
          <span class="text-4xl md:text-8xl text-slate-800 font-black tracking-tighter uppercasea" data-aos="fade-right">Nouvel</span>
          <div class="relative" data-aos="fade-left">
            <span class="text-4xl md:text-8xl text-white font-black bg-purple-700 px-6 py-2 block uppercase tracking-tighter transform -rotate-2">Arrivage</span>
            <div class="absolute -top-2 -right-2 w-4 h-4 bg-purple-500 rounded-full animate-ping"></div>
          </div>
        </div>
        <p class="text-xl md:text-2xl text-slate-500 font-medium tracking-widest uppercase py-4" data-aos="fade-up">Collection Exclusive 2026</p>
      </div>
    </div>
    <section class="container mx-auto px-4 md:px-6 py-12 min-h-[600px]">
      <TransitionGroup 
        name="product-fade" 
        tag="div" 
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10"
      >
        <div v-if="arrivals.length === 0" key="empty-state" class="col-span-full py-12 text-center bg-white rounded-[2.5rem] border border-dashed border-slate-200">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <Icon icon="solar:box-minimalistic-linear" class="w-8 h-8 text-slate-300" />
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Aucun nouvel arrivage</h3>
            <p class="text-slate-500 text-sm mb-4">Veuillez vérifier votre connexion ou recharger la page.</p>
            <button @click="window.location.reload()" class="text-purple-600 font-black uppercase tracking-widest text-[10px] hover:underline">Recharger</button>
        </div>

        <div v-for="(product) in arrivals" :key="product.id"
            class="group relative bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 cursor-pointer"
            @click="goToProduct(product)">
          
          <!-- Product Image -->
          <div class="aspect-[4/5] overflow-hidden bg-purple-50">
            <OptimizedImage 
              :key="product.id"
              :src="getProductImageUrl(product.thumbnail || product.image_url)" 
              :alt="product.title"
              imageClass="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
            />
          
            <!-- Quick Add Overlay (Now Wishlist) -->
            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" @click.stop>
              <button @click="toggleWishlist(product)" 
                      class="bg-white text-purple-600 p-4 rounded-full shadow-2xl transform scale-50 group-hover:scale-100 transition-transform duration-500 hover:bg-purple-600 hover:text-white">
                <Icon :icon="wishlistStore.isInWishlist(product.id) ? 'solar:heart-bold' : 'solar:heart-linear'" class="w-8 h-8" />
              </button>
            </div>
          </div>

          <!-- Product Info -->
          <div class="p-6 text-left relative z-20">
            <div class="flex justify-between items-start mb-2">
              <h3 class="text-xl font-bold text-gray-800">{{ product.title }}</h3>
              <span class="text-purple-600 font-black text-xl whitespace-nowrap ml-2">{{ product.price?.toLocaleString('fr-FR') }} FCFA</span>
            </div>
            <p class="text-gray-500 text-sm mb-6 line-clamp-2">{{ product.description }}</p>
            
            <button @click.stop="addToCart(product)" 
                    class="w-full py-3 bg-purple-50 text-purple-700 font-bold rounded-xl hover:bg-purple-600 hover:text-white transition-all flex items-center justify-center space-x-2 relative z-30">
              <Icon icon="solar:add-circle-bold" class="w-5 h-5" />
              <span>Ajouter au panier</span>
            </button>
          </div>

          <!-- New Badge -->
          <div class="absolute top-4 left-4 bg-purple-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-lg">
            Nouveau
          </div>
        </div>
      </TransitionGroup>

      <!-- Pagination Indicators -->
      <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-12">
        <button 
          v-for="i in totalPages" 
          :key="i"
          @click="changePage(i - 1)"
          class="w-3 h-3 rounded-full transition-all duration-300"
          :class="currentPage === i - 1 ? 'bg-purple-600 w-8' : 'bg-purple-200 hover:bg-purple-400'"
        ></button>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import OptimizedImage from './OptimizedImage.vue'
import { useCartStore } from '../stores/cart'
import { useProductStore } from '../stores/products'
import { useWishlistStore } from '../stores/wishlist'
import { getProductImageUrl } from '@/utils/imageHelper'
import Swal from 'sweetalert2'

defineProps({
  id: String
})

const router = useRouter()
const cartStore = useCartStore()
const productStore = useProductStore()
const wishlistStore = useWishlistStore()

const currentPage = ref(0)
const itemsPerPage = 6
let rotationInterval = null

const totalPages = computed(() => {
  const all = productStore.products.filter(p => p.source === 'store')
  return Math.ceil(all.length / itemsPerPage)
})

const arrivals = computed(() => {
  const all = productStore.products.filter(p => p.source === 'store')
  if (all.length === 0) return []
  
  const start = currentPage.value * itemsPerPage
  return all.slice(start, start + itemsPerPage)
})

const startRotation = () => {
  if (rotationInterval) clearInterval(rotationInterval)
  rotationInterval = setInterval(() => {
    if (totalPages.value > 1) {
      currentPage.value = (currentPage.value + 1) % totalPages.value
    }
  }, 30000)
}

const changePage = (index) => {
  currentPage.value = index
  startRotation() // Reset timer on manual click
}

onMounted(() => {
  startRotation()
})

onUnmounted(() => {
  if (rotationInterval) clearInterval(rotationInterval)
})

const goToProduct = (product) => {
  router.push(`/produit/${product.slug || product.id}`)
}

const addToCart = (product) => {
  if (!product) return;
  
  cartStore.addToCart(product)
  
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });

  Toast.fire({
    icon: 'success',
    title: `${product.title} ajouté au panier !`
  });
}

const toggleWishlist = (product) => {
  const added = wishlistStore.toggleWishlist(product);
  
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
}
</script>

<style scoped>
.product-fade-enter-active,
.product-fade-leave-active {
  transition: all 2.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-fade-leave-active {
  position: absolute;
  width: calc(33.333% - 27px);
}

@media (max-width: 1024px) {
  .product-fade-leave-active {
    width: calc(50% - 20px);
  }
}

@media (max-width: 640px) {
  .product-fade-leave-active {
    width: calc(100% - 32px);
  }
}

.product-fade-enter-from {
  opacity: 0;
  transform: translateX(100px);
}

.product-fade-leave-to {
  opacity: 0;
  transform: translateX(-100px);
}

.product-fade-move {
  transition: transform 2.5s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
