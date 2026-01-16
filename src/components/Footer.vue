<script setup>
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Swal from 'sweetalert2';

// Fonction pour le défilement fluide
const scrollToSection = (e, targetId) => {
  if (e) {
    e.preventDefault();
  }
  
  const element = document.querySelector(targetId);
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  }
};

// Ajouter les styles pour les liens avec effet de balayage
const addSweepStyles = () => {
  const style = document.createElement('style');
  style.textContent = `
    .sweep-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: currentColor;
      transition: width 0.3s ease;
    }
    .sweep-link:hover::after {
      width: 100%;
    }
  `;
  document.head.appendChild(style);
};

// Initialiser les styles de balayage
onMounted(() => {
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    mirror: false
  });
  addSweepStyles();
});

// Initialiser AOS
onMounted(() => {
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    mirror: false
  });
});
const quickLinks = ref([
  { name: 'Accueil', href: '#hero' },
  { name: 'Nos Produits', href: '#products' },
  { name: 'Nouveautés', href: '#store' },
  { name: 'Catégories', href: '#categories' },
  { name: 'Contact', href: '#contact' }
])
const customerServices = ref([
  { name: 'Politique de confidentialité', to: '/privacy' },
  { name: 'FAQ', to: '/faq' },
  { name: 'Livraison', to: '/shipping' },
  { name: 'Retours', to: '/returns' },
  { name: 'Conditions générales', to: '/terms' }
])
const paymentIcons = ref([
  { 
    name: 'visa',
    icon: 'logos:visa',
    color: '#1A1F71'
  },
  { 
    name: 'paypal',
    icon: 'logos:paypal',
    color: '#003087'
  },
  { 
    name: 'mtn-momo',
    icon: 'simple-icons:mtn',
    color: '#FFC72C'
  }
]);
const handleNewsletter = () => {
  Swal.fire({
    title: 'Inscrit !',
    text: 'Merci de votre inscription à notre newsletter.',
    icon: 'success',
    confirmButtonColor: '#9333ea',
    borderRadius: '24px'
  });
};
const socialLinks = ref([
  { icon: 'fa6-brands:facebook-f', url: 'https://facebook.com/bloomchloe' },
  { icon: 'fa6-brands:instagram', url: 'https://instagram.com/bloomchloe' },
  { icon: 'fa6-brands:pinterest', url: 'https://pinterest.com/bloomchloe' },
  { icon: 'fa6-brands:youtube', url: 'https://youtube.com/bloomchloe' }
])

</script>

<template>
    <footer class=" pt-16 pb-8">
        <div class="container mx-auto px-6">
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div>
                <h3 class="text-3xl text-purple-700 font-medium mb-6" data-aos="fade-up" data-aos-delay="150">Bloom by Chloe</h3>
                <p class="mb-6 text-lg" data-aos="fade-up" data-aos-delay="150">Votre destination privilégiée pour les parfums de luxe du monde entier.</p>
               
            </div>
            <!-- Quick Links -->
            <div data-aos="fade-up" data-aos-delay="200">
                <h3 class="text-dark mb-6 text-lg font-semibold" data-aos="fade-up" data-aos-delay="250">Liens Rapides</h3>
                <ul class="space-y-2">
                    <li v-for="(link, index) in quickLinks" :key="index">
                        <a 
                          :href="link.href" 
                          @click="(e) => scrollToSection(e, link.href)" 
                          class="sweep-link inline-block py-1 text-gray-700 hover:text-purple-600 transition-colors relative"
                        >
                          {{ link.name }}
                        </a>
                    </li>
                </ul>
            </div>
            <!-- customer Services -->
            <div data-aos="fade-up" data-aos-delay="300">
                <h3 class="text-dark mb-6 text-lg font-semibold" data-aos="fade-up" data-aos-delay="350">Service Client</h3>
                <ul class="space-y-2">
                    <li v-for="(link, index) in customerServices" :key="index">
                        <a 
                          :href="link.to" 
                          class="sweep-link inline-block py-1 text-gray-700 hover:text-purple-600 transition-colors relative"
                        >
                          {{ link.name }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div data-aos="fade-up" data-aos-delay="400" class="lg:col-span-1">
                <h3 class="text-dark mb-6 text-lg font-bold">Newsletter</h3>
                <p class="text-sm text-gray-500 mb-6 font-medium">Inscrivez-vous pour recevoir nos offres exclusives et les dernières nouveautés.</p>
                
                <form @submit.prevent="handleNewsletter" class="space-y-3">
                  <div class="relative group">
                    <input 
                      type="email" 
                      placeholder="votre@email.com" 
                      required
                      class="w-full px-5 py-4 bg-white border border-purple-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all text-sm font-medium"
                    >
                    <button 
                      type="submit"
                      class="absolute right-2 top-2 bottom-2 px-6 bg-purple-600 text-white rounded-xl font-bold text-xs hover:bg-purple-700 transition-all active:scale-95 shadow-lg shadow-purple-200"
                    >
                      S'abonner
                    </button>
                  </div>
                  <p class="text-[10px] text-gray-400 px-2 italic">
                    * En vous inscrivant, vous acceptez notre politique de confidentialité.
                  </p>
                </form>

                <div class="mt-8">
                  <h4 class="text-dark mb-4 text-sm font-bold opacity-50 uppercase tracking-widest">Suivez-nous</h4>
                  <div class="flex items-center gap-3">
                      <a v-for="(social, index) in socialLinks" :href="social.url" :key="index"
                          class="group relative w-10 h-10 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white transition-all duration-300">
                          <Icon :icon="social.icon" class="w-5 h-5 transition-transform group-hover:scale-110" />
                      </a>
                  </div>
                </div>
            </div>
        </div>
        <!-- Footer bottom -->
        <div class="border-t border-gray-200 pt-8 mt-8">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center text-black">
                    <p class="text-center md:text-left text-gray-600">
                        &copy; {{ new Date().getFullYear() }} Bloom by Chloe. Tous droits réservés.<br>
                        <a href="https://Edson-lawson.vercel.app" target="_blank" rel="noopener" class="sweep-link inline-block py-1 text-black hover:text-purple-500 transition-colors duration-300 relative">
                            Développé par Edson Lawson
                        </a>
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <div v-for="(method, index) in paymentIcons" :key="index" 
     class="flex items-center justify-center w-12 h-8 rounded-md"
     :style="{ backgroundColor: method.color }">
  <a href="">
    <Icon :icon="method.icon" class="w-6 h-6 text-white" />
  </a>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</template>
