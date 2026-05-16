<template>
  <div class="min-h-screen bg-slate-50 pt-32 pb-20">
    <div class="container mx-auto px-4 md:px-6">
      
      <!-- Top Action: Back to Home -->
      <div class="flex justify-end mb-8">
        <router-link to="/" class="flex items-center gap-2 px-6 py-3 bg-white text-purple-600 font-bold uppercase tracking-widest text-xs rounded-2xl hover:bg-purple-50 transition-all shadow-sm border border-purple-100">
          <Icon icon="solar:home-bold-duotone" class="w-5 h-5" />
          Retour à l'accueil
        </router-link>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full lg:w-80 shrink-0">
          <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 border border-slate-100">
            <div class="flex items-center gap-4 mb-10">
              <div class="w-16 h-16 rounded-full bg-purple-600 flex items-center justify-center text-white text-2xl font-black uppercase ring-4 ring-purple-50">
                {{ user?.first_name?.charAt(0) || 'U' }}
              </div>
              <div>
                <h2 class="text-xl font-black text-slate-900 truncate">{{ user?.first_name }} {{ user?.last_name }}</h2>
                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest">{{ user?.role === 'admin' ? 'Administrateur' : 'Client Privilège' }}</p>
              </div>
            </div>

            <nav class="space-y-2">
              <button 
                v-for="item in menuItems" 
                :key="item.id"
                @click="activeTab = item.id"
                class="w-full flex items-center justify-between px-6 py-4 rounded-2xl transition-all font-black uppercase tracking-widest text-[10px]"
                :class="activeTab === item.id ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600'">
                <div class="flex items-center gap-3">
                  <Icon :icon="item.icon" class="w-5 h-5" />
                  {{ item.label }}
                </div>
                <Icon v-if="activeTab === item.id" icon="solar:arrow-right-linear" class="w-4 h-4" />
              </button>

              <div class="pt-6 mt-6 border-t border-slate-100">
                <button @click="handleLogout" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-rose-500 font-black uppercase tracking-widest text-[10px] hover:bg-rose-50 transition-all">
                  <Icon icon="solar:logout-linear" class="w-5 h-5" />
                  Déconnexion
                </button>
              </div>
            </nav>
          </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
          <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 md:p-12 border border-slate-100 min-h-[600px]">
            
            <!-- Dashboard Overview -->
            <div v-if="activeTab === 'overview'" class="space-y-10">
              <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                <h1 class="text-3xl font-black text-slate-900 italic uppercase">Tableau de <span class="text-purple-600">bord</span></h1>
                <p class="text-slate-400 font-bold">Bienvenue, {{ user?.first_name }} ! 👋</p>
              </div>

              <!-- Stats Grid -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-purple-50 p-8 rounded-[2rem] border border-purple-100">
                  <Icon icon="solar:bag-bold-duotone" class="w-10 h-10 text-purple-600 mb-4" />
                  <p class="text-sm font-black text-purple-900 uppercase tracking-widest opacity-60">Commandes</p>
                  <p class="text-3xl font-black text-purple-900">{{ orders.length }}</p>
                </div>
                <div class="bg-purple-50 p-8 rounded-[2rem] border border-purple-100">
                  <Icon icon="solar:heart-bold-duotone" class="w-10 h-10 text-purple-600 mb-4" />
                  <p class="text-sm font-black text-purple-900 uppercase tracking-widest opacity-60">Favoris</p>
                  <p class="text-3xl font-black text-purple-900">{{ wishlistCount }}</p>
                </div>
                <div class="bg-blue-50 p-8 rounded-[2rem] border border-blue-100">
                  <Icon icon="solar:wallet-bold-duotone" class="w-10 h-10 text-blue-600 mb-4" />
                  <p class="text-sm font-black text-blue-900 uppercase tracking-widest opacity-60">Dépenses</p>
                  <p class="text-3xl font-black text-blue-900">{{ totalSpent.toLocaleString() }} <span class="text-xs">FCFA</span></p>
                </div>
              </div>

              <!-- Recent Activity -->
              <div>
                <h3 class="text-xl font-black text-slate-900 mb-6 uppercase tracking-tight">Activité Récente</h3>
                <div v-if="orders.length === 0" class="text-center py-12 bg-slate-50 rounded-[2rem]">
                  <p class="text-slate-400 font-bold">Vous n'avez pas encore passé de commande.</p>
                  <router-link to="/" class="mt-4 inline-block text-purple-600 font-black uppercase tracking-widest text-[10px]">Commencer mes achats →</router-link>
                </div>
                <div v-else class="space-y-4">
                   <div v-for="order in orders.slice(0, 3)" :key="order.id" class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100">
                     <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center font-black text-slate-900 shadow-sm border border-slate-100">#{{ order.id }}</div>
                        <div>
                          <p class="text-sm font-black text-slate-900 uppercase">Commande du {{ formatDate(order.created_at) }}</p>
                          <p class="text-xs text-slate-400 font-bold">{{ order.total_amount.toLocaleString() }} FCFA • {{ order.status }}</p>
                        </div>
                     </div>
                     <button class="px-4 py-2 bg-white rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm">Détails</button>
                   </div>
                </div>
              </div>
            </div>

            <!-- Orders Tab -->
            <div v-if="activeTab === 'orders'" class="space-y-8">
              <h1 class="text-3xl font-black text-slate-900 italic uppercase">Mes <span class="text-purple-600">Commandes</span></h1>
              <div class="overflow-x-auto">
                <table class="w-full text-left">
                  <thead>
                    <tr class="border-b border-slate-100">
                      <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">N°</th>
                      <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                      <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
                      <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                      <th class="pb-4"></th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-50">
                    <tr v-for="order in orders" :key="order.id" class="group hover:bg-slate-50 transition-colors">
                      <td class="py-6 font-black text-slate-900">#{{ order.id }}</td>
                      <td class="py-6 text-sm text-slate-500 font-medium">{{ formatDate(order.created_at) }}</td>
                      <td class="py-6">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                          :class="getStatusClass(order.status)">
                          {{ order.status }}
                        </span>
                      </td>
                      <td class="py-6 font-black text-slate-900">{{ order.total_amount.toLocaleString() }} FCFA</td>
                      <td class="py-6 text-right">
                        <button class="text-purple-600 hover:text-purple-900 font-black uppercase tracking-widest text-[10px]">Facture</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Profile Tab -->
            <div v-if="activeTab === 'profile'" class="space-y-10">
              <h1 class="text-3xl font-black text-slate-900 italic uppercase">Mon <span class="text-purple-600">Profil</span></h1>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Prénom</label>
                    <input type="text" v-model="userForm.first_name" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 font-bold text-slate-900">
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nom</label>
                    <input type="text" v-model="userForm.last_name" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 font-bold text-slate-900">
                  </div>
                </div>
                <div class="space-y-6">
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</label>
                    <input type="email" :value="user?.email" disabled class="w-full px-6 py-4 bg-slate-100 border border-slate-100 rounded-2xl focus:outline-none font-bold text-slate-400 cursor-not-allowed">
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Téléphone</label>
                    <input type="text" v-model="userForm.phone" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 font-bold text-slate-900">
                  </div>
                </div>
              </div>
              <div class="flex justify-end pt-6">
                <button @click="updateProfile" class="px-10 py-5 bg-slate-900 text-white font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-purple-600 transition-all shadow-xl shadow-slate-900/10">Sauvegarder les modifications</button>
              </div>
            </div>

            <!-- Addresses Tab -->
            <div v-if="activeTab === 'addresses'" class="space-y-10">
              <div class="flex items-center justify-between">
                <h1 class="text-3xl font-black text-slate-900 italic uppercase">Mes <span class="text-purple-600">Adresses</span></h1>
                <button class="flex items-center gap-2 text-purple-600 font-bold uppercase tracking-widest text-[10px]">
                  <Icon icon="solar:add-circle-bold" class="w-5 h-5" /> Ajouter
                </button>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-8 border border-slate-100 bg-slate-50 rounded-[2rem] relative group">
                  <div class="flex items-center gap-3 mb-4">
                    <Icon icon="solar:home-bold-duotone" class="w-6 h-6 text-purple-600" />
                    <h4 class="font-black text-slate-900 uppercase text-xs tracking-widest">Résidence Principale</h4>
                  </div>
                  <p class="text-slate-500 font-bold leading-relaxed">Cotonou, Quartier Fidjrossé<br>Rue 1245, Porte 254<br>Bénin</p>
                  <div class="mt-6 flex gap-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="text-[10px] font-black text-slate-900 uppercase tracking-widest underline">Modifier</button>
                    <button class="text-[10px] font-black text-rose-500 uppercase tracking-widest underline">Supprimer</button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import { useAuthStore } from '../stores/auth';
