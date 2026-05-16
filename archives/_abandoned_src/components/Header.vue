<template>
  <!-- Main Header -->
  <header :class="[
    'fixed top-0 left-0 right-0 z-[100] transition-all duration-700 ease-in-out',
    isScrolled ? 'py-3 bg-white/80 backdrop-blur-xl shadow-lg border-b border-purple-100/50' : 'py-6 bg-transparent'
  ]">
    <div class="container mx-auto px-4 md:px-6">
      <nav class="flex items-center justify-between">
        <!-- Logo -->
        <a href="#hero" class="group flex items-center space-x-2 shrink-0 z-[170] relative" @click="closeMenu">
          <div
            class="relative overflow-hidden w-10 h-10 rounded-full bg-gradient-to-tr from-purple-600 to-pink-400 flex items-center justify-center text-white shadow-lg transform transition-transform group-hover:scale-110 shrink-0">
            <Icon icon="solar:crown-minimalistic-bold" class="w-6 h-6" />
          </div>
          <div class="flex flex-col justify-center leading-none">
            <span
              class="text-xl md:text-2xl font-black tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-purple-800 to-indigo-600">
              Bloom
            </span>
            <span
              class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-[0.2em] -mt-0.5 whitespace-nowrap">
              by Chloe
            </span>
          </div>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex items-center space-x-8">
          <a v-for="link in navLinks" :key="link.href" :href="link.href" @click="(e) => scrollToSection(e, link.href)"
            class="relative font-bold text-gray-700 hover:text-purple-600 transition-colors py-2 px-1 group text-sm uppercase tracking-wide">
            {{ link.name }}
            <span
              class="absolute bottom-0 left-0 w-full h-0.5 bg-purple-600 transform scale-x-0 transition-transform origin-left group-hover:scale-x-100"></span>
          </a>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2 md:space-x-4">
          <!-- Store Icon (SPECIAL PAGE LINK) -->
          <!-- <button @click="$emit('open-store')" class="relative p-2 text-gray-700 hover:text-indigo-600 transition-colors group hidden sm:block" title="Découvrir le produit vedette">
             <Icon icon="solar:shop-bold-duotone" class="w-7 h-7 transform transition-transform group-hover:scale-110" />
             <span class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
              </span>
          </button> -->

          <!-- Wishlist Icon -->
          <button @click="viewWishlist"
            class="relative p-2 text-gray-700 hover:text-purple-600 transition-colors group">
            <Icon icon="solar:heart-bold-duotone"
              class="w-7 h-7 transform transition-transform group-hover:scale-110" />
            <span v-if="wishlistCount > 0"
              class="absolute top-0 right-0 flex h-5 w-5 items-center justify-center rounded-full bg-purple-600 text-[10px] font-bold text-white ring-2 ring-white">
              {{ wishlistCount }}
            </span>
          </button>

          <!-- Cart Icon -->
          <button @click="toggleCart" class="relative p-2 text-gray-700 hover:text-purple-600 transition-colors group">
            <Icon icon="solar:cart-large-minimalistic-bold"
              class="w-7 h-7 transform transition-transform group-hover:rotate-12" />
            <span v-if="cartStore.totalItems > 0"
              class="absolute top-0 right-0 flex h-5 w-5 items-center justify-center rounded-full bg-purple-600 text-[10px] font-bold text-white ring-2 ring-white">
              {{ cartStore.totalItems }}
            </span>
          </button>

          <!-- User Profile / Login -->
          <div class="hidden sm:block">
            <button v-if="!authStore.isAuthenticated" @click="showAuthModal = true"
              class="flex items-center space-x-2 px-6 py-2.5 rounded-full bg-purple-600 text-white font-bold hover:bg-purple-700 transition-all hover:shadow-xl active:scale-95 text-sm">
              <Icon icon="solar:user-circle-bold" class="w-5 h-5" />
              <span>S'identifier</span>
            </button>
            <div v-else class="relative group">
              <button
                class="flex items-center space-x-2 p-1.5 rounded-full bg-purple-50 border border-purple-100 hover:bg-purple-100 transition-colors">
                <div
                  class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-white text-xs font-bold uppercase">
                  {{ authStore.user?.first_name?.charAt(0) || 'U' }}
                </div>
                <Icon icon="solar:alt-arrow-down-linear" class="w-4 h-4 text-gray-500" />
              </button>
              <!-- Dropdown -->
              <div
                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform origin-top-right translate-y-2 group-hover:translate-y-0 py-2 z-[110]">
                <div class="px-4 py-2 border-b border-gray-50 mb-1">
                  <p class="text-xs text-gray-400">Connecté en tant que</p>
                  <p class="text-sm font-semibold truncate">{{ authStore.user?.email }}</p>
                </div>
                <button @click="authStore.logout"
                  class="w-full flex items-center space-x-2 px-4 py-2 text-red-500 hover:bg-red-50 transition-colors text-left">
                  <Icon icon="solar:logout-linear" class="w-4 h-4" />
                  <span class="text-sm font-bold">Déconnexion</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile Menu Button -->
          <button @click="isMenuOpen = !isMenuOpen"
            class="lg:hidden p-2 text-purple-700 hover:bg-purple-50 rounded-xl transition-colors focus:outline-none z-[170] relative"
            aria-label="Toggle Menu">
            <Icon :icon="isMenuOpen ? 'solar:close-circle-bold' : 'solar:hamburger-menu-bold'"
              class="w-8 h-8 transition-all duration-300" :class="{ 'rotate-90': isMenuOpen }" />
          </button>
        </div>
      </nav>
    </div>
  </header>

  <!-- Mobile Menu & Overlays (Outside header to avoid stacking issues) -->
  <div class="relative">
    <!-- Mobile Menu Full Screen -->
    <Transition name="menu-fade">
      <div v-if="isMenuOpen" class="fixed inset-0 z-[160] lg:hidden bg-gradient-to-br from-purple-50 to-purple-70 backdrop-blur-2xl overflow-y-auto">
        <!-- Background Decorations -->
        <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
          <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-purple-200/40 rounded-full blur-[100px]">
          </div>
          <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-pink-200/40 rounded-full blur-[100px]">
          </div>
        </div>

        <!-- Close Button -->
        <button @click="isMenuOpen = false" 
                class="absolute top-6 right-6 z-10 p-3 bg-white/80 backdrop-blur rounded-full shadow-lg text-gray-700 hover:text-purple-600 hover:bg-white hover:scale-110 transition-all"
                aria-label="Fermer le menu">
          <Icon icon="solar:close-circle-bold" class="w-8 h-8" />
        </button>

        <div class="min-h-full flex flex-col justify-start items-center pt-32 pb-12 px-6 text-center w-full">
          <nav class="flex flex-col items-center space-y-6 w-full max-w-lg">
            <a v-for="(link, index) in navLinks" :key="link.href" :href="link.href"
              @click="(e) => scrollToSectioWithDelay(e, link.href)"
              class="group relative text-3xl md:text-4xl font-black text-gray-800 hover:text-purple-600 transition-all duration-300 transform hover:scale-105">
              {{ link.name }}
            </a>

            <div
              class="w-16 h-1 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full my-6 opacity-0 animate-fade-in"
              style="animation-delay: 0.5s"></div>

            <!-- Quick Actions Grid -->
            <div class="grid grid-cols-2 gap-3 w-full">
              <button @click="toggleCart"
                class="flex flex-col items-center justify-center p-3 bg-white border border-gray-100 rounded-2xl shadow-lg hover:shadow-xl hover:bg-purple-50 transition-all gap-2">
                <Icon icon="solar:shop-bold-duotone" class="w-6 h-6 text-indigo-500" />
                <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Store ({{ cartStore.totalItems }})</span>
              </button>

              <button @click="viewWishlist"
                class="flex flex-col items-center justify-center p-3 bg-white border border-gray-100 rounded-2xl shadow-lg hover:shadow-xl hover:bg-pink-50 transition-all gap-2">
                <Icon icon="solar:heart-bold-duotone" class="w-6 h-6 text-pink-500" />
                <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Favoris ({{ wishlistCount }})</span>
              </button>
            </div>

            <!-- Auth -->
            <button v-if="!authStore.isAuthenticated" @click="isMenuOpen = false; showAuthModal = true"
              class="mt-6 text-xs font-bold text-gray-400 hover:text-purple-600 transition-colors uppercase tracking-widest opacity-0 animate-fade-in"
              style="animation-delay: 0.8s">
              Se connecter / S'inscrire
            </button>
            <button v-else @click="authStore.logout(); isMenuOpen = false"
              class="mt-6 text-xs font-bold text-red-400 hover:text-red-600 transition-colors uppercase tracking-widest opacity-0 animate-fade-in"
              style="animation-delay: 0.8s">
              Se déconnecter
            </button>
          </nav>
        </div>
      </div>
    </Transition>

    <!-- Auth Modal -->
    <AuthModal v-if="showAuthModal" @close="showAuthModal = false" />

    <!-- Cart Drawer -->
    <CartDrawer :is-open="isCartOpen" @close="isCartOpen = false" />

    <!-- Wishlist Drawer -->
    <WishlistDrawer :is-open="isWishlistOpen" @close="isWishlistOpen = false" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import { useWishlistStore } from '../stores/wishlist';
