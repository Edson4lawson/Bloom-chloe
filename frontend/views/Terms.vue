<template>
  <div class="min-h-screen bg-slate-50 pt-32 pb-20">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl">
      <!-- Header Section -->
      <div class="text-center mb-16" data-aos="fade-up">
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Conditions Générales de Vente</h1>
        <p class="text-slate-500 font-medium">Contrat de vente et conditions d'utilisation</p>
        <div class="w-20 h-1.5 bg-indigo-600 mx-auto mt-8 rounded-full"></div>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 md:p-16 border border-slate-100" data-aos="fade-up" data-aos-delay="100">
        <div class="prose prose-slate prose-lg max-w-none space-y-12">
          
          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center">
              <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-4 text-lg">1</span>
              Objet du Contrat
            </h2>
            <p class="text-slate-600 leading-relaxed">
              Les présentes conditions générales de vente (CGV) régissent l'ensemble des relations contractuelles entre la boutique <strong>Bloom by Chloé</strong> et ses clients dans le cadre de la vente de produits de beauté, bien-être et accessoires lifestyle. Toute commande sur le site implique l'adhésion sans réserve à ces conditions.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center">
              <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-4 text-lg">2</span>
              Commandes et Prix
            </h2>
            <div class="space-y-4">
              <p class="text-slate-600">
                Toutes les commandes sont fermes et définitives dès la validation du paiement. Les prix sont affichés en <strong>Francs CFA (FCFA)</strong> et incluent toutes les taxes applicables au Bénin.
              </p>
              <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-3xl">
                <h4 class="text-indigo-900 font-bold mb-2">Note sur la disponibilité :</h4>
                <p class="text-indigo-800 text-sm opacity-80">
                  En cas d'indisponibilité exceptionnelle d'un produit après commande, nous nous engageons à vous en informer sous 24h et à vous proposer une solution de remplacement ou un remboursement immédiat.
                </p>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center">
              <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-4 text-lg">3</span>
              Paiement Sécurisé
            </h2>
            <p class="text-slate-600 mb-6">Nous acceptons les modes de paiement suivants, garantissant une sécurité maximale :</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <Icon icon="logos:mtn" class="w-10 h-6 mx-auto mb-3" />
                <p class="text-xs font-bold text-slate-800 uppercase tracking-widest">MTN MOMO</p>
              </div>
              <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <Icon icon="simple-icons:moov" class="w-10 h-10 mx-auto mb-3 text-blue-600" />
                <p class="text-xs font-bold text-slate-800 uppercase tracking-widest">MOOV FLOAT</p>
              </div>
              <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <Icon icon="solar:card-bold" class="w-10 h-10 mx-auto mb-3 text-indigo-600" />
                <p class="text-xs font-bold text-slate-800 uppercase tracking-widest">CELTIS CASH</p>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center">
              <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-4 text-lg">4</span>
              Livraison et Délais
            </h2>
            <div class="grid md:grid-cols-2 gap-8">
              <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                <h4 class="font-bold text-slate-900 mb-4 flex items-center">
                  <Icon icon="solar:delivery-bold" class="text-indigo-600 mr-2" />
                  Cotonou & Calavi
                </h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                  Livraison sous 24h à 48h ouvrés. Frais fixes de livraison applicables selon la zone précise.
                </p>
              </div>
              <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                <h4 class="font-bold text-slate-900 mb-4 flex items-center">
                  <Icon icon="solar:global-bold" class="text-indigo-600 mr-2" />
                  Autres Villes (Bénin)
                </h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                  Expédition via services de transport partenaires sous 2 à 5 jours ouvrés.
                </p>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center">
              <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-4 text-lg">5</span>
              Garantie et Retours
            </h2>
            <p class="text-slate-600 leading-relaxed">
              Conformément à notre charte qualité, vous disposez d'un délai de 48h après réception pour nous signaler tout défaut apparent. Les retours ne sont acceptés que pour les produits non ouverts et dans leur emballage d'origine. Les produits d'hygiène et cosmétiques entamés ne sont ni repris ni échangés pour des raisons de santé publique.
            </p>
          </section>

          <div class="pt-8 border-t border-slate-100 italic text-slate-400 text-sm text-center">
            En validant votre panier, vous reconnaissez avoir pris connaissance de ces termes et les accepter sans réserve.
          </div>

          <div class="flex justify-center pt-8">
            <router-link to="/" class="group flex items-center gap-2 px-8 py-4 bg-slate-900 text-sm font-black text-white uppercase tracking-widest rounded-2xl hover:bg-indigo-600 transition-all active:scale-95 shadow-lg shadow-slate-200">
              <Icon icon="solar:arrow-left-linear" class="group-hover:-translate-x-1 transition-transform" />
              Retour Boutique
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
