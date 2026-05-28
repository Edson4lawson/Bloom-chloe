<template>
  <div class="min-h-screen bg-slate-50 pt-32 pb-20">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl">
      <!-- Header Section -->
      <div class="text-center mb-16" data-aos="fade-up">
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Retours & Échanges</h1>
        <p class="text-slate-500 font-medium max-w-xl mx-auto">Votre satisfaction est notre priorité. Découvrez notre politique de retour simplifiée.</p>
        <div class="w-20 h-1.5 bg-rose-500 mx-auto mt-8 rounded-full"></div>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 md:p-16 border border-slate-100" data-aos="fade-up" data-aos-delay="100">
        <div class="prose prose-slate prose-lg max-w-none space-y-12">
          
          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-4">
              <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center text-lg">
                <Icon icon="solar:reorder-bold" />
              </div>
              Droit de Retour
            </h2>
            <p class="text-slate-600 leading-relaxed">
              Vous avez changé d'avis ? Vous disposez d'un délai de <strong>48 heures</strong> à compter de la réception de votre commande pour nous signaler votre souhait de retourner un produit. 
            </p>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mb-4">
                  <Icon icon="solar:check-circle-bold" />
                </div>
                <h4 class="font-bold text-slate-900 mb-2">Conditions acceptées</h4>
                <ul class="text-sm text-slate-500 space-y-2 list-none p-0">
                  <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-green-500"></div> Emballage d'origine scellé</li>
                  <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-green-500"></div> Produit non utilisé</li>
                  <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-green-500"></div> Erreur de préparation</li>
                </ul>
              </div>
              <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                <div class="w-8 h-8 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center mb-4">
                  <Icon icon="solar:close-circle-bold" />
                </div>
                <h4 class="font-bold text-slate-900 mb-2">Conditions refusées</h4>
                <ul class="text-sm text-slate-500 space-y-2 list-none p-0">
                  <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div> Cosmétique ouvert</li>
                  <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div> Produit testé/usagé</li>
                  <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div> Délai de 48h dépassé</li>
                </ul>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-4">
              <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center text-lg">
                <Icon icon="solar:transfer-horizontal-bold" />
              </div>
              Procédure d'Échange
            </h2>
            <div class="space-y-8 relative before:absolute before:left-5 before:top-2 before:bottom-2 before:w-px before:bg-slate-100">
              <div class="relative pl-12">
                <div class="absolute left-0 top-0 w-10 h-10 bg-white border-2 border-slate-100 rounded-full flex items-center justify-center font-black text-slate-400">1</div>
                <h4 class="text-slate-900 font-bold mb-1">Signalement</h4>
                <p class="text-slate-500 text-base">Contactez-nous sur WhatsApp (+229 01 56 78 37 70) avec votre numéro de commande et une photo du produit.</p>
              </div>
              <div class="relative pl-12">
                <div class="absolute left-0 top-0 w-10 h-10 bg-white border-2 border-slate-100 rounded-full flex items-center justify-center font-black text-slate-400">2</div>
                <h4 class="text-slate-900 font-bold mb-1">Vérification</h4>
                <p class="text-slate-500 text-base">Notre équipe valide votre demande sous 4h ouvrées.</p>
              </div>
              <div class="relative pl-12">
                <div class="absolute left-0 top-0 w-10 h-10 bg-white border-2 border-slate-100 rounded-full flex items-center justify-center font-black text-slate-400">3</div>
                <h4 class="text-slate-900 font-bold mb-1">Retour Colis</h4>
                <p class="text-slate-500 text-base">Vous déposez le colis à notre point relais partenaire ou un livreur passe le récupérer (frais à votre charge sauf erreur de notre part).</p>
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-4">
              <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center text-lg">
                <Icon icon="solar:bill-list-bold" />
              </div>
              Remboursements
            </h2>
            <p class="text-slate-600 leading-relaxed">
              Une fois le produit inspecté et validé par notre équipe, le remboursement est effectué sous <strong>24h</strong> via le même mode de paiement utilisé lors de la commande (MTN Mobile Money, Celtis Cash ou Virement UBA).
            </p>
            <div class="bg-slate-900 text-white p-8 rounded-3xl mt-8">
              <p class="font-medium opacity-80 italic">"Nous nous engageons à traiter chaque demande avec bienveillance et équité pour garantir votre entière satisfaction."</p>
              <p class="mt-4 font-black text-rose-400">— L'équipe Bloom by Chloé</p>
            </div>
          </section>

          <div class="flex justify-center pt-8">
            <router-link to="/" class="px-8 py-4 bg-slate-100 text-sm font-black text-slate-900 uppercase tracking-widest rounded-2xl hover:bg-rose-500 hover:text-white transition-all active:scale-95">
              Retour à l'accueil
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
