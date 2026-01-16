<template>
  <section class="py-12" id="products" data-aos="fade-up">
    <div class="container mx-auto px-4">
      <!-- En-tête avec titre et filtre -->
      <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6" data-aos="fade-up" data-aos-delay="100">
        <div class="text-center md:text-left">
          <h2 class="text-4xl md:text-6xl font-black text-gray-800 mb-2 leading-tight">Nos <span class="text-purple-600">Collections</span></h2>
          <p class="text-lg text-gray-500 font-medium">L'art de la parfumerie d'exception</p>
        </div>
        
        <!-- Filtres -->
        <div class="flex items-center w-full md:w-auto">
          <div class="relative w-full md:w-auto">
            <button @click="toggleSortDropdown" 
                    class="w-full md:w-48 flex items-center justify-between px-6 py-4 bg-white border border-purple-100 rounded-2xl shadow-sm hover:shadow-md transition-all">
              <span class="font-semibold text-gray-700">{{ selectedSort.label }}</span>
              <Icon icon="solar:alt-arrow-down-linear" class="w-5 h-5 text-purple-600" />
            </button>
            
            <!-- Menu déroulant de tri -->
            <Transition name="fade">
              <div v-if="showSortDropdown" 
                   class="absolute right-0 mt-3 w-full md:w-56 bg-white rounded-2xl shadow-2xl z-20 border border-purple-50 py-2 overflow-hidden">
                <div v-for="option in sortOptions" 
                     :key="option.value"
                     @click="sortProducts(option.value)"
                     class="px-6 py-3 text-sm font-medium hover:bg-purple-50 cursor-pointer transition-colors"
                     :class="{ 'text-purple-600 bg-purple-50/50': selectedSort.value === option.value, 'text-gray-600': selectedSort.value !== option.value }">
                  {{ option.label }}
                </div>
              </div>
            </Transition>
          </div>
        </div>
      </div>

      <!-- Grille de produits -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <div v-for="(product, index) in displayedProducts" :data-aos="'fade-up'" :data-aos-delay="100 * (index % 4 + 1)" 
             :key="product.id" 
             @click="$emit('select-product', product)"
             class="group relative bg-white/40 backdrop-blur-md rounded-3xl overflow-hidden border border-white/60 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 cursor-pointer">
          
          <!-- Badge de promotion -->
          <div v-if="product.discount" 
               class="absolute top-4 right-4 bg-pink-500 text-white text-[10px] font-black px-3 py-1.5 rounded-full z-10 shadow-lg uppercase tracking-wider">
            -{{ product.discount }}%
          </div>
          
          <!-- Image du produit -->
          <div class="relative overflow-hidden aspect-[4/5] m-3 rounded-2xl">
            <OptimizedImage 
              :src="product.thumbnail" 
              :alt="product.title"
              imageClass="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
            />
            
            <!-- Overlay d'actions rapides -->
            <div class="absolute inset-0 bg-gradient-to-t from-purple-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 
                        flex items-end justify-center pb-6 gap-3" @click.stop>
              <button @click="addToWishlist(product)" 
                      class="bg-white/90 backdrop-blur-sm rounded-full p-3 transform translate-y-4 opacity-0 group-hover:opacity-100 
                            group-hover:translate-y-0 transition-all duration-300 hover:bg-white text-gray-800">
                <Icon :icon="isInWishlist(product.id) ? 'solar:heart-bold' : 'solar:heart-linear'" 
                      class="w-6 h-6" :class="{ 'text-pink-500': isInWishlist(product.id) }" />
              </button>
              
              <button @click="addToCart(product)" 
                      class="bg-purple-600 rounded-full p-3 transform translate-y-4 opacity-0 group-hover:opacity-100 
                            group-hover:translate-y-0 transition-all duration-300 hover:bg-purple-700 text-white shadow-lg">
                <Icon icon="solar:cart-large-minimalistic-bold" class="w-6 h-6" />
              </button>
            </div>
          </div>
          
          <!-- Détails du produit -->
          <div class="p-6">
            <!-- Catégorie -->
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] text-purple-600 uppercase font-black tracking-[0.2em]">
                {{ product.category }}
              </span>
              <div class="flex items-center gap-1">
                <Icon icon="solar:star-bold" class="w-3 h-3 text-yellow-400" />
                <span class="text-[10px] font-bold text-gray-500">{{ product.rating.toFixed(1) }}</span>
              </div>
            </div>
            
            <!-- Nom du produit -->
            <h3 class="text-lg font-bold text-gray-800 mb-4 line-clamp-1 group-hover:text-purple-600 transition-colors">
              {{ product.title }}
            </h3>
            
            <!-- Prix et CTA -->
            <div class="flex items-center justify-between">
              <div class="flex flex-col">
                <span v-if="product.discount" 
                      class="text-xs text-gray-400 line-through mb-0.5">
                  ${{ (product.price * (1 + product.discount/100)).toFixed(2) }}
                </span>
                <span class="text-xl font-black text-gray-900">${{ product.price.toFixed(2) }}</span>
              </div>
              <button @click.stop="addToCart(product)" 
                      class="flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-700 rounded-xl text-sm font-bold hover:bg-purple-600 hover:text-white transition-all">
                <Icon icon="solar:add-circle-bold" class="w-5 h-5" />
                <span>Shop</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Chargement et pagination -->
      <div v-if="!loading && hasMore" class="text-center mt-12" data-aos="fade-up" data-aos-delay="200">
        <button @click="loadMore" 
                class="px-8 py-3 bg-purple-200 border border-purple-600 text-purple-700 rounded-full font-medium 
                       hover:bg-purple-600 hover:text-white transition-colors duration-300 focus:outline-none focus:ring-2 
                       focus:ring-purple-500 focus:ring-offset-2">
          Charger plus de produits
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Icon } from '@iconify/vue';
import OptimizedImage from './OptimizedImage.vue';
import { useCartStore } from '../stores/cart';
import { useProductStore } from '../stores/products';
import { useWishlistStore } from '../stores/wishlist';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Swal from 'sweetalert2';

