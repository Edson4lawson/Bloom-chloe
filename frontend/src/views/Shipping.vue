<template>
  <div class="min-h-screen bg-slate-50 pt-32 pb-20">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl">
      <!-- Header Section -->
      <div class="text-center mb-16" data-aos="fade-up">
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Livraison & Expédition</h1>
        <p class="text-slate-500 font-medium max-w-xl mx-auto">Nous livrons vos produits préférés partout au Bénin avec le plus grand soin.</p>
        <div class="w-20 h-1.5 bg-blue-600 mx-auto mt-8 rounded-full"></div>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 md:p-16 border border-slate-100" data-aos="fade-up" data-aos-delay="100">
        <div class="prose prose-slate prose-lg max-w-none space-y-12">
          
          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-4">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                <Icon icon="solar:map-point-bold" />
              </div>
              Zones & Délais
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="p-8 bg-slate-50 rounded-3xl border border-slate-100 group hover:border-blue-200 transition-colors">
                <h4 class="text-blue-900 font-bold mb-2">Cotonou & Calavi</h4>
                <p class="text-slate-500 text-sm mb-4">Livraison express à domicile ou au bureau.</p>
                <div class="flex items-center gap-2 text-blue-600 font-black">
                  <Icon icon="solar:clock-circle-bold" />
                  24h - 48h
                </div>
              </div>
              <div class="p-8 bg-slate-50 rounded-3xl border border-slate-100 group hover:border-blue-200 transition-colors">
                <h4 class="text-blue-900 font-bold mb-2">Autres Villes</h4>
                <p class="text-slate-500 text-sm mb-4">Expédition sécurisée via nos partenaires logistiques.</p>
                <div class="flex items-center gap-2 text-blue-600 font-black">
                  <Icon icon="solar:delivery-bold" />
                  2 - 5 Jours
                </div>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-4">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                <Icon icon="solar:wad-of-money-bold" />
              </div>
              Tarifs de Livraison
            </h2>
            <div class="overflow-hidden border border-slate-100 rounded-3xl">
              <table class="w-full text-left">
                <thead class="bg-slate-50">
                  <tr>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Localité</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Frais</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-slate-700 font-bold">Cotonou & Abomey-Calavi</td>
                    <td class="px-6 py-4 text-blue-600 font-black">1.000 FCFA</td>
                  </tr>
                  <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-slate-700 font-bold">Porto-Novo, Parakou, Bohicon</td>
                    <td class="px-6 py-4 text-blue-600 font-black">1.500 FCFA</td>
                  </tr>
                  <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-slate-700 font-bold">Autres Localités</td>
                    <td class="px-6 py-4 text-blue-600 font-black">2.000 FCFA</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p class="mt-4 text-sm text-slate-400 italic text-center">Livraison offerte pour toute commande supérieure à 50.000 FCFA.</p>
          </section>

          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-4">
              <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                <Icon icon="solar:box-bold" />
              </div>
              Processus d'Expédition
            </h2>
            <div class="space-y-6">
              <div class="flex gap-6 items-start group">
                <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center flex-shrink-0 font-bold transition-transform group-hover:scale-110">1</div>
                <div>
                  <h4 class="text-slate-900 font-bold mb-1">Validation</h4>
                  <p class="text-slate-500 text-base">Dès confirmation de votre paiement (MTN, Celtis ou Virement UBA).</p>
                </div>
              </div>
              <div class="flex gap-6 items-start group">
                <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center flex-shrink-0 font-bold transition-transform group-hover:scale-110">2</div>
                <div>
                  <h4 class="text-slate-900 font-bold mb-1">Préparation</h4>
                  <p class="text-slate-500 text-base">Nettoyage et emballage soigné de vos produits dans nos cartons signature.</p>
                </div>
              </div>
              <div class="flex gap-6 items-start group">
                <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center flex-shrink-0 font-bold transition-transform group-hover:scale-110">3</div>
                <div>
                  <h4 class="text-slate-900 font-bold mb-1">Prise en charge</h4>
                  <p class="text-slate-500 text-base">Remise de votre colis au livreur ou transporteur partenaire.</p>
                </div>
              </div>
            </div>
          </section>

          <div class="bg-amber-50 border border-amber-100 p-8 rounded-3xl flex gap-6 items-start">
            <Icon icon="solar:info-circle-bold" class="w-10 h-10 text-amber-600 flex-shrink-0" />
            <div>
              <h4 class="text-amber-900 font-bold mb-2">Conseil important</h4>
              <p class="text-amber-800 text-sm leading-relaxed opacity-80">
                Veuillez vérifier l'intégrité de votre colis en présence du livreur. Toute réclamation concernant un produit cassé ou manquant doit être faite immédiatement au moment de la réception.
              </p>
            </div>
          </div>

          <div class="flex justify-center pt-8">
            <router-link to="/" class="px-8 py-4 bg-slate-900 text-sm font-black text-white uppercase tracking-widest rounded-2xl hover:bg-blue-600 transition-all active:scale-95 shadow-lg shadow-slate-200">
              Continuer mes achats
            </router-link>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
</script>
