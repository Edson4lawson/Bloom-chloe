import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { productsService } from '@/services/api'
import { getProductImageUrl } from '@/utils/imageHelper'

export const useProductStore = defineStore('products', () => {
  const products = ref([])
  const loading = ref(false)
  const error = ref(null)
  const lastFetchTime = ref(0)

  // Cache duration: 5 minutes
  const CACHE_DURATION = 5 * 60 * 1000

  /**
   * Fetches all products from the API and transforms them into a local format.
   * Uses caching to avoid redundant API calls.
   */
  const fetchProducts = async (force = false) => {
    const now = Date.now()
    
    // Use cache if fresh enough
    if (!force && products.value.length > 0 && (now - lastFetchTime.value) < CACHE_DURATION) {
      return
    }

    loading.value = true
    error.value = null

    try {
      const response = await productsService.getAll({ per_page: 200 })
      let raw = response.data?.data || response.data?.products || response.data
      if (!Array.isArray(raw)) {
        raw = []
      }

      products.value = raw.map(p => ({
        id: p.id,
        title: p.name || p.title || 'Produit sans nom',
        slug: p.slug || `produit-${p.id}`,
        description: p.description || '',
        price: parseFloat(p.price) || 0,
        comparePrice: parseFloat(p.compare_price) || null,
        discount: p.compare_price && parseFloat(p.compare_price) > parseFloat(p.price) 
          ? Math.round((1 - parseFloat(p.price) / parseFloat(p.compare_price)) * 100) 
          : 0,
        stock: parseInt(p.stock_quantity || p.stock) || 0,
        category: p.category_name || p.category || '',
        category_id: p.category_id || null,
        thumbnail: getProductImageUrl(p.image_url || p.main_image),
        gallery: p.gallery_urls ? (typeof p.gallery_urls === 'string' ? JSON.parse(p.gallery_urls) : p.gallery_urls).map(getProductImageUrl) : [],
        rating: parseFloat(p.rating) || (3.5 + Math.random() * 1.5),
        source: p.source || 'produit',
        isBestseller: !!p.is_bestseller,
        isNewest: !!p.is_newest,
        isSpecialOffer: !!p.is_special_offer,
        isActive: p.is_active !== false && p.status !== 'draft',
        createdAt: p.created_at
      })).filter(p => p.isActive)

      lastFetchTime.value = now
    } catch (err) {
      error.value = 'Impossible de charger les produits'
      console.error('Product fetch error:', err)
    } finally {
      loading.value = false
    }
  }

  /**
   * Search products by title or category
   */
  const searchProducts = (query) => {
    if (!query || query.length < 2) return []
    const q = query.toLowerCase()
    return products.value.filter(p => 
      p.title.toLowerCase().includes(q) || 
      p.category.toLowerCase().includes(q) ||
      p.description.toLowerCase().includes(q)
    )
  }

  /**
   * Get product by ID
   */
  const getProductById = (id) => {
    return products.value.find(p => p.id === parseInt(id))
  }

  /**
   * Get featured product (random bestseller or first product)
   */
  const getFeaturedProduct = () => {
    const bestsellers = products.value.filter(p => p.isBestseller)
    if (bestsellers.length > 0) {
      return bestsellers[Math.floor(Math.random() * bestsellers.length)]
    }
    return products.value[0] || null
  }

  /**
   * Get products by category
   */
  const getByCategory = (category) => {
    return products.value.filter(p => p.category === category)
  }

  /**
   * Get newest products
   */
  const getNewest = (limit = 6) => {
    return products.value
      .filter(p => p.isNewest)
      .slice(0, limit)
  }

  /**
   * Get bestsellers
   */
  const getBestsellers = (limit = 8) => {
    return products.value
      .filter(p => p.isBestseller)
      .slice(0, limit)
  }

  /**
   * Get all unique categories
   */
  const categories = computed(() => {
    return [...new Set(products.value.map(p => p.category).filter(Boolean))].sort()
  })

  /**
   * Total product count
   */
  const totalCount = computed(() => products.value.length)

  return {
    products,
    loading,
    error,
    fetchProducts,
    searchProducts,
    getProductById,
    getFeaturedProduct,
    getByCategory,
    getNewest,
    getBestsellers,
    categories,
    totalCount
  }
}, {
  persist: {
    storage: sessionStorage,
    paths: ['products', 'lastFetchTime']
  }
})
