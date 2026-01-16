<template>
    <section class="py-20 bg-white" id="contact" data-aos="fade-up">
        <div class="container px-4 md:px-6 mx-auto">
            <div class="flex flex-col lg:flex-row gap-12 lg:items-stretch">
                <!-- Contact Info Card -->
                <div class="w-full lg:w-5/12 bg-purple-50 rounded-3xl p-8 md:p-12 flex flex-col justify-between" data-aos="fade-right">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 leading-tight">
                            Contactez-nous pour <br class="hidden md:block"> plus d'informations
                        </h2>
                        <p class="mt-4 text-gray-600">Nous sommes à votre écoute pour vous conseiller sur nos fragrances.</p>

                        <div class="mt-10 space-y-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-purple-600">
                                    <Icon icon="solar:map-point-bold-duotone" class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Adresse</p>
                                    <p class="text-gray-700 font-medium">Cotonou, Littoral, Bénin</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-purple-600">
                                    <Icon icon="solar:phone-calling-bold-duotone" class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Téléphone</p>
                                    <p class="text-gray-700 font-medium">+229 123 456 789</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-purple-600">
                                    <Icon icon="solar:letter-bold-duotone" class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Email</p>
                                    <p class="text-gray-700 font-medium">contact@bloomchloe.com</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-purple-100">
                        <h4 class="text-gray-700 mb-4 font-bold uppercase text-xs tracking-widest">Suivez l'aventure</h4>
                        <div class="flex items-center space-x-4">
                            <a v-for="(social, index) in socialLinks" :href="social.url" :key="index"
                          class="group relative w-10 h-10 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white transition-all duration-300">
                          <Icon :icon="social.icon" class="w-5 h-5 transition-transform group-hover:scale-110" />
                      </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="w-full lg:w-7/12" data-aos="fade-left">
                    <div class="bg-white border border-purple-100 rounded-3xl p-8 md:p-12 shadow-xl shadow-purple-50">
                        <h3 class="text-2xl font-bold text-gray-800 mb-8">Envoyez-nous un message</h3>

                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-gray-600">Nom Complet</label>
                                    <input type="text" placeholder="Votre nom" required
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-purple-400 focus:ring-4 focus:ring-purple-100 outline-none transition-all" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-gray-600">Adresse Email</label>
                                    <input type="email" placeholder="votre@email.com" required
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-purple-400 focus:ring-4 focus:ring-purple-100 outline-none transition-all" />
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-600">Sujet</label>
                                <input type="text" placeholder="Comment pouvons-nous vous aider ?"
                                    class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-purple-400 focus:ring-4 focus:ring-purple-100 outline-none transition-all" />
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-600">Message</label>
                                <textarea required rows="4" placeholder="Votre message..."
                                    class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-purple-400 focus:ring-4 focus:ring-purple-100 outline-none transition-all resize-none"></textarea>
                            </div>

                            <button type="submit" 
                                class="w-full py-5 bg-purple-600 text-white rounded-2xl font-bold text-lg hover:bg-purple-700 hover:shadow-xl hover:shadow-purple-200 transition-all transform active:scale-[0.98]">
                                Envoyer le message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const socialLinks = [
  { icon: 'fa6-brands:facebook-f', url: 'https://facebook.com/bloomchloe' },
  { icon: 'fa6-brands:instagram', url: 'https://instagram.com/bloomchloe' },
  { icon: 'fa6-brands:pinterest', url: 'https://pinterest.com/bloomchloe' },
  { icon: 'fa6-brands:youtube', url: 'https://youtube.com/bloomchloe' }
];


const handleSubmit = () => {
    // Logique d'envoi
    alert('Message envoyé avec succès !');
};

onMounted(() => {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });
});
</script>
