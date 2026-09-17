<template>
  <div :id="id" class="relative">
    <div class="relative py-20 flex flex-col items-center justify-center text-center overflow-hidden">
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-32 bg-purple-600/5 blur-[100px] z-0"></div>
      <div class="relative z-10 space-y-4">
        <div class="flex items-center justify-center gap-4 md:gap-8">
          <span class="text-4xl md:text-8xl text-slate-800 font-black tracking-tighter uppercase" data-aos="fade-right">Nouvel</span>
          <div class="relative" data-aos="fade-left">
            <span class="text-4xl md:text-8xl text-white font-black bg-purple-700 px-6 py-2 block uppercase tracking-tighter transform -rotate-2">Arrivage</span>
            <div class="absolute -top-2 -right-2 w-4 h-4 bg-purple-500 rounded-full animate-ping"></div>
          </div>
        </div>
        <p class="text-xl md:text-2xl text-slate-500 font-medium tracking-widest uppercase py-4" data-aos="fade-up">Collection Exclusive 2026</p>
      </div>
    </div>

    <section class="container mx-auto px-4 md:px-6 py-12">
      <div v-if="pages.length === 0" class="py-12 text-center bg-white rounded-[2.5rem] border border-dashed border-slate-200">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
          <Icon icon="solar:box-minimalistic-linear" class="w-8 h-8 text-slate-300" />
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">Aucun nouvel arrivage pour le moment</h3>
        <p class="text-slate-500 text-sm">Consultez notre catalogue pour découvrir toute la collection.</p>
      </div>

      <div 
        v-else 
        class="overflow-hidden w-full relative select-none"
        @mouseenter="pauseRotation"
        @mouseleave="resumeRotation"
      >
        <!-- Horizontal Carousel Track — Hauteur fixe et zéro déplacement de scroll -->
        <div 
          class="flex transition-transform duration-700 ease-[cubic-bezier(0.25,1,0.5,1)]"
          :style="{ transform: `translateX(-${currentPage * 100}%)` }"
        >
          <div 
            v-for="(pageItems, pageIdx) in pages" 
            :key="pageIdx"
            class="w-full shrink-0 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 px-1"
          >
            <div 
              v-for="product in pageItems" 
              :key="product.id"
              class="group relative bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1.5 cursor-pointer flex flex-col justify-between"
              @click="goToProduct(product)"
            >
              <!-- Product Image Section -->
              <div class="aspect-[4/5] overflow-hidden bg-purple-50 relative flex-shrink-0">
                <OptimizedImage 
                  :key="product.id"
                  :src="getProductImageUrl(product.thumbnail || product.image_url)" 
                  :alt="product.title"
                  imageClass="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                />
              
                <!-- Quick Wishlist Overlay -->
                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" @click.stop>
                  <button 
                    @click="toggleWishlist(product)" 
                    class="bg-white text-purple-600 p-4 rounded-full shadow-2xl transform scale-75 group-hover:scale-100 transition-transform duration-300 hover:bg-purple-600 hover:text-white"
                    title="Ajouter aux favoris"
                  >
                    <Icon :icon="wishlistStore.isInWishlist(product.id) ? 'solar:heart-bold' : 'solar:heart-linear'" class="w-7 h-7" />
                  </button>
                </div>

                <!-- New Badge -->
                <div class="absolute top-4 left-4 bg-purple-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-lg">
                  Nouveau
                </div>
              </div>

              <!-- Product Info Section -->
              <div class="p-6 text-left relative z-20 flex-1 flex flex-col justify-between">
                <div>
                  <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-bold text-gray-800 line-clamp-1">{{ product.title }}</h3>
                    <span class="text-purple-600 font-black text-xl whitespace-nowrap ml-2">{{ Number(product.price).toLocaleString('fr-FR') }} FCFA</span>
                  </div>
                  <p class="text-gray-500 text-sm mb-6 line-clamp-2">{{ product.description }}</p>
                </div>
                
                <button 
                  @click.stop="addToCart(product)" 
                  class="w-full py-3.5 bg-purple-50 text-purple-700 font-bold rounded-2xl hover:bg-purple-600 hover:text-white transition-all flex items-center justify-center space-x-2 relative z-30 shadow-sm hover:shadow-md"
                >
                  <Icon icon="solar:add-circle-bold" class="w-5 h-5" />
                  <span>Ajouter au panier</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination Indicators with Timer Progress -->
      <div v-if="pages.length > 1" class="flex items-center justify-center gap-3 mt-12">
        <button 
          v-for="(page, i) in pages" 
          :key="i"
          @click="changePage(i)"
          class="h-3 rounded-full transition-all duration-500 cursor-pointer"
          :class="currentPage === i ? 'bg-purple-600 w-10 shadow-md shadow-purple-200' : 'bg-purple-200 hover:bg-purple-300 w-3'"
          :aria-label="'Aller à la page ' + (i + 1)"
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
let autoPlayTimer = null

const storeProductsList = computed(() => {
  const storeSpecific = productStore.products.filter(p => p.source === 'store' && p.isActive !== false)
  if (storeSpecific.length > 0) return storeSpecific
  return productStore.products.filter(p => p.isActive !== false).slice(0, 12)
})

// Découpe les produits en pages (ex: 6 par 6)
const pages = computed(() => {
  const items = storeProductsList.value
  if (items.length === 0) return []
  const chunks = []
  for (let i = 0; i < items.length; i += itemsPerPage) {
    chunks.push(items.slice(i, i + itemsPerPage))
  }
  return chunks
})

const changePage = (index) => {
  currentPage.value = index
  resetTimer()
}

// Défilement automatique fluide et sans secousse (30 secondes)
const startRotation = () => {
  if (autoPlayTimer) clearInterval(autoPlayTimer)
  autoPlayTimer = setInterval(() => {
    if (pages.value.length > 1) {
      currentPage.value = (currentPage.value + 1) % pages.value.length
    }
  }, 30000) // Rotation toutes les 30 secondes
}

const pauseRotation = () => {
  if (autoPlayTimer) clearInterval(autoPlayTimer)
}

const resumeRotation = () => {
  startRotation()
}

const resetTimer = () => {
  pauseRotation()
  startRotation()
}

onMounted(() => {
  startRotation()
})

onUnmounted(() => {
  if (autoPlayTimer) clearInterval(autoPlayTimer)
})

const goToProduct = (product) => {
  router.push(`/produit/${product.slug || product.id}`)
}

const addToCart = (product) => {
  if (!product) return;
  cartStore.addToCart(product)
  Swal.fire({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    icon: 'success',
    title: `${product.title} ajouté au panier !`
  });
}

const toggleWishlist = (product) => {
  const added = wishlistStore.toggleWishlist(product);
  Swal.fire({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    icon: added ? 'success' : 'info',
    title: added ? 'Ajouté aux favoris' : 'Retiré des favoris'
  });
}
</script>
