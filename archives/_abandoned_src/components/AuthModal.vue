<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[200] p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative">
      <!-- Close Button -->
      <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
        <Icon icon="mdi:close" class="w-6 h-6" />
      </button>

      <!-- Tabs -->
      <div class="flex border-b border-gray-200 mb-6">
        <button 
          @click="activeTab = 'login'" 
          :class="['flex-1 py-3 text-center font-medium transition-colors', 
                   activeTab === 'login' ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500']">
          Connexion
        </button>
        <button 
          @click="activeTab = 'register'" 
          :class="['flex-1 py-3 text-center font-medium transition-colors', 
                   activeTab === 'register' ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500']">
          Inscription
        </button>
      </div>

      <!-- Login Form -->
      <form v-if="activeTab === 'login'" @submit.prevent="handleLogin" class="space-y-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Bienvenue !</h2>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input 
            v-model="loginForm.email" 
            type="email" 
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="votre@email.com">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
          <input 
            v-model="loginForm.password" 
            type="password" 
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="••••••••">
        </div>

        <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors disabled:opacity-50">
          {{ loading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </form>

      <!-- Register Form -->
      <form v-if="activeTab === 'register'" @submit.prevent="handleRegister" class="space-y-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Créer un compte</h2>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
            <input 
              v-model="registerForm.first_name" 
              type="text" 
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              placeholder="Jean">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
            <input 
              v-model="registerForm.last_name" 
              type="text" 
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              placeholder="Dupont">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input 
            v-model="registerForm.email" 
            type="email" 
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="votre@email.com">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
          <input 
            v-model="registerForm.password" 
            type="password" 
            required
            minlength="8"
            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
            title="Au moins 8 caractères, une majuscule, une minuscule et un chiffre"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="••••••••">
          <p class="text-xs text-gray-500 mt-1">Min. 8 caractères avec majuscule, minuscule et chiffre</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone (optionnel)</label>
          <input 
            v-model="registerForm.phone" 
            type="tel"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="+33 6 12 34 56 78">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Adresse (optionnel)</label>
          <input 
            v-model="registerForm.address" 
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="123 Rue de Paris, 75001 Paris">
        </div>

        <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors disabled:opacity-50">
          {{ loading ? 'Inscription...' : 'S\'inscrire' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import { useAuthStore } from '@/stores/auth';
import Swal from 'sweetalert2';

const emit = defineEmits(['close', 'success']);

const authStore = useAuthStore();
const activeTab = ref('login');
const loading = ref(false);
const error = ref('');

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
      text: `Bienvenue ${authStore.currentUser.first_name || authStore.currentUser.email}`,
      timer: 2000,
      showConfirmButton: false
    });
    
    emit('success');
    emit('close');
  } catch (err) {
    error.value = err;
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
      title: 'Inscription réussie !',
      text: `Bienvenue ${authStore.currentUser.first_name}`,
      timer: 2000,
      showConfirmButton: false
    });
    
    emit('success');
    emit('close');
  } catch (err) {
    error.value = err;
  } finally {
    loading.value = false;
  }
};
</script>
