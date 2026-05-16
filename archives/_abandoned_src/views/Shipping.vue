<template>
  <div class="min-h-screen bg-gray-50 py-24">
    <div class="container mx-auto px-6 max-w-4xl">
    
      <div class="bg-white rounded-3xl shadow-xl p-8 mt-20 md:p-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">Livraison</h1>
        
        <div class="space-y-8">
          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Zones de Livraison</h2>
            <div class="grid md:grid-cols-2 gap-6">
              <div class="p-6 bg-purple-50 rounded-2xl">
                <h3 class="font-semibold text-purple-800 mb-2">Cotonou & Abomey-Calavi</h3>
                <p class="text-gray-600">Livraison sous 24-48h</p>
                <!-- <p class="text-sm text-gray-500 mt-2">Gratuite dès 15.000 FCFA</p> -->
              </div>
              <div class="p-6 bg-blue-50 rounded-2xl">
                <h3 class="font-semibold text-blue-800 mb-2">Autres villes du Bénin</h3>
                <p class="text-gray-600">Livraison sous 2-5 jours</p>
                <!-- <p class="text-sm text-gray-500 mt-2">Gratuite dès 25.000 FCFA</p> -->
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Tarifs de Livraison</h2>
            <div class="bg-gray-50 rounded-2xl p-6">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-gray-200">
                    <th class="text-left py-3 text-gray-700">Zone</th>
                    <th class="text-left py-3 text-gray-700">Délai</th>
                    <th class="text-left py-3 text-gray-700">Frais</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-gray-200">
                    <td class="py-3">Cotonou & Abomey-Calavi</td>
                    <td class="py-3">24-48h</td>
                    <td class="py-3">1.000 FCFA</td>
                  </tr>
                  <tr class="border-b border-gray-200">
                    <td class="py-3">Grandes villes (Parakou, Porto-Novo, etc.)</td>
                    <td class="py-3">2-3 jours</td>
                    <td class="py-3">1.500 FCFA</td>
                  </tr>
                  <tr>
                    <td class="py-3">Autres localités</td>
                    <td class="py-3">3-5 jours</td>
                    <td class="py-3">2.000 FCFA</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Processus de Livraison</h2>
            <div class="space-y-4">
              <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center flex-shrink-0">1</div>
                <div>
                  <h3 class="font-semibold text-gray-800">Confirmation de commande</h3>
                  <p class="text-gray-600">Vous recevez un message de confirmation avec les détails de votre commande.</p>
                </div>
              </div>
              <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center flex-shrink-0">2</div>
                <div>
                  <h3 class="font-semibold text-gray-800">Préparation</h3>
                  <p class="text-gray-600">Votre commande est préparée et emballée avec soin (1-2h).</p>
                </div>
              </div>
              <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center flex-shrink-0">3</div>
                <div>
                  <h3 class="font-semibold text-gray-800">Expédition</h3>
                  <p class="text-gray-600">Votre colis est confié à notre service de livraison.</p>
                </div>
              </div>
              <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center flex-shrink-0">4</div>
                <div>
                  <h3 class="font-semibold text-gray-800">Livraison</h3>
                  <p class="text-gray-600">Vous recevez votre commande à l'adresse indiquée.</p>
                </div>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Suivi de Commande</h2>
            <p class="text-gray-600 mb-4">Suivez votre commande en temps réel :</p>
            <ul class="list-disc list-inside space-y-2 text-gray-600">
              <li>Email de suivi avec numéro de tracking</li>
              <li>Mises à jour SMS à chaque étape clé</li>
              <li>Accès depuis votre compte client</li>
              <li>Service client disponible pour toute question</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Important</h2>
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
              <ul class="space-y-2 text-gray-700">
                <li>• Livraison sur le territoire béninois et partout dans le monde</li>
                <li>• Présentation d'une pièce d'identité requise à la réception</li>
                <li>• Vérification du colis avant signature obligatoire</li>
                <li>• En cas d'absence, un deuxième passage sera organisé mais a vos frais</li>
              </ul>
            </div>
          </section>
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
import { Icon } from '@iconify/vue'
// Pas de useHead pour éviter les erreurs
</script>
