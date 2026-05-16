<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-6 max-w-4xl">
      <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">Politique de Cookies</h1>
        
        <div class="space-y-8">
          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Qu'est-ce qu'un cookie ?</h2>
            <p class="text-gray-600 leading-relaxed">
              Un cookie est un petit fichier texte stocké sur votre appareil (ordinateur, tablette, mobile) lorsque vous visitez notre site. 
              Les cookies nous permettent de reconnaître votre appareil et de mémoriser certaines informations concernant votre visite.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Types de cookies que nous utilisons</h2>
            
            <div class="space-y-6">
              <!-- Essential Cookies -->
              <div class="border-l-4 border-green-500 pl-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
                  <Icon icon="solar:shield-check-bold" class="w-6 h-6 text-green-600" />
                  Cookies Essentiels
                </h3>
                <p class="text-gray-600 mb-3">
                  Ces cookies sont indispensables au fonctionnement du site. Ils vous permettent de naviguer sur notre site 
                  et d'utiliser ses fonctionnalités essentielles.
                </p>
                <div class="bg-gray-50 rounded-xl p-4">
                  <h4 class="font-medium text-gray-700 mb-2">Exemples :</h4>
                  <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Panier d'achat</li>
                    <li>Authentification utilisateur</li>
                    <li>Sécurité du site</li>
                    <li>Préférences de base</li>
                  </ul>
                </div>
                <p class="text-sm text-green-600 mt-2 font-medium">
                  <Icon icon="solar:check-circle-bold" class="inline w-4 h-4 mr-1" />
                  Toujours actifs - Impossible de les désactiver
                </p>
              </div>

              <!-- Analytics Cookies -->
              <div class="border-l-4 border-blue-500 pl-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
                  <Icon icon="solar:chart-square-bold" class="w-6 h-6 text-blue-600" />
                  Cookies de Statistiques
                </h3>
                <p class="text-gray-600 mb-3">
                  Ces cookies nous permettent de mesurer l'audience et le trafic de notre site. Ils nous aident à comprendre 
                  comment les visiteurs interagissent avec nos pages pour améliorer l'expérience utilisateur.
                </p>
                <div class="bg-gray-50 rounded-xl p-4">
                  <h4 class="font-medium text-gray-700 mb-2">Services utilisés :</h4>
                  <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Google Analytics</li>
                    <li>Google Tag Manager</li>
                    <li>Hotjar (optionnel)</li>
                  </ul>
                </div>
                <p class="text-sm text-blue-600 mt-2">
                  <Icon icon="solar:info-circle-bold" class="inline w-4 h-4 mr-1" />
                  Activables sur demande - Durée de conservation : 14 mois
                </p>
              </div>

              <!-- Marketing Cookies -->
              <div class="border-l-4 border-orange-500 pl-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
                  <Icon icon="solar:target-bold" class="w-6 h-6 text-orange-600" />
                  Cookies Marketing
                </h3>
                <p class="text-gray-600 mb-3">
                  Ces cookies sont utilisés pour diffuser des publicités pertinentes pour vous et vos centres d'intérêt. 
                  Ils permettent également de mesurer l'efficacité de nos campagnes marketing.
                </p>
                <div class="bg-gray-50 rounded-xl p-4">
                  <h4 class="font-medium text-gray-700 mb-2">Services utilisés :</h4>
                  <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Meta Pixel (Facebook/Instagram)</li>
                    <li>TikTok Pixel</li>
                    <li>Google Ads</li>
                    <li>LinkedIn Pixel</li>
                  </ul>
                </div>
                <p class="text-sm text-orange-600 mt-2">
                  <Icon icon="solar:info-circle-bold" class="inline w-4 h-4 mr-1" />
                  Activables sur demande - Durée de conservation : variable selon le service
                </p>
              </div>

              <!-- Functional Cookies -->
              <div class="border-l-4 border-purple-500 pl-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
                  <Icon icon="solar:settings-bold" class="w-6 h-6 text-purple-600" />
                  Cookies Fonctionnels
                </h3>
                <p class="text-gray-600 mb-3">
                  Ces cookies améliorent l'expérience utilisateur en offrant des fonctionnalités personnalisées. 
                  Ils peuvent être définis par nous ou par des tiers dont nous avons intégré les services.
                </p>
                <div class="bg-gray-50 rounded-xl p-4">
                  <h4 class="font-medium text-gray-700 mb-2">Exemples :</h4>
                  <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Chat client en direct</li>
                    <li>Vidéos intégrées (YouTube, Vimeo)</li>
                    <li>Cartes interactives</li>
                    <li>Personnalisation du contenu</li>
                  </ul>
                </div>
                <p class="text-sm text-purple-600 mt-2">
                  <Icon icon="solar:info-circle-bold" class="inline w-4 h-4 mr-1" />
                  Activables sur demande - Durée de conservation : variable
                </p>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestion de vos préférences</h2>
            <div class="space-y-4">
              <div class="flex items-start gap-3">
                <Icon icon="solar:check-circle-bold" class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" />
                <div>
                  <h3 class="font-semibold text-gray-800">Consentement explicite</h3>
                  <p class="text-gray-600">Nous ne déposons que les cookies essentiels par défaut. Votre consentement est requis pour les autres catégories.</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <Icon icon="solar:check-circle-bold" class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" />
                <div>
                  <h3 class="font-semibold text-gray-800">Modification à tout moment</h3>
                  <p class="text-gray-600">Vous pouvez modifier vos préférences via le bouton "Gérer mes cookies" en bas à droite du site.</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <Icon icon="solar:check-circle-bold" class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" />
                <div>
                  <h3 class="font-semibold text-gray-800">Transparence totale</h3>
                  <p class="text-gray-600">Vous pouvez consulter l'historique de vos consentements et les détails des cookies utilisés.</p>
                </div>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Durée de conservation</h2>
            <div class="bg-blue-50 rounded-xl p-6">
              <table class="w-full text-left">
                <thead>
                  <tr class="border-b border-blue-200">
                    <th class="py-2 text-gray-700">Type de cookie</th>
                    <th class="py-2 text-gray-700">Durée maximale</th>
                    <th class="py-2 text-gray-700">Base légale</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-blue-100">
                    <td class="py-3">Essentiels</td>
                    <td class="py-3">Session à 1 an</td>
                    <td class="py-3">Exécution du contrat</td>
                  </tr>
                  <tr class="border-b border-blue-100">
                    <td class="py-3">Statistiques</td>
                    <td class="py-3">14 mois maximum</td>
                    <td class="py-3">Consentement</td>
                  </tr>
                  <tr class="border-b border-blue-100">
                    <td class="py-3">Marketing</td>
                    <td class="py-3">Variable (6 mois à 2 ans)</td>
                    <td class="py-3">Consentement</td>
                  </tr>
                  <tr>
                    <td class="py-3">Fonctionnels</td>
                    <td class="py-3">Variable</td>
                    <td class="py-3">Consentement</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Vos droits</h2>
            <div class="grid md:grid-cols-2 gap-4">
              <div class="p-4 bg-gray-50 rounded-xl">
                <h3 class="font-semibold text-gray-800 mb-2">Droit d'information</h3>
                <p class="text-sm text-gray-600">Accéder aux informations sur les cookies que nous utilisons</p>
              </div>
              <div class="p-4 bg-gray-50 rounded-xl">
                <h3 class="font-semibold text-gray-800 mb-2">Droit de consentement</h3>
                <p class="text-sm text-gray-600">Accepter ou refuser les cookies non essentiels</p>
              </div>
              <div class="p-4 bg-gray-50 rounded-xl">
                <h3 class="font-semibold text-gray-800 mb-2">Droit de retrait</h3>
                <p class="text-sm text-gray-600">Retirer votre consentement à tout moment</p>
              </div>
              <div class="p-4 bg-gray-50 rounded-xl">
                <h3 class="font-semibold text-gray-800 mb-2">Droit de réclamation</h3>
                <p class="text-sm text-gray-600">Saisir l'autorité de protection des données</p>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Paramètres de votre navigateur</h2>
            <p class="text-gray-600 mb-4">
              Vous pouvez configurer votre navigateur pour refuser tous les cookies ou pour vous avertir lorsqu'un cookie est déposé. 
              Cependant, désactiver certains cookies peut affecter certaines fonctionnalités de notre site.
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
              <p class="text-sm text-yellow-800">
                <Icon icon="solar:info-circle-bold" class="inline w-4 h-4 mr-1" />
                <strong>Note :</strong> La désactivation des cookies essentiels peut rendre notre site inutilisable.
              </p>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Contact et mise à jour</h2>
            <p class="text-gray-600 mb-4">
              Cette politique de cookies est mise à jour régulièrement pour refléter les évolutions réglementaires 
              et les changements dans notre utilisation des cookies.
            </p>
            <div class="bg-purple-50 rounded-xl p-6">
              <h3 class="font-semibold text-purple-800 mb-2">Pour toute question :</h3>
              <p class="text-purple-700">Email : egouassangni@mail.com</p>
              <p class="text-purple-700">Téléphone : +229 01 56 78 37 70</p>
              <p class="text-sm text-purple-600 mt-2">
                Dernière mise à jour : {{ new Date().toLocaleDateString('fr-BJ') }}
              </p>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

// SEO
document.title = 'Politique de Cookies - Bloom by Chloe'
</script>
