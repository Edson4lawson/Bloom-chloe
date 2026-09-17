<template>
  <div v-if="isOpen" class="fixed inset-0 z-[200] flex items-start justify-center bg-black/50 backdrop-blur-sm pt-32 md:pt-40 overflow-y-auto">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-gray-800">Choisir le paiement</h3>
        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
          <Icon icon="solar:close-circle-bold" class="w-6 h-6" />
        </button>
      </div>

      <div class="space-y-4">
        <!-- Paiement en liquidité (Cash on Delivery) -->
        <div 
          @click="selectPayment('cash_on_delivery')"
          class="border-2 border-gray-200 rounded-2xl p-4 cursor-pointer transition-all hover:border-amber-500 hover:bg-amber-50"
          :class="{ 'border-amber-500 bg-amber-50': selectedProvider === 'cash_on_delivery' }"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                <Icon icon="solar:wallet-money-bold" class="w-6 h-6 text-amber-600" />
              </div>
              <div>
                <h4 class="font-semibold text-gray-800">Paiement en liquidité</h4>
                <p class="text-sm text-gray-500">Payer à la livraison</p>
              </div>
            </div>
            <div class="w-6 h-6 rounded-full border-2 border-gray-300" 
                 :class="{ 'bg-amber-500 border-amber-500': selectedProvider === 'cash_on_delivery' }">
              <div v-if="selectedProvider === 'cash_on_delivery'" class="w-full h-full flex items-center justify-center">
                <Icon icon="mdi:check" class="w-4 h-4 text-white" />
              </div>
            </div>
          </div>
        </div>

        <!-- Paiement par transfert -->
        <div 
          @click="selectPayment('transfer')"
          class="border-2 border-gray-200 rounded-2xl p-4 cursor-pointer transition-all hover:border-purple-500 hover:bg-purple-50"
          :class="{ 'border-purple-500 bg-purple-50': selectedProvider === 'transfer' }"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <Icon icon="solar:transfer-horizontal-bold" class="w-6 h-6 text-purple-600" />
              </div>
              <div>
                <h4 class="font-semibold text-gray-800">Transfert mobile / bancaire</h4>
                <p class="text-sm text-gray-500">MTN, Celtis ou UBA</p>
              </div>
            </div>
            <div class="w-6 h-6 rounded-full border-2 border-gray-300" 
                 :class="{ 'bg-purple-500 border-purple-500': selectedProvider === 'transfer' }">
              <div v-if="selectedProvider === 'transfer'" class="w-full h-full flex items-center justify-center">
                <Icon icon="mdi:check" class="w-4 h-4 text-white" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Info Cash on Delivery -->
      <div v-if="selectedProvider === 'cash_on_delivery'" class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
        <div class="flex items-start space-x-3">
          <Icon icon="solar:info-circle-bold" class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" />
          <div>
            <p class="text-sm font-medium text-amber-800">Paiement à la livraison</p>
            <p class="text-sm text-amber-700 mt-1">
              Vous paierez le montant de <strong>{{ amount?.toLocaleString('fr-FR') }} Fcfa</strong> en espèces au livreur lors de la réception de votre commande.
            </p>
          </div>
        </div>
      </div>

      <!-- Info Transfert avec numéros -->
      <div v-if="selectedProvider === 'transfer'" class="mt-6 space-y-3">
        <div class="p-4 bg-purple-50 border border-purple-200 rounded-2xl">
          <div class="flex items-start space-x-3">
            <Icon icon="solar:info-circle-bold" class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0" />
            <div>
              <p class="text-sm font-medium text-purple-800">Instructions de transfert</p>
              <p class="text-sm text-purple-700 mt-1">
                Envoyez <strong>{{ amount?.toLocaleString('fr-FR') }} Fcfa</strong> via l'un des réseaux ci-dessous, puis confirmez la commande.
              </p>
            </div>
          </div>
        </div>

        <!-- MTN Mobile Money -->
        <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48" class="text-white">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M27.514 22.84a4.973 4.973 0 0 1-4.977 4.97h0a4.973 4.973 0 0 1-4.977-4.97h0a4.973 4.973 0 0 1 4.977-4.97h0a4.973 4.973 0 0 1 4.977 4.97M31.153 4.5l-4.28 6.593c5.98 4.98 9.916 12.094 11.657 19.62c.41-6.817-.245-13.918-3.433-20.07C34 8.466 32.66 6.416 31.153 4.5M20.42 10.735L9.36 27.509c8.41 1.282 16.409 5.09 22.63 10.893c1.74 1.597 3.38 3.3 4.91 5.098c.734-9.014-1.584-18.514-7.56-25.47c-2.493-2.95-5.547-5.414-8.92-7.295" stroke-width="1"/>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">MTN MoMo</p>
              <p class="text-xs text-gray-500">Mobile Money</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <span class="font-mono font-bold text-gray-800 text-sm">+229 96 XX XX XX</span>
            <button @click.stop="copyNumber('+22996XXXXXX')" class="p-1.5 hover:bg-yellow-100 rounded-lg transition-colors">
              <Icon icon="solar:copy-bold" class="w-4 h-4 text-gray-500" />
            </button>
          </div>
        </div>

        <!-- Celtis Cash -->
        <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-xl">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
              <Icon icon="mdi:cash" class="w-5 h-5 text-white" />
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">Celtis Cash</p>
              <p class="text-xs text-gray-500">Moov Money</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <span class="font-mono font-bold text-gray-800 text-sm">+229 97 XX XX XX</span>
            <button @click.stop="copyNumber('+22997XXXXXX')" class="p-1.5 hover:bg-green-100 rounded-lg transition-colors">
              <Icon icon="solar:copy-bold" class="w-4 h-4 text-gray-500" />
            </button>
          </div>
        </div>

        <!-- UBA Bank -->
        <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-xl">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
              <Icon icon="mdi:bank" class="w-5 h-5 text-white" />
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">UBA Bank</p>
              <p class="text-xs text-gray-500">Virement bancaire</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <span class="font-mono font-bold text-gray-800 text-sm">XXXX XXXX XXXX</span>
            <button @click.stop="copyNumber('XXXXXXXXXXXX')" class="p-1.5 hover:bg-blue-100 rounded-lg transition-colors">
              <Icon icon="solar:copy-bold" class="w-4 h-4 text-gray-500" />
            </button>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex space-x-4 mt-8">
        <button 
          @click="closeModal"
          class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors"
        >
          Annuler
        </button>
        <button 
          @click="processPayment"
          :disabled="!selectedProvider || isProcessing"
          class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="selectedProvider === 'cash_on_delivery'">Confirmer</span>
          <span v-else-if="selectedProvider === 'transfer'">J'ai effectué le transfert</span>
          <span v-else>Choisir un mode</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, toRef } from 'vue'
