<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="category-header opacity-0 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Gestion des Catégories</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Organisez votre catalogue par thématiques</p>
      </div>
      <button 
        @click="openCreateModal" 
        class="inline-flex items-center px-4 py-2 bg-accent text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-all "
      >
        <Plus class="w-4 h-4 mr-2" />
        Nouvelle Catégorie
      </button>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div 
        v-for="category in categories" 
        :key="category.id" 
        class="category-card-anim opacity-0 group bg-white dark:bg-[rgb(43,44,43)] rounded-3xl border border-slate-100 dark:border-slate-500 shadow-sm hover:shadow-xl hover:border-accent/20 transition-all duration-300 overflow-hidden"
      >
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl group-hover:bg-accent/10 transition-colors">
              <FolderTree class="w-6 h-6 text-slate-400 group-hover:text-accent" />
            </div>
            <div class="flex space-x-1 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
              <button 
                @click="editCategory(category)" 
                class="p-2 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all"
                title="Modifier"
              >
                <Edit3 class="w-4 h-4" />
              </button>
              <button 
                @click="confirmDelete(category)" 
                class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all"
                title="Supprimer"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
          
          <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">{{ category.name }}</h3>
          <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mb-4 uppercase tracking-widest">/{{ category.slug }}</p>
          
          <div class="flex items-center justify-between pt-4 border-t border-slate-50 dark:border-slate-500">
            <div class="flex items-center space-x-2">
              <Package class="w-4 h-4 text-slate-400" />
              <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ category.product_count || 0 }} produits</span>
            </div>
            <span 
              class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
              :class="category.is_active ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400'"
            >
              {{ category.is_active ? 'Active' : 'Brouillon' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Add Card Placeholder -->
      <button 
        @click="openCreateModal"
        class="add-category-card-anim opacity-0 h-full min-h-[220px] rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center space-y-3 text-slate-400 hover:border-accent hover:text-accent hover:bg-accent/5 transition-all group"
      >
        <div class="p-4 rounded-full bg-slate-50 group-hover:bg-accent/10 transition-colors">
          <Plus class="w-8 h-8" />
        </div>
        <span class="font-bold">Ajouter une catégorie</span>
      </button>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-slate-900/50 dark:bg-slate-950/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200 border border-transparent dark:border-slate-500">
        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-500 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/20">
          <h2 class="text-xl font-bold text-slate-800 dark:text-white">{{ editingCategory ? 'Éditer la catégorie' : 'Nouvelle catégorie' }}</h2>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            <X class="w-6 h-6" />
          </button>
        </div>
        
        <form @submit.prevent="saveCategory" class="p-8 space-y-6">
          <div class="space-y-1">
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nom de la catégorie</label>
            <input 
              v-model="categoryForm.name" 
              @input="generateSlug" 
              placeholder="Ex: Électronique"
              required 
              class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all font-medium dark:text-white"
            >
          </div>
          
          <div class="space-y-1">
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lien (Slug)</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">/</span>
              <input 
                v-model="categoryForm.slug" 
                placeholder="electronique"
                required 
                class="w-full pl-7 pr-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all font-medium text-slate-600 dark:text-slate-200"
              >
            </div>
          </div>
          
          <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
            <div class="flex items-center space-x-3 cursor-pointer" @click="categoryForm.is_active = !categoryForm.is_active">
              <div class="relative inline-flex items-center">
                <input type="checkbox" v-model="categoryForm.is_active" class="sr-only peer">
                <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
              </div>
              <div>
                <span class="block text-sm font-bold text-slate-700 dark:text-slate-300">Catégorie active</span>
                <span class="block text-[10px] text-slate-400 dark:text-slate-500 uppercase font-bold tracking-tight">Visible par les clients</span>
              </div>
            </div>
          </div>
          
          <div class="flex space-x-3 pt-4">
            <button 
              type="button" 
              @click="closeModal" 
              class="flex-1 px-4 py-3 text-sm font-bold text-slate-500 border-light border-1 hover:bg-slate-50 hover:cursor-pointer rounded-2xl transition-colors"
            >
              Annuler
            </button>
            <button 
              type="submit" 
              :disabled="loading"
              class="flex-1 px-4 py-3 bg-accent text-white text-sm font-bold hover:cursor-pointer rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-black/10 flex items-center justify-center"
            >
              <Loader2 v-if="loading" class="w-4 h-4 animate-spin mr-2" />
              {{ editingCategory ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import adminService from '../../Services/adminService.js'
import { 
  Plus, Edit3, Trash2, FolderTree, Package, 
  X, Loader2, Info 
} from 'lucide-vue-next'
import { gsap } from 'gsap'

const categories = ref([])
const showModal = ref(false)
const editingCategory = ref(null)
const loading = ref(false)

const categoryForm = ref({
  name: '',
  slug: '',
  is_active: true
})

// --- GSAP Animation ---
let ctx = null

const runAnimations = async () => {
    await nextTick()
    if (ctx) ctx.revert()
    
    ctx = gsap.context(() => {
        const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })
        
        tl.to(".category-header", {
            y: 0,
            opacity: 1,
            duration: 0.8
        })
        .from(".category-header", {
            y: -30,
            immediateRender: false
        }, "<")


        tl.to(".category-card-anim", {
            scale: 1,
            y: 0,
            opacity: 1,
            stagger: 0.1,
            duration: 0.6,
            ease: "back.out(1.5)"
        }, "-=0.4")
        .from(".category-card-anim", {
            scale: 0.9,
            y: 30,
            immediateRender: false
        }, "<")

        tl.to(".add-category-card-anim", {
            opacity: 1,
            scale: 1,
            duration: 0.6
        }, "-=0.2")
        .from(".add-category-card-anim", {
           opacity: 0,
           scale: 0.9,
           immediateRender: false
        }, "<")
    })
}

const loadCategories = async () => {
  try {
    const res = await adminService.getCategories()
    if (res.success) {
        categories.value = res.categories
        runAnimations()
    }
  } catch (err) {
    console.error('Erreur chargement catégories:', err)
  }
}
// ... (rest of the script)
const cancelEdit = () => {
  showModal.value = false
  editingCategory.value = null
  categoryForm.value = { name: '', slug: '', is_active: true }
}

const openCreateModal = () => {
  editingCategory.value = null
  categoryForm.value = { name: '', slug: '', is_active: true }
  showModal.value = true
}

const editCategory = (category) => {
  editingCategory.value = category
  categoryForm.value = { ...category }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingCategory.value = null
}

const generateSlug = () => {
  if (categoryForm.value.name) {
    categoryForm.value.slug = categoryForm.value.name
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '')
  }
}

const saveCategory = async () => {
  loading.value = true
  try {
    let res
    if (editingCategory.value) {
      res = await adminService.updateCategory(editingCategory.value.id, categoryForm.value)
    } else {
      res = await adminService.createCategory(categoryForm.value)
    }
    
    if (res.success) {
      await loadCategories()
      closeModal()
    }
  } catch (err) {
    alert("Erreur lors de l'enregistrement")
  } finally {
    loading.value = false
  }
}

const confirmDelete = async (category) => {
  if (category.product_count > 0) {
    alert(`Impossible de supprimer cette catégorie car elle contient ${category.product_count} produits. Déplacez-les d'abord.`)
    return
  }
  
  if (confirm(`Voulez-vous vraiment supprimer la catégorie "${category.name}" ?`)) {
    try {
      const res = await adminService.deleteCategory(category.id)
      if (res.success) loadCategories()
    } catch (err) {
      console.error(err)
    }
  }
}

onMounted(loadCategories)

onUnmounted(() => {
    if (ctx) ctx.revert()
})
</script>