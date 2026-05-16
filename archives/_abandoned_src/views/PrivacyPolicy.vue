<template>
  <div class="min-h-screen bg-gray-50 py-24">
    <div class="container mx-auto px-6 max-w-4xl">
      
      <div class="bg-white rounded-3xl shadow-xl p-8 mt-20 md:p-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">Politique de Confidentialité</h1>
        
        <div class="space-y-8 text-gray-600">
          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Introduction</h2>
            <p>Bloom by Chloe s'engage à protéger la confidentialité de vos données personnelles. Cette politique explique comment nous collectons, utilisons et protégeons vos informations.</p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Données Collectées</h2>
            <ul class="list-disc list-inside space-y-2">
              <li>Nom, prénom et adresse email</li>
              <li>Adresse de livraison et de facturation</li>
              <li>Numéro de téléphone</li>
              <li>Historique des commandes et préférences</li>
              <li>Données de navigation et cookies</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Utilisation des Données</h2>
            <p>Vos données sont utilisées pour :</p>
            <ul class="list-disc list-inside space-y-2">
              <li>Traiter vos commandes et livraisons</li>
              <li>Améliorer nos services et produits</li>
              <li>Vous contacter concernant votre commande</li>
              <li>Vous envoyer des offres promotionnelles (avec votre consentement)</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Protection des Données</h2>
            <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles pour protéger vos données contre l'accès non autorisé, la modification ou la destruction.</p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Vos Droits</h2>
            <p>Vous avez le droit de :</p>
            <ul class="list-disc list-inside space-y-2">
              <li>Accéder à vos données personnelles</li>
              <li>Rectifier vos informations</li>
              <li>Supprimer votre compte et vos données</li>
              <li>Vous opposer au traitement de vos données</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Contact</h2>
            <p>Pour toute question concernant cette politique, contactez-nous à :</p>
            <p class="text-purple-600">egouassangni@mail.com</p>
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
