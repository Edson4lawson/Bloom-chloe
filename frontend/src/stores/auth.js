import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/services/api'
import { useCartStore } from './cart'
import { useWishlistStore } from './wishlist'

/**
 * Auth Store — Gère l'authentification avec synchronisation des données 
 */
export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const isAuthenticated = ref(false)
  const loading = ref(false)

  /**
   * Initialize auth state from localStorage
   */
  const initFromStorage = () => {
    const token = localStorage.getItem('access_token')
    const savedUser = localStorage.getItem('user')

    if (token && savedUser) {
      try {
        user.value = JSON.parse(savedUser)
        isAuthenticated.value = true
      } catch {
        clearAuth()
      }
    }
  }

  /**
   * Login user
   */
  const login = async (email, password) => {
    loading.value = true
    try {
      const response = await authService.login(email, password)
      const data = response.data

      if (data.access_token) {
        localStorage.setItem('access_token', data.access_token)
        if (data.refresh_token) {
          localStorage.setItem('refresh_token', data.refresh_token)
        }

        user.value = data.user || { email }
        localStorage.setItem('user', JSON.stringify(user.value))
        isAuthenticated.value = true

        // Sync app data after login
        syncAppData()
      } else {
        throw data.error || 'Erreur de connexion'
      }
    } catch (err) {
      throw err.response?.data?.error || err.message || 'Identifiants invalides'
    } finally {
      loading.value = false
    }
  }

  /**
   * Register new user
   */
  const register = async (userData) => {
    loading.value = true
    try {
      const response = await authService.register(userData)
      const data = response.data

      if (data.access_token) {
        localStorage.setItem('access_token', data.access_token)
        if (data.refresh_token) {
          localStorage.setItem('refresh_token', data.refresh_token)
        }

        user.value = data.user || { email: userData.email }
        localStorage.setItem('user', JSON.stringify(user.value))
        isAuthenticated.value = true

        syncAppData()
      } else if (data.success) {
        // Registration success without auto-login — user must login
        return true
      } else {
        throw data.error || 'Erreur d\'inscription'
      }
    } catch (err) {
      throw err.response?.data?.error || err.message || 'Erreur lors de l\'inscription'
    } finally {
      loading.value = false
    }
  }

  /**
   * Logout user
   */
  const logout = async () => {
    try {
      await authService.logout()
    } catch {
      // Silent — logout even if API fails
    } finally {
      clearAuth()
    }
  }

  /**
   * Clear all auth data
   */
  const clearAuth = () => {
    user.value = null
    isAuthenticated.value = false
    localStorage.removeItem('access_token')
    localStorage.removeItem('refresh_token')
    localStorage.removeItem('user')
  }

  /**
   * Sync cart and wishlist from backend after login
   */
  const syncAppData = async () => {
    try {
      const cartStore = useCartStore()
      const wishlistStore = useWishlistStore()

      await Promise.all([
        cartStore.syncCartFromBackend(),
        wishlistStore.syncFromBackend()
      ])
    } catch (err) {
      console.warn('App data sync failed:', err)
    }
  }

  /**
   * Refresh the access token using the refresh token
   */
  const refreshToken = async () => {
    try {
      const response = await authService.refreshToken()
      if (response.data?.access_token) {
        localStorage.setItem('access_token', response.data.access_token)
        return response.data.access_token
      }
      return null
    } catch (err) {
      console.error('Token refresh failed:', err)
      clearAuth()
      return null
    }
  }

  /**
   * Update user profile
   */
  const updateProfile = async (profileData) => {
    loading.value = true
    try {
      const response = await authService.updateProfile(profileData)
      const data = response.data
      if (data.user) {
        user.value = data.user
        localStorage.setItem('user', JSON.stringify(user.value))
      }
      return response
    } catch (err) {
      throw err.response?.data?.error || err.message || 'Erreur lors de la mise à jour'
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    isAuthenticated,
    loading,
    initFromStorage,
    login,
    register,
    logout,
    clearAuth,
    syncAppData,
    refreshToken,
    updateProfile
  }
}, {
  persist: {
    paths: ['user', 'isAuthenticated']
  }
})