import AuthModal from './AuthModal.vue';
import CartDrawer from './CartDrawer.vue';
import WishlistDrawer from './WishlistDrawer.vue';
import Swal from 'sweetalert2';

const cartStore = useCartStore();
const authStore = useAuthStore();
const wishlistStore = useWishlistStore();

const isScrolled = ref(false);
const isMenuOpen = ref(false);
const isCartOpen = ref(false);
const isWishlistOpen = ref(false);
const showAuthModal = ref(false);
const wishlistCount = computed(() => wishlistStore.totalItems);


const navLinks = [
  { name: 'Accueil', href: '#hero' },
  { name: 'Produits', href: '#products' },
  { name: 'Collections', href: '#categories' },
  { name: 'Nouveautés', href: '#store' },
  { name: 'Contact', href: '#contact' },
];

const scrollToSectioWithDelay = (e, targetId) => {
  if (e) e.preventDefault();
  isMenuOpen.value = false;

  setTimeout(() => {
    scrollToSection(null, targetId);
  }, 400); // Wait for menu to close
};

const scrollToSection = (e, targetId) => {
  if (e) e.preventDefault();

  if (!isMenuOpen.value) {
    const element = document.querySelector(targetId);
    if (element) {
      const offset = 80;
      const elementPosition = element.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - offset;

      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }
  }
};

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

const closeMenu = () => {
  isMenuOpen.value = false;
};

const toggleCart = () => {
  isCartOpen.value = !isCartOpen.value;
  isMenuOpen.value = false;
};

const viewWishlist = () => {
  isMenuOpen.value = false;
  isWishlistOpen.value = !isWishlistOpen.value;
};

// Watchers for body scroll lock (safer implementation)
watch(isMenuOpen, (val) => {
  document.body.style.overflow = val ? 'hidden' : '';
});
watch(isCartOpen, (val) => {
  document.body.style.overflow = val ? 'hidden' : '';
});
watch(isWishlistOpen, (val) => {
  document.body.style.overflow = val ? 'hidden' : '';
});

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    document.body.style.overflow = ''; // Clean up
  });
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.menu-fade-enter-active,
.menu-fade-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}

.menu-fade-enter-from,
.menu-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fade-in {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.animate-fade-in {
  animation: fade-in 0.6s ease forwards;
}
</style>
