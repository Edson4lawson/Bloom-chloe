import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService } from '@/services/api';
import { useCartStore } from './cart';
import { useWishlistStore } from './wishlist';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const accessToken = ref(null);
  const refreshToken = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Getters
  const isAuthenticated = computed(() => !!accessToken.value);
  const currentUser = computed(() => user.value);

  // Initialiser depuis localStorage au démarrage
  const initializeFromStorage = () => {
    try {
      const storedUser = localStorage.getItem('user');
      const storedToken = localStorage.getItem('access_token');
      const storedRefresh = localStorage.getItem('refresh_token');
      
      if (storedUser) user.value = JSON.parse(storedUser);
      if (storedToken) accessToken.value = storedToken;
      if (storedRefresh) refreshToken.value = storedRefresh;
    } catch (e) {
      console.warn('Erreur lors de l\'initialisation depuis localStorage:', e);
    }
  };

  // Actions
  async function syncAppData() {
    const cartStore = useCartStore();
    const wishlistStore = useWishlistStore();
    
    // Charger parallèlement les données utilisateur
    await Promise.allSettled([
      cartStore.fetchCart?.(),
      wishlistStore.fetchWishlist?.()
    ]);
  }

  async function login(email, password) {
    loading.value = true;
    error.value = null;
    try {
      const response = await authService.login(email, password);
      
      if (response.data.access_token && response.data.user) {
        accessToken.value = response.data.access_token;
        refreshToken.value = response.data.refresh_token;
        user.value = response.data.user;
        
        // Sync data après login
        await syncAppData();
        
        return response.data;
      }
      throw new Error('Identifiants invalides');
    } catch (err) {
      error.value = err.response?.data?.error || 'Erreur lors de la connexion';
      throw error.value;
    } finally {
      loading.value = false;
    }
  }

  async function register(userData) {
    loading.value = true;
    try {
      const response = await authService.register(userData);
      if (response.data.access_token && response.data.user) {
        accessToken.value = response.data.access_token;
        refreshToken.value = response.data.refresh_token;
        user.value = response.data.user;
        return response.data;
      }
    } catch (err) {
      error.value = err.response?.data?.error || 'Erreur lors de l\'inscription';
      throw error.value;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    const cartStore = useCartStore();
    const wishlistStore = useWishlistStore();
    
    try {
      await authService.logout();
    } finally {
      accessToken.value = null;
      refreshToken.value = null;
      user.value = null;
      
      // Nettoyer stores locaux au logout
      cartStore.clearCart?.();
      wishlistStore.clearWishlist?.();
    }
  }

  return {
    user, accessToken, refreshToken, loading, error,
    isAuthenticated, currentUser,
    login, register, logout, syncAppData, initializeFromStorage
  };
}, {
  persist: {
    key: 'bloom-auth',
    paths: ['user', 'accessToken', 'refreshToken']
  }
});
