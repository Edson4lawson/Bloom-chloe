import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { cartService } from '@/services/api'

/**
 * Cart Store — Gère le panier avec synchronisation backend
 */
export const useCartStore = defineStore('cart', () => {
  const items = ref([])

  // Computed: nombre total d'articles
  const totalItems = computed(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  // Computed: montant total du panier
  const cartTotal = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })

  /**
   * Ajouter un produit au panier
   */
  const addToCart = async (product, qty = 1) => {
    const existingIndex = items.value.findIndex(item => item.id === product.id)

    if (existingIndex !== -1) {
      items.value[existingIndex].quantity += qty
    } else {
      items.value.push({
        id: product.id,
        title: product.title || product.name,
        price: parseFloat(product.price) || 0,
        quantity: qty,
        thumbnail: product.thumbnail || product.image_url,
        category: product.category || product.category_name,
        slug: product.slug
      })
    }

    // Sync with backend if authenticated
    await syncAddToBackend(product.id, qty)
  }

  /**
   * Mettre à jour la quantité d'un article
   */
  const updateQuantity = async (productId, newQty) => {
    const item = items.value.find(i => i.id === productId)
    if (item) {
      if (newQty <= 0) {
        removeFromCart(productId)
      } else {
        item.quantity = newQty
        await syncUpdateBackend(productId, newQty)
      }
    }
  }

  /**
   * Retirer un article du panier
   */
  const removeFromCart = async (productId) => {
    items.value = items.value.filter(i => i.id !== productId)
    await syncRemoveBackend(productId)
  }

  /**
   * Vider le panier
   */
  const clearCart = () => {
    items.value = []
  }

  /**
   * Synchroniser le panier depuis le backend (après login)
   */
  const syncCartFromBackend = async () => {
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return

      const response = await cartService.get()
      const backendItems = response.data?.cart || response.data?.items || response.data || []

      if (backendItems.length === 0 && items.value.length > 0) {
        // Local has items but backend is empty — push local to backend
        for (const item of items.value) {
          await syncAddToBackend(item.id, item.quantity)
        }
      } else if (backendItems.length > 0) {
        // Merge backend items with local
        const merged = new Map()

        // Add backend items
        for (const bi of backendItems) {
          merged.set(bi.product_id || bi.id, {
            id: bi.product_id || bi.id,
            title: bi.product_name || bi.name || bi.title,
            price: parseFloat(bi.price) || 0,
            quantity: parseInt(bi.quantity) || 1,
            thumbnail: bi.image_url || bi.thumbnail,
            category: bi.category_name || bi.category,
            slug: bi.slug
          })
        }

        // Add local-only items
        for (const li of items.value) {
          if (!merged.has(li.id)) {
            merged.set(li.id, li)
            await syncAddToBackend(li.id, li.quantity)
          }
        }

        items.value = Array.from(merged.values())
      }
    } catch (err) {
      console.error('Cart sync error:', err)
    }
  }

  // Private sync helpers
  const syncAddToBackend = async (productId, quantity) => {
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return
      await cartService.add(productId, quantity)
    } catch { /* silent */ }
  }

  const syncUpdateBackend = async (productId, quantity) => {
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return
      await cartService.update(productId, quantity)
    } catch { /* silent */ }
  }

  const syncRemoveBackend = async (productId) => {
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return
      await cartService.remove(productId)
    } catch { /* silent */ }
  }

  return {
    items,
    totalItems,
    cartTotal,
    addToCart,
    updateQuantity,
    removeFromCart,
    clearCart,
    syncCartFromBackend
  }
}, {
  persist: true
})
