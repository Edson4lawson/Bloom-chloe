<template>
  <div class="space-y-6">
    <div class="offer-header flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Gestion des Offres Spéciales</h1>
      <div class="text-sm text-slate-500 dark:text-slate-400">
        {{ specialOffers.length }} produit(s) en promotion
      </div>
    </div>

    <!-- Info Box -->
    <div class="offer-info bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4 flex items-start space-x-3">
      <Info class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
      <p class="text-sm text-blue-700 dark:text-blue-300">
        Les produits marqués comme "Offre Spéciale" apparaîtront automatiquement dans le slider de la page d'accueil (Offres Spéciales).
      </p>
    </div>

    <!-- Products Selector -->
    <div class="offer-table-container bg-white dark:bg-[rgb(43,44,43)] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-500 overflow-hidden">
      <div class="offer-filter p-6 border-b border-slate-100 dark:border-slate-500">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
          <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
            <input 
              v-model="search"
              type="text" 
              placeholder="Rechercher un produit à ajouter..." 
              class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all dark:text-white"
            />
          </div>
          <select v-model="filter" class="px-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/20 dark:text-white">
            <option value="all">Tous les produits</option>
            <option value="offers">Uniquement les offres</option>
            <option value="none">Hors offres</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-500">
          <thead class="bg-slate-50 dark:bg-slate-900/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Produit</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Prix</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Statut Offre</th>
              <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-[rgb(43,44,43)] divide-y divide-slate-100 dark:divide-slate-500">
            <tr v-for="product in filteredProducts" :key="product.id" class="offer-row hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex-shrink-0 mr-3 overflow-hidden">
                    <img v-if="product.main_image" :src="getImageUrl(product.main_image)" class="w-full h-full object-cover" />
                    <Package v-else class="w-full h-full p-2 text-slate-400" />
                  </div>
                  <div>
                    <div class="text-sm font-medium text-slate-900 dark:text-white">{{ product.name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ product.category_name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 dark:text-white">
                {{ product.price }}FCFA
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                  :class="product.is_special_offer ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400'"
                >
                  {{ product.is_special_offer ? 'En Promotion' : 'Standard' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <button 
                  @click="toggleOffer(product)"
                  :disabled="loadingId === product.id"
                  class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-all"
                  :class="product.is_special_offer 
                    ? 'text-rose-600 hover:bg-rose-50 border border-rose-200' 
                    : 'text-emerald-600 hover:bg-emerald-50 border border-emerald-200'"
                >
                  <Gift v-if="!product.is_special_offer" class="w-3.5 h-3.5 mr-1.5" />
                  <X v-else class="w-3.5 h-3.5 mr-1.5" />
                  {{ product.is_special_offer ? 'Retirer l\'offre' : 'Marquer comme Offre' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import adminService from '@/services/adminService'
import { Search, Package, Gift, X, Info } from 'lucide-vue-next'
import { gsap } from 'gsap'
import { getProductImageUrl } from '@/utils/imageHelper'

const products = ref([])
const search = ref('')
const filter = ref('all')
const loadingId = ref(null)

const loadProducts = async () => {
  try {
    const res = await adminService.getProducts()
    if (res.success) {
      products.value = res.products
    }
  } catch (err) {
    console.error(err)
  }
}

const specialOffers = computed(() => products.value.filter(p => p.is_special_offer))

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchesSearch = p.name.toLowerCase().includes(search.value.toLowerCase())
    if (!matchesSearch) return false
    
    if (filter.value === 'offers') return p.is_special_offer
    if (filter.value === 'none') return !p.is_special_offer
    return true
  })
})

const getImageUrl = (path) => getProductImageUrl(path)

const toggleOffer = async (product) => {
  loadingId.value = product.id
  try {
    const newStatus = !product.is_special_offer
    const res = await adminService.toggleSpecialOffer(product.id, newStatus)
    if (res.success) {
      product.is_special_offer = newStatus
    }
  } catch (err) {
    console.error(err)
  } finally {
    loadingId.value = null
  }
}

// --- GSAP Animations ---
let ctx = null

const animateRows = async () => {
    await nextTick()
    if (ctx) ctx.revert()
    
    ctx = gsap.context(() => {
        gsap.from(".offer-row", {
            y: 20,
            opacity: 0,
            duration: 0.4,
            stagger: 0.05,
            ease: "power2.out",
            clearProps: "all"
        })
    })
}

watch(filteredProducts, () => {
    animateRows()
})

onMounted(async () => {
    await loadProducts()
    
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })
    
    tl.from(".offer-header", {
        y: -30,
        opacity: 0,
        duration: 0.8
    })
    .from(".offer-info", {
        scale: 0.95,
        opacity: 0,
        duration: 0.6
    }, "-=0.4")
    .from(".offer-table-container", {
        y: 30,
        opacity: 0,
        duration: 0.8
    }, "-=0.4")
    
    animateRows()
})

onUnmounted(() => {
    if (ctx) ctx.revert()
})
</script>
