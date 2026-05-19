import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { cartService } from '@/services/api'
import { useNotifications } from '@/services/notificationService.js'

/**
 * Cart Store — Gère le panier avec synchronisation backend
 */
export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const { addNotification } = useNotifications()
  
  // Vérifie qu'un ID de produit est valide (entier positif > 0)
  const isValidProductId = (id) => {
    if (id === null || id === undefined) return false
    const num = parseInt(id, 10)
    return !isNaN(num) && num > 0 && String(num) !== 'NaN'
  }

  // Nettoyage automatique des items invalides au démarrage
  const cleanInvalidItems = () => {
    items.value = items.value.filter(item =>
      item &&
      isValidProductId(item.id) &&
      item.id !== 'undefined' &&
      item.id !== 'null'
    )
    // Normaliser tous les IDs en entiers
    items.value = items.value.map(item => ({ ...item, id: parseInt(item.id, 10) }))
  }
  cleanInvalidItems()


  // Computed: nombre total d'articles
  const totalItems = computed(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  // Computed: montant total du panier
  const cartTotal = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })

  /**
   * Ajouter un produit au panier avec vérification de stock
   */
  const addToCart = async (product, qty = 1) => {
    const productId = parseInt(product.id, 10)
    if (!isValidProductId(productId)) return

    // Récupération du stock réel (soit depuis l'objet passé, soit depuis le store)
    let stockAvailable = 0
    if (product.stock_quantity !== undefined && product.stock_quantity !== null) {
      stockAvailable = parseInt(product.stock_quantity, 10)
    } else if (product.stock !== undefined && product.stock !== null) {
      stockAvailable = parseInt(product.stock, 10)
    } else {
      // Rechercher dans le productStore
      const productStore = (await import('./products')).useProductStore()
      const dbProduct = productStore.getProductById(productId)
      if (dbProduct) {
        stockAvailable = parseInt(dbProduct.stock, 10)
      } else {
        // Fallback à 999 si le produit n'est pas trouvé dans le store pour éviter de bloquer l'ajout
        stockAvailable = 999
      }
    }
    const existingIndex = items.value.findIndex(item => item.id === productId)
    const currentQtyInCart = existingIndex !== -1 ? items.value[existingIndex].quantity : 0
    const totalRequested = currentQtyInCart + qty

    // Vérification de stock
    if (totalRequested > stockAvailable) {
      const diff = stockAvailable - currentQtyInCart
      
      // Message personnalisé pour l'utilisateur
      addNotification({
        type: 'warning',
        title: 'Stock limité',
        message: stockAvailable <= 0 
          ? `Désolé, cet article est actuellement en rupture de stock. N'hésitez pas à contacter notre administrateur pour savoir quand il sera de nouveau disponible !`
          : `Nous n'avons que ${stockAvailable} unité(s) disponible(s) pour cet article. Pour une commande plus importante, veuillez contacter l'administrateur.`,
        duration: 7000
      })

      // Si on peut encore ajouter quelques unités, on le fait jusqu'à la limite du stock
      if (diff > 0) {
        if (existingIndex !== -1) {
          items.value[existingIndex].quantity = stockAvailable
        } else {
          items.value.push({
            id: productId,
            title: product.title || product.name,
            price: parseFloat(product.price) || 0,
            quantity: stockAvailable,
            thumbnail: product.thumbnail || product.image_url,
            category: product.category || product.category_name,
            slug: product.slug
          })
        }
        await syncUpdateBackend(productId, stockAvailable)
      }
      return
    }

    if (existingIndex !== -1) {
      items.value[existingIndex].quantity += qty
    } else {
      items.value.push({
        id: productId,
        title: product.title || product.name,
        price: parseFloat(product.price) || 0,
        quantity: qty,
        thumbnail: product.thumbnail || product.image_url,
        category: product.category || product.category_name,
        slug: product.slug
      })
    }

    // Sync with backend if authenticated
    await syncAddToBackend(productId, qty)
  }

  /**
   * Mettre à jour la quantité d'un article avec vérification de stock
   */
  const updateQuantity = async (productId, newQty) => {
    const item = items.value.find(i => i.id === productId)
    if (!item) return

    if (newQty <= 0) {
      await removeFromCart(productId)
      return
    }

    // On essaie de trouver les infos de stock dans le productStore
    const productStore = (await import('./products')).useProductStore()
    const product = productStore.getProductById(productId)
    const stockAvailable = product ? parseInt(product.stock) : 999 // Fallback si non trouvé

    if (newQty > stockAvailable) {
      addNotification({
        type: 'warning',
        title: 'Limite de stock',
        message: `Le stock est limité à ${stockAvailable} unités pour cet article. Contactez-nous pour en demander plus !`,
        duration: 5000
      })
      item.quantity = stockAvailable
      await syncUpdateBackend(productId, stockAvailable)
    } else {
      item.quantity = newQty
      await syncUpdateBackend(productId, newQty)
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
      const raw = response.data
      let backendItems = []
      
      if (Array.isArray(raw)) {
        backendItems = raw
      } else if (raw && Array.isArray(raw.cart)) {
        backendItems = raw.cart
      } else if (raw && Array.isArray(raw.items)) {
        backendItems = raw.items
      } else if (raw && Array.isArray(raw.data)) {
        backendItems = raw.data
      }

      if (backendItems.length === 0 && items.value.length > 0) {
        // Le panier local contient des articles mais le serveur est vide
        // On pousse les articles locaux vers le serveur
        for (const item of items.value) {
          if (item.id) {
            await syncAddToBackend(item.id, item.quantity)
          }
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
    const id = parseInt(productId, 10)
    if (!isValidProductId(id)) {
      console.error('[Cart Sync] ID produit invalide pour sync:', productId)
      return
    }
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return
      await cartService.add(id, quantity)
    } catch (err) {
      if (err.response?.status === 400) {
        const errorMsg = err.response.data.error
        console.warn('Cart sync warning:', errorMsg, { id, quantity })
        
        // Si le stock est insuffisant, ajuster localement la quantité à celle disponible
        if (errorMsg === 'Stock insuffisant' && err.response.data.available_quantity !== undefined) {
          const available = parseInt(err.response.data.available_quantity, 10)
          const item = items.value.find(i => i.id === id)
          
          addNotification({
            type: 'warning',
            title: 'Stock limité',
            message: `Le stock pour "${item?.title || 'cet article'}" est insuffisant. Quantité ajustée à ${available}.`,
            duration: 5000
          })

          if (item) {
            if (available <= 0) {
              items.value = items.value.filter(i => i.id !== id)
            } else {
              item.quantity = available
            }
          }
        }
      }
    }
  }

  const syncUpdateBackend = async (productId, quantity) => {
    const id = parseInt(productId, 10)
    if (!isValidProductId(id) || quantity === undefined) return
    try {
      const token = localStorage.getItem('access_token')
      if (!token) return
      await cartService.update(id, parseInt(quantity))
    } catch (err) {
      if (err.response?.status === 400) {
        console.warn('Cart update warning:', err.response.data.error, { id, quantity })
      }
    }
  }

  const syncRemoveBackend = async (productId) => {
    if (!productId) return
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
