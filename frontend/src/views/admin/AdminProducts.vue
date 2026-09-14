<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="product-header flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-daba-navy dark:text-white">Catalogue Produits</h1>
        <p class="text-sm text-daba-slate dark:text-daba-slate-dark">Gérez vos produits, stocks et promotions</p>
      </div>
      <div class="flex items-center gap-3">
        <button 
          @click="loadProducts" 
          :disabled="isLoading"
          class="p-2.5 bg-daba-cream dark:bg-[rgb(43,44,43)] border border-daba-cream-alt dark:border-slate-500 text-daba-slate dark:text-slate-300 rounded-xl hover:bg-daba-cream-alt dark:hover:bg-slate-700 transition-all disabled:opacity-50"
          title="Actualiser les produits"
        >
          <RotateCw class="w-4 h-4" :class="{ 'animate-spin': isLoading }" />
        </button>
        <button 
          @click="showCreateForm = true" 
          class="inline-flex items-center px-4 py-2 bg-daba-orange dark:bg-daba-orange text-white text-sm font-bold rounded-xl hover:bg-daba-navy dark:hover:bg-daba-orange/80 transition-all"
        >
          <Plus class="w-4 h-4 mr-2" />
          Nouveau Produit
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="product-filter bg-daba-cream dark:bg-daba-dark-card rounded-2xl shadow-sm border border-daba-cream-alt dark:border-daba-dark-border p-4 transition-colors">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
          <input 
            v-model="filters.search" 
            placeholder="Rechercher un produit..." 
            class="w-full pl-10 pr-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-daba-orange/20 transition-all dark:text-white"
          >
        </div>
        <select v-model="filters.category" class="px-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-daba-orange/20 dark:text-white">
          <option value="">Toutes les catégories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <select v-model="filters.status" class="px-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-daba-orange/20 dark:text-white">
          <option value="">Tous les statuts</option>
          <option value="active">Actif</option>
          <option value="inactive">Inactif</option>
        </select>
        <div class="flex items-center space-x-2 px-2">
          <label class="flex items-center cursor-pointer group">
            <input type="checkbox" v-model="filters.bestsellers" class="hidden">
            <div class="w-4 h-4 border-2 rounded mr-2 flex items-center justify-center transition-colors border-daba-cream-alt dark:border-daba-dark-border group-hover:border-daba-orange" :class="filters.bestsellers ? 'bg-daba-orange border-daba-orange' : ''">
              <Check v-if="filters.bestsellers" class="w-3 h-3 text-white" />
            </div>
            <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Top Ventes</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="product-table-container bg-daba-cream dark:bg-daba-dark-card rounded-2xl shadow-sm border border-daba-cream-alt dark:border-daba-dark-border overflow-hidden transition-colors relative">
      <!-- Top Loading Bar -->
      <div v-if="isLoading" class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-daba-orange via-daba-navy to-daba-orange animate-pulse z-10"></div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 dark:divide-daba-dark-border">
          <thead class="bg-daba-cream-alt dark:bg-daba-dark-card/50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Produit</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Catégorie</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Prix/Stock</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Mises en avant</th>
              <th class="px-6 py-4 text-right text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-daba-cream dark:bg-daba-dark-card divide-y divide-slate-100 dark:divide-daba-dark-border">
            <!-- Loading Skeletons -->
            <tr v-if="isLoading" v-for="n in 6" :key="'skeleton-' + n" class="animate-pulse">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-xl bg-daba-cream-alt dark:bg-slate-700 flex-shrink-0"></div>
                  <div class="ml-4 space-y-2">
                    <div class="h-4 bg-daba-cream-alt dark:bg-slate-700 rounded w-36"></div>
                    <div class="h-3 bg-daba-cream-alt dark:bg-slate-800 rounded w-24"></div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="h-6 bg-daba-cream-alt dark:bg-slate-700 rounded-lg w-24"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap space-y-2">
                <div class="h-4 bg-daba-cream-alt dark:bg-slate-700 rounded w-20"></div>
                <div class="h-3 bg-daba-cream-alt dark:bg-slate-800 rounded w-16"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center justify-center space-x-3">
                  <div class="w-7 h-7 bg-daba-cream-alt dark:bg-slate-700 rounded-lg"></div>
                  <div class="w-7 h-7 bg-daba-cream-alt dark:bg-slate-700 rounded-lg"></div>
                  <div class="w-7 h-7 bg-daba-cream-alt dark:bg-slate-700 rounded-lg"></div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end space-x-2">
                  <div class="w-8 h-8 bg-daba-cream-alt dark:bg-slate-700 rounded-lg"></div>
                  <div class="w-8 h-8 bg-daba-cream-alt dark:bg-slate-700 rounded-lg"></div>
                  <div class="w-8 h-8 bg-daba-cream-alt dark:bg-slate-700 rounded-lg"></div>
                </div>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-else-if="filteredProducts.length === 0">
              <td colspan="5" class="px-6 py-16 text-center text-daba-slate dark:text-slate-400 text-sm font-medium">
                <div class="flex flex-col items-center justify-center space-y-2">
                  <Package class="w-10 h-10 text-daba-slate-dark dark:text-slate-500 stroke-[1.5]" />
                  <p>Aucun produit ne correspond à votre recherche</p>
                </div>
              </td>
            </tr>

            <!-- Data Rows -->
            <tr v-else v-for="product in filteredProducts" :key="product.id" class="product-row hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-xl bg-daba-cream-alt dark:bg-daba-dark-card flex-shrink-0 overflow-hidden border border-daba-cream-alt dark:border-daba-dark-border">
                    <img :src="getImageUrl(product.image_url)" :alt="product.name" class="w-full h-full object-cover">
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-bold text-daba-navy dark:text-white">{{ product.name }}</div>
                    <div class="text-xs text-daba-slate dark:text-daba-slate-dark truncate max-w-[200px]">{{ product.slug }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-daba-cream-alt dark:bg-daba-dark-card text-slate-600 dark:text-slate-300">
                  {{ product.category_name }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-daba-navy dark:text-white">{{ product.price }} FCFA</div>
                <div class="text-xs" :class="product.stock > 10 ? 'text-daba-green dark:text-daba-green' : 'text-rose-600 dark:text-rose-400'">
                  {{ product.stock }} en stock
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center justify-center space-x-3">
                  <!-- Nouveauté -->
                  <button 
                    @click="toggleFeature(product, 'newest')"
                    class="p-1.5 rounded-lg transition-all"
                    :class="product.is_newest ? 'bg-daba-cream-alt text-daba-orange' : 'text-slate-300 hover:text-slate-400'"
                    :title="product.is_newest ? 'Retirer des nouveautés' : 'Marquer comme nouveauté'"
                  >
                    <Sparkles class="w-4 h-4" />
                  </button>
                  <!-- Tendance / Bestseller -->
                  <button 
                    @click="toggleFeature(product, 'bestseller')"
                    class="p-1.5 rounded-lg transition-all"
                    :class="product.is_bestseller ? 'bg-daba-cream-alt text-daba-orange' : 'text-slate-300 hover:text-slate-400'"
                    :title="product.is_bestseller ? 'Retirer des tendances' : 'Marquer comme tendance'"
                  >
                    <TrendingUp class="w-4 h-4" />
                  </button>
                  <!-- Offre Spéciale -->
                  <button 
                    @click="toggleFeature(product, 'offer')"
                    class="p-1.5 rounded-lg transition-all"
                    :class="product.is_special_offer ? 'bg-daba-cream-alt text-daba-green' : 'text-slate-300 hover:text-slate-400'"
                    :title="product.is_special_offer ? 'Retirer de l\'offre' : 'Mettre en offre spéciale'"
                  >
                    <Gift class="w-4 h-4" />
                  </button>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <router-link 
                    :to="`/admin/products/${product.id}/images`"
                    class="p-2 text-daba-slate hover:text-daba-navy dark:text-daba-slate-dark dark:hover:text-white rounded-lg hover:bg-daba-cream-alt transition-colors"
                    title="Gérer les images"
                  >
                    <ImageIcon class="w-4 h-4" />
                  </router-link>
                  <button 
                    @click="editProduct(product)"
                    class="p-2 text-daba-slate hover:text-daba-orange rounded-lg hover:bg-daba-cream-alt transition-colors"
                    title="Modifier"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>
                  <button 
                    @click="deleteProduct(product.id)"
                    class="p-2 text-daba-slate hover:text-rose-600 rounded-lg hover:bg-daba-cream-alt transition-colors"
                    title="Supprimer"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal Form -->
    <div v-if="showCreateForm || editingProduct" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-daba-cream dark:bg-daba-dark-card rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-daba-cream-alt dark:border-daba-dark-border transition-colors">
        <div class="flex items-center justify-between pb-4 border-b border-daba-cream-alt dark:border-daba-dark-border mb-6">
          <h2 class="text-xl font-bold text-daba-navy dark:text-white">
            {{ editingProduct ? 'Modifier le produit' : 'Créer un nouveau produit' }}
          </h2>
          <button @click="cancelEdit" class="text-daba-slate hover:text-daba-navy dark:hover:text-white p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="saveProduct" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase mb-1">Nom du produit</label>
              <input 
                v-model="productForm.name" 
                @input="generateSlug"
                required
                class="w-full px-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-xl focus:outline-none focus:ring-2 focus:ring-daba-orange/20 dark:text-white"
                placeholder="Ex: Robe d'été fleurie"
              >
            </div>
            <div>
              <label class="block text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase mb-1">Slug (URL)</label>
              <input 
                v-model="productForm.slug" 
                required
                class="w-full px-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-xl focus:outline-none focus:ring-2 focus:ring-daba-orange/20 dark:text-white"
                placeholder="Ex: robe-ete-fleurie"
              >
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase mb-1">Description</label>
            <textarea 
              v-model="productForm.description" 
              rows="3"
              class="w-full px-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-xl focus:outline-none focus:ring-2 focus:ring-daba-orange/20 dark:text-white"
              placeholder="Description détaillée du produit..."
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase mb-1">Prix (FCFA)</label>
              <input 
                v-model="productForm.price" 
                type="number"
                step="0.01"
                required
                class="w-full px-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-xl focus:outline-none focus:ring-2 focus:ring-daba-orange/20 dark:text-white"
                placeholder="0.00"
              >
            </div>
            <div>
              <label class="block text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase mb-1">Stock</label>
              <input 
                v-model="productForm.stock" 
                type="number"
                required
                class="w-full px-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-xl focus:outline-none focus:ring-2 focus:ring-daba-orange/20 dark:text-white"
                placeholder="10"
              >
            </div>
            <div>
              <label class="block text-xs font-bold text-daba-slate dark:text-daba-slate-dark uppercase mb-1">Catégorie</label>
              <select 
                v-model="productForm.category_id" 
                required
                class="w-full px-4 py-2 bg-daba-cream-alt dark:bg-daba-dark-card/50 border border-daba-cream-alt dark:border-daba-dark-border rounded-xl focus:outline-none focus:ring-2 focus:ring-daba-orange/20 dark:text-white"
              >
                <option value="">Sélectionner</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
          </div>

          <div class="flex items-center space-x-2 pt-2">
            <input type="checkbox" id="is_active" v-model="productForm.is_active" class="rounded border-gray-300 text-daba-orange focus:ring-daba-orange/20">
            <label for="is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">Produit visible dans la boutique</label>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-6 border-t border-daba-cream-alt dark:border-daba-dark-border">
            <button 
              type="button" 
              @click="cancelEdit"
              class="px-4 py-2 text-sm font-bold text-daba-slate hover:text-daba-navy dark:hover:text-white rounded-xl hover:bg-daba-cream-alt transition-colors"
            >
              Annuler
            </button>
            <button 
              type="submit"
              class="px-6 py-2 bg-daba-orange text-white text-sm font-bold rounded-xl hover:bg-daba-navy transition-colors"
            >
              {{ editingProduct ? 'Mettre à jour' : 'Créer & Ajouter Images' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import adminService from '@/services/adminService.js'
import { 
  Plus, Search, Edit3, Trash2, ImageIcon, X, 
  Sparkles, TrendingUp, Gift, Check, RotateCw, Package
} from 'lucide-vue-next'
import { gsap } from 'gsap'
import { getProductImageUrl } from '@/utils/imageHelper'

const router = useRouter()
const products = ref([])
const categories = ref([])
const isLoading = ref(true)
const showCreateForm = ref(false)
const editingProduct = ref(null)
const filters = ref({ search: '', category: '', status: '', bestsellers: false })

const productForm = ref({
  name: '',
  slug: '',
  description: '',
  price: '',
  stock: '',
  category_id: '',
  is_active: true
})

const filteredProducts = computed(() => {
  return products.value.filter(product => {
    const matchesSearch = !filters.value.search || 
      (product.name && product.name.toLowerCase().includes(filters.value.search.toLowerCase()))
    const matchesCategory = !filters.value.category || 
      product.category_id == filters.value.category
    const matchesStatus = !filters.value.status || 
      (filters.value.status === 'active' ? product.status === 'published' : product.status !== 'published')
    const matchesBestseller = !filters.value.bestsellers || product.is_bestseller
    
    return matchesSearch && matchesCategory && matchesStatus && matchesBestseller
  })
})

const loadProducts = async () => {
  isLoading.value = true
  try {
    const response = await adminService.getProducts({ per_page: 200 })
    if (response.success) products.value = response.products
  } catch (err) { 
    console.error(err) 
  } finally {
    isLoading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await adminService.getCategories()
    if (response.success) categories.value = response.categories
  } catch (err) { console.error(err) }
}

const editProduct = (product) => {
  editingProduct.value = product
  productForm.value = { ...product }
}

const toggleFeature = async (product, type) => {
  try {
    let res
    if (type === 'newest') {
      const target = !product.is_newest
      res = await adminService.toggleNewest(product.id, target)
      if (res.success) product.is_newest = target
    } else if (type === 'bestseller') {
      const target = !product.is_bestseller
      res = await adminService.toggleBestseller(product.id, target)
      if (res.success) product.is_bestseller = target
    } else if (type === 'offer') {
      const target = !product.is_special_offer
      res = await adminService.toggleSpecialOffer(product.id, target)
      if (res.success) product.is_special_offer = target
    }
  } catch (err) { console.error(err) }
}

const generateSlug = () => {
  if (productForm.value.name) {
    productForm.value.slug = productForm.value.name
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-0]/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '')
  }
}

const saveProduct = async () => {
  try {
    let response
    if (editingProduct.value) {
      response = await adminService.updateProduct({ ...productForm.value, id: editingProduct.value.id })
    } else {
      response = await adminService.createProduct(productForm.value)
    }
    
    if (response.success) {
      await loadProducts()
      const pid = editingProduct.value ? editingProduct.value.id : response.product_id
      if (!editingProduct.value) {
        router.push(`/admin/products/${pid}/images`)
      }
      cancelEdit()
    }
  } catch (err) { console.error(err) }
}

const deleteProduct = async (id) => {
  if (confirm('Supprimer ce produit définitivement ?')) {
    try {
      const res = await adminService.deleteProduct(id)
      if (res.success) loadProducts()
    } catch (err) { console.error(err) }
  }
}

const getImageUrl = (url) => getProductImageUrl(url)

const cancelEdit = () => {
  showCreateForm.value = false
  editingProduct.value = null
  productForm.value = { name: '', slug: '', description: '', price: '', stock: '', category_id: '', is_active: true }
}

// --- GSAP Animation ---
let ctx = null

const animateTableRows = async () => {
  await nextTick()
  if (ctx) ctx.revert()
  
  ctx = gsap.context(() => {
    if (document.querySelector('.product-row')) {
      gsap.from('.product-row', {
        y: 20,
        opacity: 0,
        duration: 0.4,
        stagger: 0.05,
        ease: 'power2.out',
        clearProps: 'all'
      })
    }
  })
}

watch(filteredProducts, () => {
  animateTableRows()
})

onMounted(async () => {
  await nextTick()
  
  try {
    await Promise.all([
      loadProducts(),
      loadCategories()
    ])
  } catch (err) {
    console.error('Load error:', err)
  }
  
  const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })
  
  if (document.querySelector('.product-header')) {
    tl.from('.product-header', { y: -30, opacity: 0, duration: 0.8 })
  }
  if (document.querySelector('.product-filter')) {
    tl.from('.product-filter', { y: -20, opacity: 0, duration: 0.6 }, '-=0.4')
  }
  if (document.querySelector('.product-table-container')) {
    tl.from('.product-table-container', { y: 30, opacity: 0, duration: 0.8 }, '-=0.4')
  }
  
  animateTableRows()
})

onUnmounted(() => {
  if (ctx) ctx.revert()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #475569;
}
</style>