import { useWishlistStore } from '../stores/wishlist';
import api from '../services/api';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const wishlistStore = useWishlistStore();
const user = computed(() => authStore.user);
const wishlistCount = computed(() => wishlistStore.totalItems);

const activeTab = ref('overview');
const orders = ref([]);
const totalSpent = ref(0);

const menuItems = [
  { id: 'overview', label: 'Vue d\'ensemble', icon: 'solar:widget-bold-duotone' },
  { id: 'orders', label: 'Commandes', icon: 'solar:bag-bold-duotone' },
  { id: 'profile', label: 'Profil', icon: 'solar:user-bold-duotone' },
  { id: 'addresses', label: 'Adresses', icon: 'solar:map-point-bold-duotone' },
];

const userForm = ref({
  first_name: user.value?.first_name || '',
  last_name: user.value?.last_name || '',
  phone: user.value?.phone || '',
});

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'long', year: 'numeric'
  });
};

const getStatusClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'livré': return 'bg-green-100 text-green-700';
    case 'en cours': return 'bg-blue-100 text-blue-700';
    case 'annulé': return 'bg-rose-100 text-rose-700';
    default: return 'bg-slate-100 text-slate-700';
  }
};

const fetchOrders = async () => {
  try {
    const response = await api.get('/orders/get_user_orders.php');
    orders.value = response.data.data || [];
    totalSpent.value = orders.value.reduce((acc, order) => acc + parseFloat(order.total_amount), 0);
  } catch (err) {
    console.warn('Orders fetch failed:', err);
  }
};

const handleLogout = async () => {
  const result = await Swal.fire({
    title: 'Déconnexion ?',
    text: 'Voulez-vous vraiment vous déconnecter ?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#9333ea',
    confirmButtonText: 'Oui, déconnexion',
    cancelButtonText: 'Annuler'
  });

  if (result.isConfirmed) {
    await authStore.logout();
    window.location.href = '/';
  }
};

const updateProfile = async () => {
  try {
    // await api.post('/auth/update_profile.php', userForm.value);
    Swal.fire({ icon: 'success', title: 'Profil mis à jour !', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Erreur lors de la mise à jour' });
  }
};

onMounted(() => {
  if (authStore.isAuthenticated) {
    fetchOrders();
  }
});
</script>

<style scoped>
/* Custom animations if needed */
</style>