import { Icon } from '@iconify/vue'
import { paymentService } from '@/services/api'
import { useScrollLock } from '@/composables/useScrollLock'
import Swal from 'sweetalert2'

const props = defineProps({
  isOpen: Boolean,
  amount: Number,
  orderId: Number
})

// Verrouillage du scroll en arrière-plan lorsque la modal de paiement est ouverte
useScrollLock(toRef(props, 'isOpen'))

const emit = defineEmits(['close', 'success'])

const selectedProvider = ref('')
const isProcessing = ref(false)

const closeModal = () => {
  selectedProvider.value = ''
  emit('close')
}

const selectPayment = (provider) => {
  selectedProvider.value = provider
}

const copyNumber = (number) => {
  navigator.clipboard.writeText(number).then(() => {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Numéro copié !',
      showConfirmButton: false,
      timer: 1500
    })
  }).catch(() => {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'info',
      title: number,
      showConfirmButton: false,
      timer: 3000
    })
  })
}

const processPayment = async () => {
  if (!selectedProvider.value || isProcessing.value) return
  isProcessing.value = true

  try {
    if (selectedProvider.value === 'cash_on_delivery') {
      await paymentService.process({
        amount: props.amount,
        provider: 'cash_on_delivery',
        order_id: props.orderId
      })

      Swal.fire({
        title: 'Commande confirmée ! 🎉',
        html: `
          <div class="text-center space-y-3 mt-4">
            <p class="text-slate-800 font-bold text-base">Votre commande est enregistrée avec succès.</p>
            <div class="p-3 bg-purple-50/80 rounded-2xl border border-purple-100/80 inline-block">
              <span class="text-xs text-purple-700 font-bold uppercase tracking-wider">Montant à régler à la livraison</span>
              <p class="text-xl font-black text-purple-900 mt-0.5">${Number(props.amount).toLocaleString('fr-FR')} FCFA</p>
            </div>
            <p class="text-xs text-slate-500 font-medium">📦 Notre équipe vous contactera sous peu pour organiser votre livraison.</p>
          </div>
        `,
        icon: 'success',
        confirmButtonText: 'Continuer mes achats',
        buttonsStyling: true
      })
      emit('success', { provider: 'cash_on_delivery', status: 'pending_delivery' })
      closeModal()
    } else if (selectedProvider.value === 'transfer') {
      const result = await Swal.fire({
        title: 'Confirmer votre transfert',
        html: `
          <div class="text-center space-y-3 mt-4">
            <p class="text-slate-700 text-sm">Avez-vous bien effectué le transfert de <strong class="text-purple-700 font-black">${Number(props.amount).toLocaleString('fr-FR')} FCFA</strong> ?</p>
            <p class="text-xs text-slate-400">Notre équipe procédera à la vérification dès réception.</p>
          </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui, j\'ai transféré',
        cancelButtonText: 'Pas encore'
      })

      if (result.isConfirmed) {
        await paymentService.process({
          amount: props.amount,
          provider: 'transfer',
          order_id: props.orderId
        })

        Swal.fire({
          title: 'Commande en attente de vérification 🔍',
          html: `
            <div class="text-center space-y-3 mt-4">
              <p class="text-slate-800 font-bold text-base">Votre commande est enregistrée.</p>
              <p class="text-slate-600 text-sm">Nous vérifierons votre transfert dans les plus brefs délais.</p>
              <div class="pt-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-700 text-xs font-bold rounded-xl border border-purple-100">
                  📱 Vous recevrez une confirmation une fois le paiement validé.
                </span>
              </div>
            </div>
          `,
          icon: 'success',
          confirmButtonText: 'D\'accord'
        })
        emit('success', { provider: 'transfer', status: 'pending_verification' })
        closeModal()
      }
    }
  } catch (error) {
    console.error('Erreur traitement paiement:', error)
    emit('success', { provider: selectedProvider.value, status: 'pending' })
    closeModal()
  } finally {
    isProcessing.value = false
  }
}
</script>
