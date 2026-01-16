<template>
    <div class="relative py-20 flex flex-col items-center justify-center text-center overflow-hidden" id="store">
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
             <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                <div v-for="(product, index) in arrivals" :data-aos="'fade-up'" :data-aos-delay="100 * (index % 3 + 1)" :key="index"
                    class="group relative bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                    
                    <!-- Product Image -->
                    <div class="aspect-[4/5] overflow-hidden bg-purple-50">
                        <OptimizedImage 
                            :src="product.thumbnail" 
                            :alt="product.title"
                            imageClass="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                        />
                        
                        <!-- Quick Add Overlay -->
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                           <button @click="addToCart(product)" 
                                   class="bg-white text-purple-600 p-4 rounded-full shadow-2xl transform scale-50 group-hover:scale-100 transition-transform duration-500 hover:bg-purple-600 hover:text-white">
                             <Icon icon="solar:cart-large-minimalistic-bold" class="w-8 h-8" />
                           </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-6 text-left">
                        <div class="flex justify-between items-start mb-2">
                          <h3 class="text-xl font-bold text-gray-800"> {{ product.title }} </h3>
                          <span class="text-purple-600 font-black text-xl">{{ product.price }}€</span>
                        </div>
                        <p class="text-gray-500 text-sm mb-6">{{ product.description }}</p>
                        
                        <button @click="addToCart(product)" 
                                class="w-full py-3 bg-purple-50 text-purple-700 font-bold rounded-xl hover:bg-purple-600 hover:text-white transition-all flex items-center justify-center space-x-2">
                          <Icon icon="solar:add-circle-bold" class="w-5 h-5" />
                          <span>Ajouter au panier</span>
                        </button>
                    </div>

                    <!-- New Badge -->
                    <div class="absolute top-4 left-4 bg-purple-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-lg">
                      Nouveau
                    </div>
                </div>
            </div>
              </section>
    

</template>

<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'
import OptimizedImage from './OptimizedImage.vue'
import { useCartStore } from '../stores/cart'
import { useProductStore } from '../stores/products'
import Swal from 'sweetalert2'

const cartStore = useCartStore()
const productStore = useProductStore()

// Get latest products (simulated by taking the last 6 added)
const arrivals = computed(() => {
  // If store is empty, fetch might be needed, but usually App/Header triggers it. 
  // We'll fallback to slice if populated.
  const all = productStore.products;
  if (all.length === 0) return [];
  // Return last 6 items
  return all.slice(-6).reverse(); 
});

const addToCart = (product) => {
  cartStore.addToCart(product)
  
  Swal.fire({
    title: 'Ajouté !',
    text: `${product.title} a été ajouté à votre panier.`,
    icon: 'success',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
  })
}
</script>