const cartStore = useCartStore();
const productStore = useProductStore();
const wishlistStore = useWishlistStore();

const loading = computed(() => productStore.loading);
const page = ref(1);
const hasMore = ref(false); // Simplified for store
const showSortDropdown = ref(false);

const localProducts = ref([]); // To handle local sorting/filtering display associated with store data

// Options de tri
const sortOptions = [
  { value: 'featured', label: 'En vedette' },
  { value: 'price-asc', label: 'Prix croissant' },
  { value: 'price-desc', label: 'Prix décroissant' },
  { value: 'rating', label: 'Meilleures notes' },
];

const selectedSort = ref(sortOptions[0]);

// Charger les produits
const loadProducts = async () => {
  await productStore.fetchProducts();
  localProducts.value = [...productStore.products];
  hasMore.value = false; // Store fetches all at once for now
};

// Trier les produits
const sortProducts = (option) => {
  selectedSort.value = sortOptions.find(opt => opt.value === option) || sortOptions[0];
  showSortDropdown.value = false;
  
  switch (option) {
    case 'price-asc':
      localProducts.value.sort((a, b) => a.price - b.price);
      break;
    case 'price-desc':
      localProducts.value.sort((a, b) => b.price - a.price);
      break;
    case 'rating':
      localProducts.value.sort((a, b) => b.rating - a.rating);
      break;
    default:
      // Tri par défaut (featured)
      localProducts.value.sort((a, b) => a.id - b.id);
  }
};

// Produits affichés (avec le tri appliqué)
const displayedProducts = computed(() => {
  return [...localProducts.value];
});

// Vérifier si un produit est dans les favoris
const isInWishlist = (productId) => {
  return wishlistStore.isInWishlist(productId);
};

// Ajouter ou retirer un produit des favoris
const addToWishlist = (product) => {
  const added = wishlistStore.toggleWishlist(product);
  
  if (added) {
    showToast('Ajouté aux favoris', 'success');
  } else {
    showToast('Retiré des favoris', 'info');
  }
};

// Gestion du panier
const addToCart = async (product) => {
  try {
    await cartStore.addToCart(product);
    showToast('Produit ajouté au panier', 'success');
  } catch (error) {
    console.error('Erreur lors de l\'ajout au panier:', error);
    showToast('Erreur lors de l\'ajout au panier', 'error');
  }
};

// Fonction utilitaire pour les notifications
const showToast = (message, icon = 'success') => {
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });

  Toast.fire({ icon, title: message });
};

// Gestion du clic en dehors du menu déroulant
const handleClickOutside = (event) => {
  if (showSortDropdown.value && !event.target.closest('.relative')) {
    showSortDropdown.value = false;
  }
};

const toggleSortDropdown = () => {
  showSortDropdown.value = !showSortDropdown.value;
};

// Charger plus de produits
const loadMore = () => {
  if (!loading.value && hasMore.value) {
    loadProducts();
  }
};

// Gestion des événements
onMounted(() => {
  loadProducts();
  document.addEventListener('click', handleClickOutside);
  
  // Initialiser AOS
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    mirror: false
  });
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
 </script>

<style scoped>
/* Animation pour le survol des cartes */
.group:hover .group-hover\:translate-y-0 {
  transform: translateY(0);
}

/* Masquer le texte trop long avec des points de suspension */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  max-height: 3em;
  line-height: 1.5em;
}

/* Animation pour le bouton "Charger plus" */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style> 
