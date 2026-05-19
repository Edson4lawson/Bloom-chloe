import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import { getProductImageUrl } from '@/utils/imageHelper'

/**
 * Wishlist Store — Gère les favoris avec synchronisation backend
 */
export const useWishlistStore = defineStore('wishlist', () => {
  const items = ref([])

  const totalItems = computed(() => items.value.length)

  /**
   * Check if a product is in the wishlist
   */
  const isInWishlist = (productId) => {
    return items.value.some(item => item.id === productId)
  }

  /**
   * Toggle a product in/out of the wishlist
   * Returns true if added, false if removed
   */
  const toggleWishlist = (product) => {
    const index = items.value.findIndex(item => item.id === product.id)
    if (index !== -1) {
      items.value.splice(index, 1)
      syncRemove(product.id)
      return false
    } else {
      items.value.push({
        id: product.id,
        title: product.title || product.name,
        price: product.price,
        thumbnail: getProductImageUrl(product.thumbnail || product.image_url),
        category: product.category || product.category_name,
        slug: product.slug
      })
      syncAdd(product.id)
      return true
    }
  }

  /**
   * Add a product to the wishlist
   */
  const addToWishlist = (product) => {
    if (!isInWishlist(product.id)) {
      return toggleWishlist(product)
    }
    return true
  }

  /**
   * Remove a product from the wishlist
   */
  const removeFromWishlist = (productId) => {
    const index = items.value.findIndex(item => item.id === productId)
    if (index !== -1) {
      items.value.splice(index, 1)
      syncRemove(productId)
    }
  }

  /**
   * Clear the entire wishlist
   */
  const clearWishlist = () => {
    items.value = []
  }

  /**
   * Sync wishlist from backend (called after login)
   */
  const syncFromBackend = async () => {
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return

      const response = await api.get('/favorites/get.php')
      const backendItems = response.data?.data || response.data?.favorites || (Array.isArray(response.data) ? response.data : [])
      
      // Merge: keep local items not in backend + add backend items
      const backendIds = new Set(backendItems.map(i => i.product_id || i.id))
      const localOnly = items.value.filter(i => !backendIds.has(i.id))
      
      const merged = [
        ...backendItems.map(i => ({
          id: i.product_id || i.id,
          title: i.product_name || i.name || i.title,
          price: parseFloat(i.price) || 0,
          thumbnail: getProductImageUrl(i.image_url || i.thumbnail),
          category: i.category_name || i.category,
          slug: i.slug
        })),
        ...localOnly
      ]

      items.value = merged

      // Sync local-only items to backend
      for (const item of localOnly) {
        syncAdd(item.id)
      }
    } catch (err) {
      console.error('Wishlist sync error:', err)
    }
  }

  // Private sync helpers
  const syncAdd = async (productId) => {
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return
      await api.post('/favorites/add.php', { product_id: productId })
    } catch { /* silent */ }
  }

  const syncRemove = async (productId) => {
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return
      await api.post('/favorites/remove.php', { product_id: productId })
    } catch { /* silent */ }
  }

  return {
    items,
    totalItems,
    isInWishlist,
    toggleWishlist,
    addToWishlist,
    removeFromWishlist,
    clearWishlist,
    syncFromBackend
  }
}, {
  persist: true
})
