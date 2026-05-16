<template>
  <Transition
    enter-active-class="transform ease-out duration-500 transition-all"
    enter-from-class="translate-y-full opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transform ease-in duration-300 transition-all"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-full opacity-0"
  >
    <div v-if="isOpen" class="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6">
      <div class="container mx-auto max-w-5xl">
        <div class="bg-white/90 backdrop-blur-md border border-purple-100 rounded-3xl shadow-2xl p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 md:gap-8">
          
          <!-- Icon -->
          <div class="hidden md:flex items-center justify-center w-16 h-16 bg-purple-50 rounded-2xl flex-shrink-0">
            <Icon icon="solar:cookie-bold-duotone" class="w-8 h-8 text-purple-600" />
          </div>

          <!-- Content -->
          <div class="flex-1 text-center md:text-left">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Nous utilisons des cookies 🍪</h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Nous utilisons des cookies pour améliorer votre expérience sur Bloom by Chloe. 
              En continuant, vous acceptez notre 
              <router-link to="/privacy" class="text-purple-600 font-medium hover:underline">politique de confidentialité</router-link>.
            </p>
          </div>

          <!-- Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto min-w-[300px]">
            <button 
              @click="decline"
              class="px-6 py-3 border border-gray-200 text-gray-600 font-medium rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200"
            >
              Refuser
            </button>
            <button 
              @click="accept"
              class="flex-1 px-6 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-all transform hover:scale-105 active:scale-95 shadow-lg shadow-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
            >
              Accepter tout
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Icon } from '@iconify/vue'

const isOpen = ref(false)

const checkConsent = () => {
  const consent = localStorage.getItem('cookie_consent')
  if (!consent) {
    // Petit délai pour l'animation d'entrée
    setTimeout(() => {
      isOpen.value = true
    }, 1000)
  }
}

const accept = () => {
  localStorage.setItem('cookie_consent', 'accepted')
  isOpen.value = false
}

const decline = () => {
  localStorage.setItem('cookie_consent', 'declined')
  isOpen.value = false
}

onMounted(() => {
  checkConsent()
})
</script>
