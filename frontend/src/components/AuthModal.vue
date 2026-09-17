<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-[200] p-4 overflow-y-auto" @click.self="$emit('close')">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 relative max-h-[90vh] overflow-y-auto border border-purple-100">
      <!-- Close Button -->
      <button @click="$emit('close')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-purple-50 flex items-center justify-center text-gray-400 hover:text-purple-600 hover:bg-purple-100 transition-all">
        <Icon icon="mdi:close" class="w-5 h-5" />
      </button>

      <!-- Logo / Header -->
      <div class="text-center mb-6">
        <img src="../assets/bloom-icone.png" alt="Bloom Chloé" class="w-14 h-14 mx-auto mb-2 drop-shadow-sm">
        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Bloom Chloé</h3>
        <p class="text-xs text-purple-600 font-medium tracking-wide uppercase">Parfumerie & Cosmétiques de Luxe</p>
      </div>

      <!-- Tabs -->
      <div class="flex border-b border-gray-100 mb-6 bg-gray-50/80 p-1 rounded-2xl">
        <button 
          @click="activeTab = 'login'; error = ''" 
          :class="['flex-1 py-2.5 text-center font-bold text-sm rounded-xl transition-all', 
                   activeTab === 'login' ? 'bg-white text-purple-600 shadow-md' : 'text-gray-500 hover:text-gray-800']">
          Connexion
        </button>
        <button 
          @click="activeTab = 'register'; error = ''" 
          :class="['flex-1 py-2.5 text-center font-bold text-sm rounded-xl transition-all', 
                   activeTab === 'register' ? 'bg-white text-purple-600 shadow-md' : 'text-gray-500 hover:text-gray-800']">
          Inscription
        </button>
      </div>

      <!-- Login Form -->
      <form v-if="activeTab === 'login'" @submit.prevent="handleLogin" class="space-y-4">
        <h2 class="text-2xl font-black text-gray-900 mb-1">Bienvenue !</h2>
        <p class="text-xs text-gray-500 mb-4">Connectez-vous pour retrouver vos commandes et vos favoris.</p>
        
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Email</label>
          <div class="relative">
            <Icon icon="solar:letter-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input 
              v-model="loginForm.email" 
              type="email" 
              required
              class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all"
              placeholder="votre@email.com">
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Mot de passe</label>
          </div>
          <div class="relative">
            <Icon icon="solar:lock-password-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input 
              v-model="loginForm.password" 
              :type="showPassword ? 'text' : 'password'" 
              required
              class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all"
              placeholder="••••••••">
            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
              <Icon :icon="showPassword ? 'solar:eye-bold' : 'solar:eye-closed-linear'" class="w-4 h-4" />
            </button>
          </div>
        </div>

        <div v-if="error" class="p-3 bg-red-50 border border-red-100 rounded-xl text-red-600 text-xs flex items-center gap-2">
          <Icon icon="solar:danger-triangle-bold" class="w-4 h-4 flex-shrink-0" />
          <span>{{ error }}</span>
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3.5 rounded-2xl font-bold shadow-lg shadow-purple-200 hover:shadow-xl hover:from-purple-700 hover:to-indigo-700 transition-all active:scale-98 disabled:opacity-50 text-sm">
          {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
        </button>
      </form>

      <!-- Register Form -->
      <form v-if="activeTab === 'register'" @submit.prevent="handleRegister" class="space-y-4">
        <h2 class="text-2xl font-black text-gray-900 mb-1">Créer un compte</h2>
        <p class="text-xs text-gray-500 mb-4">Rejoignez Bloom Chloé pour commander en toute simplicité.</p>
        
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Prénom</label>
            <input 
              v-model="registerForm.first_name" 
              type="text" 
              required
              class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all"
              placeholder="Ex: Sophie">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nom</label>
            <input 
              v-model="registerForm.last_name" 
              type="text" 
              required
              class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all"
              placeholder="Ex: Martin">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Email</label>
          <div class="relative">
            <Icon icon="solar:letter-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input 
              v-model="registerForm.email" 
              type="email" 
              required
              class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all"
              placeholder="votre@email.com">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Mot de passe</label>
          <div class="relative">
            <Icon icon="solar:lock-password-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input 
              v-model="registerForm.password" 
              :type="showRegisterPassword ? 'text' : 'password'" 
              required
              minlength="8"
              class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all"
              placeholder="••••••••">
            <button type="button" @click="showRegisterPassword = !showRegisterPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
              <Icon :icon="showRegisterPassword ? 'solar:eye-bold' : 'solar:eye-closed-linear'" class="w-4 h-4" />
            </button>
          </div>
          <p class="text-[11px] text-gray-400 mt-1">Au moins 8 caractères avec 1 majuscule, 1 minuscule et 1 chiffre</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Téléphone</label>
            <input 
              v-model="registerForm.phone" 
              type="tel"
              class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all"
              placeholder="+229 97 00 00 00">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Ville / Adresse</label>
            <input 
              v-model="registerForm.address" 
              type="text"
              class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all"
              placeholder="Cotonou">
          </div>
        </div>

        <div v-if="error" class="p-3 bg-red-50 border border-red-100 rounded-xl text-red-600 text-xs flex items-center gap-2">
          <Icon icon="solar:danger-triangle-bold" class="w-4 h-4 flex-shrink-0" />
          <span>{{ error }}</span>
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3.5 rounded-2xl font-bold shadow-lg shadow-purple-200 hover:shadow-xl hover:from-purple-700 hover:to-indigo-700 transition-all active:scale-98 disabled:opacity-50 text-sm">
          {{ loading ? 'Création du compte...' : 'S\'inscrire' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useScrollLock } from '@/composables/useScrollLock';
import Swal from 'sweetalert2';

// Verrouillage automatique du scroll en arrière-plan
useScrollLock();

const emit = defineEmits(['close', 'success']);

const authStore = useAuthStore();
const router = useRouter();
const activeTab = ref('login');
const loading = ref(false);
const error = ref('');
const showPassword = ref(false);
const showRegisterPassword = ref(false);

const loginForm = ref({
  email: '',
  password: ''
});

const registerForm = ref({
  email: '',
  password: '',
  first_name: '',
  last_name: '',
  phone: '',
  address: ''
});

const handleLogin = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    await authStore.login(loginForm.value.email, loginForm.value.password);
    
    Swal.fire({
      icon: 'success',
      title: 'Connexion réussie !',
      text: `Bienvenue ${authStore.user?.first_name || authStore.user?.email || 'sur Bloom Chloé'}`,
      timer: 2000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end'
    });
    
    const user = authStore.user;
    if (user && (user.role === 'admin' || user.role === 'super_admin')) {
      router.push({ name: 'AdminDashboard' });
    } else {
      router.push({ name: 'AccountPage' });
    }

    emit('success');
    emit('close');
  } catch (err) {
    error.value = typeof err === 'string' ? err : (err.message || 'Identifiants incorrects');
  } finally {
    loading.value = false;
  }
};

const handleRegister = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    await authStore.register(registerForm.value);
    
    Swal.fire({
      icon: 'success',
      title: 'Compte créé avec succès !',
      text: `Bienvenue ${authStore.user?.first_name || 'sur Bloom Chloé'}`,
      timer: 2500,
      showConfirmButton: false,
      toast: true,
      position: 'top-end'
    });
    
    const user = authStore.user;
    if (user && (user.role === 'admin' || user.role === 'super_admin')) {
      router.push({ name: 'AdminDashboard' });
    } else {
      router.push({ name: 'AccountPage' });
    }

    emit('success');
    emit('close');
  } catch (err) {
    error.value = typeof err === 'string' ? err : (err.message || 'Erreur lors de la création du compte');
  } finally {
    loading.value = false;
  }
};
</script>
