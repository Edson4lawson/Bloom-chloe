import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService } from '@/services/api';

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
  function initFromStorage() {
    const storedUser = localStorage.getItem('user');
    const storedAccessToken = localStorage.getItem('access_token');
    const storedRefreshToken = localStorage.getItem('refresh_token');
    
    if (storedUser) {
      try {
        user.value = JSON.parse(storedUser);
      } catch (e) {
        user.value = null;
      }
    }
    accessToken.value = storedAccessToken;
    refreshToken.value = storedRefreshToken;
  }

  // Actions
  async function login(email, password) {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await authService.login(email, password);
      
      if (response.data.access_token && response.data.user) {
        accessToken.value = response.data.access_token;
        refreshToken.value = response.data.refresh_token;
        user.value = response.data.user;
        return response.data;
      } else {
        throw new Error('Réponse invalide du serveur');
      }
    } catch (err) {
      error.value = err.response?.data?.error || 'Erreur lors de la connexion';
      throw error.value;
    } finally {
      loading.value = false;
    }
  }

  async function register(userData) {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await authService.register(userData);
      
      if (response.data.access_token && response.data.user) {
        accessToken.value = response.data.access_token;
        refreshToken.value = response.data.refresh_token;
        user.value = response.data.user;
        return response.data;
      } else {
        throw new Error('Réponse invalide du serveur');
      }
    } catch (err) {
      error.value = err.response?.data?.error || 'Erreur lors de l\'inscription';
      throw error.value;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      await authService.logout();
    } catch (err) {
      console.error('Erreur lors de la déconnexion:', err);
    } finally {
      accessToken.value = null;
      refreshToken.value = null;
      user.value = null;
      error.value = null;
    }
  }

  async function logoutAllDevices() {
    try {
      await authService.logoutAll();
    } catch (err) {
      console.error('Erreur lors de la déconnexion:', err);
    } finally {
      accessToken.value = null;
      refreshToken.value = null;
      user.value = null;
      error.value = null;
    }
  }

  // Force logout (sans appel API)
  function forceLogout() {
    authService.forceLogout();
    accessToken.value = null;
    refreshToken.value = null;
    user.value = null;
    error.value = null;
  }

  // Initialiser au chargement
  initFromStorage();

  return {
    user,
    accessToken,
    refreshToken,
    loading,
    error,
    isAuthenticated,
    currentUser,
    login,
    register,
    logout,
    logoutAllDevices,
    forceLogout,
    initFromStorage
  };
}, {
  persist: {
    key: 'bloom-auth',
    paths: ['user', 'accessToken', 'refreshToken']
  }
});

