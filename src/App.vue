<template>
  <PageTransition>
    <div>
      <Header @open-store="openFeaturedProduct"/>
      <main class="relative bg-[#fdfaff] min-h-screen overflow-hidden">
        <!-- Modern Mesh Gradient Background -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
          <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-purple-200/50 blur-[120px] animate-pulse-slow"></div>
          <div class="absolute top-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-indigo-100/40 blur-[100px] animate-pulse-slow" style="animation-delay: 2s;"></div>
          <div class="absolute -bottom-[10%] left-[20%] w-[45%] h-[45%] rounded-full bg-pink-100/40 blur-[110px] animate-pulse-slow" style="animation-delay: 4s;"></div>
        </div>
        
        <div class="relative z-10">
          <Hero id="hero"/>
          
          <!-- Lazy loaded components with Suspense -->
          <Suspense>
            <template #default>
              <Products id="products" @select-product="openProductDetails"/>
            </template>
            <template #fallback>
              <div class="flex justify-center items-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-purple-200 border-t-purple-600"></div>
              </div>
            </template>
          </Suspense>
          
          <Suspense>
            <template #default>
              <Store id="store"/>
            </template>
            <template #fallback>
              <div class="h-48"></div>
            </template>
          </Suspense>
          
          <Suspense>
            <template #default>
              <Categories id="categories"/>
            </template>
            <template #fallback>
              <div class="h-48"></div>
            </template>
          </Suspense>
          
          <Contacts id="contact"/>
          <Footer />
        </div>
      </main>

      <ProductDetails 
        :is-open="isProductDetailsOpen" 
        :product="selectedProduct" 
        @close="isProductDetailsOpen = false" 
      />
     
    </div>
  </PageTransition>
</template>

<script setup>
import { onMounted, ref, defineAsyncComponent } from 'vue';

// Composants critiques - chargés immédiatement
import Header from './components/Header.vue';
import Hero from './components/Hero.vue';
import PageTransition from './components/PageTransition.vue';
import Footer from './components/Footer.vue';

// Composants non-critiques - chargés en lazy loading
const Products = defineAsyncComponent(() => 
  import('./components/Products.vue')
);

const Store = defineAsyncComponent(() => 
  import('./components/Store.vue')
);

const Categories = defineAsyncComponent(() => 
  import('./components/Categories.vue')
);

const Contacts = defineAsyncComponent(() => 
  import('./components/Contacts.vue')
);

const ProductDetails = defineAsyncComponent(() => 
  import('./components/ProductDetails.vue')
);

import { useProductStore } from './stores/products';

const productStore = useProductStore();
const isProductDetailsOpen = ref(false);
const selectedProduct = ref({});

const openProductDetails = (product) => {
  selectedProduct.value = product;
  isProductDetailsOpen.value = true;
};

const openFeaturedProduct = async () => {
  if (productStore.products.length === 0) {
    await productStore.fetchProducts();
  }
  const featured = productStore.getFeaturedProduct();
  openProductDetails(featured);
};

// Ajouter le style de balayage global
const addSweepStyles = () => {
  const style = document.createElement('style');
  style.textContent = `
    .sweep-link {
      position: relative;
      display: inline-block;
      overflow: hidden;
      transition: color 0.3s ease;
    }
    
    .sweep-link::before {
      content: '';
      position: absolute;
      z-index: -1;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to right, #9333ea, #4f46e5);
      transform: scaleX(0);
      transform-origin: 0 50%;
      transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      opacity: 0.1;
    }
    
    .sweep-link:hover::before {
      transform: scaleX(1);
    }

    @keyframes pulse-slow {
      0%, 100% { opacity: 0.4; transform: scale(1); }
      50% { opacity: 0.7; transform: scale(1.1); }
    }
    .animate-pulse-slow {
      animation: pulse-slow 10s infinite ease-in-out;
    }
  `;
  document.head.appendChild(style);
};

onMounted(() => {
  addSweepStyles();
  
  // Ajouter la classe sweep-link à tous les liens internes
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.classList.add('sweep-link');
  });
});
</script>
