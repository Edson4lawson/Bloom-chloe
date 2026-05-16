<template>
  <div class="min-h-screen bg-gray-50 py-24">
    <div class="container mx-auto px-6 max-w-4xl">
      
      <div class="bg-white rounded-3xl shadow-xl p-8 mt-20 md:p-20">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">Questions Fréquemment Posées</h1>
        
        <div class="space-y-6">
          <div v-for="(faq, index) in faqs" :key="index" class="border border-gray-200 rounded-2xl overflow-hidden">
            <button 
              @click="toggleFAQ(index)"
              class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition-colors"
            >
              <span class="font-semibold text-gray-800">{{ faq.question }}</span>
              <Icon 
                :icon="expandedFAQ === index ? 'solar:minus-circle-bold' : 'solar:add-circle-bold'" 
                class="w-6 h-6 text-purple-600"
              />
            </button>
            <div 
              v-show="expandedFAQ === index"
              class="px-6 py-4 bg-gray-50 border-t border-gray-200"
            >
              <p class="text-gray-600">{{ faq.answer }}</p>
            </div>
          </div>
        </div>

        <div class="mt-12 p-6 bg-purple-50 rounded-2xl">
          <h3 class="text-xl font-semibold text-gray-800 mb-3">Vous ne trouvez pas votre réponse ?</h3>
          <p class="text-gray-600 mb-4">Notre service client est à votre disposition pour vous aider.</p>
          <button class="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition-colors">
            Contacter le support
          </button>
        </div>
      </div>
      <!-- Bouton de retour vers l'accueil -->
      <div class="mt-8">
        <router-link 
          :to="{ path: '/', hash: '#footer' }" 
          class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition-all transform hover:scale-105 active:scale-95 shadow-lg"
        >
          <Icon icon="solar:arrow-left-bold" class="w-5 h-5" />
          Retour à l'accueil
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'

const expandedFAQ = ref(null)

const faqs = ref([
  {
    question: "Quels sont les délais de livraison ?",
    answer: "Les livraisons à Cotonou et Abomey-Calavi sont effectuées sous 24-48h. Pour les autres villes du Bénin, comptez 2-5 jours ouvrables."
  },
  {
    question: "Comment puis-je payer ma commande ?",
    answer: "Nous acceptons Mobile Money (MTN), Celtis Cash et les virements bancaires UBA. Le paiement est sécurisé et instantané."
  },
  {
    question: "Puis-je retourner un produit ?",
    answer: "Oui, vous avez 03 jours pour retourner un produit non utilisé et dans son emballage d'origine. Nous vous rembourserons intégralement."
  },
  {
    question: "Les produits sont-ils authentiques ?",
    answer: "Absolument ! Tous nos produits sont 100% authentiques et proviennent directement des fabricants ou distributeurs agréés."
  },
  {
    question: "Comment savoir si un produit est en stock ?",
    answer: "La disponibilité est indiquée sur chaque page produit. Si un produit est épuisé, vous pouvez vous inscrire pour être notifié dès son retour."
  },
  {
    question: "Proposez-vous des cadeaux d'emballage ?",
    answer: "Oui, nous proposons un service d'emballage cadeau gratuit pour les commandes supérieures à 10.000 FCFA."
  },
  {
    question: "Comment suivre ma commande ?",
    answer: "Vous recevrez un email avec un numéro de suivi dès l'expédition. Vous pouvez également suivre votre commande depuis votre compte."
  },
  {
    question: "Y a-t-il des frais de livraison ?",
    answer: "La livraison est gratuite à partir de 50.000 FCFA d'achat. En dessous de ce montant, les frais sont de 1.500 FCFA."
  }
])

const toggleFAQ = (index) => {
  expandedFAQ.value = expandedFAQ.value === index ? null : index
}

// Pas de useHead pour éviter les erreurs
</script>
