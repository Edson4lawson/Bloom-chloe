<template>
  <section class="relative container mx-auto px-4 md:px-6 pt-32 pb-16 md:py-32 lg:py-40 min-h-[90vh] flex items-center"
    id="hero">
    <!-- Floating shapes for background depth -->
    <div class="absolute top-1/4 -left-20 w-64 h-64 bg-purple-300/20 rounded-full blur-3xl animate-float-slow"></div>
    <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-pink-200/20 rounded-full blur-3xl animate-float-slow"
      style="animation-delay: -5s;"></div>

    <div class="relative flex flex-col lg:flex-row items-center justify-between gap-12 w-full z-10">
      <div
        class="w-full lg:w-3/5 flex flex-col items-center lg:items-start text-center lg:text-left order-1 lg:order-1">
        <div class="space-y-8">
          <div
            class="inline-flex items-center px-4 py-2 rounded-full bg-purple-100/50 border border-purple-200 text-purple-700 text-sm font-bold tracking-widest uppercase mb-4"
            data-aos="fade-down">
            Collection Prestige 2026
          </div>
          <h2 class="text-4xl font-extrabold text-gray-800 sm:text-6xl tracking-tight" data-aos="fade-up"
            data-aos-delay="100">
            L'essence de <span class="text-purple-600">votre</span> beauté
          </h2>
          <p class="bg-gradient-to-r from-purple-900 via-indigo-700 to-purple-800 bg-clip-text text-5xl font-black text-transparent sm:text-7xl leading-[1.1] tracking-tighter"
            data-aos="fade-up" data-aos-delay="200">
            Révélez votre <br> éclat unique !
          </p>

          <p class="text-gray-600 text-lg md:text-xl max-w-xl mx-auto lg:mx-0 leading-relaxed font-medium"
            data-aos="fade-up" data-aos-delay="250">
            Découvrez une collection exclusive de fragrances raffinées, conçues pour sublimer votre présence à chaque
            instant.
          </p>

          <div class="pt-8 flex flex-col sm:flex-row items-center gap-4 lg:justify-start justify-center">
            <button @click="scrollToProducts"
              class="group relative overflow-hidden h-16 inline-flex items-center justify-center rounded-full bg-purple-700 px-12 text-xl font-black text-white shadow-xl transition-all hover:scale-105 active:scale-95"
              data-aos="fade-up" data-aos-delay="300">
              <span class="relative z-10">Voir la collection</span>
              <div
                class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              </div>
            </button>
            <button @click="scrollToStore"
              class="h-16 px-8 rounded-full border-2 border-purple-200 text-purple-700 font-bold hover:bg-purple-80 transition-colors"
              data-aos="fade-up" data-aos-delay="350">
               Nos secrets
            </button>
          </div>
        </div>
      </div>
      <div class="w-full lg:w-2/5 flex justify-center lg:justify-end order-2 lg:order-2">
        <div class="relative w-full max-w-[450px]" data-aos="zoom-in-left" data-aos-delay="100">
          <div
            class="absolute -inset-6 bg-gradient-to-tr from-purple-600/20 to-pink-500/20 rounded-[3rem] blur-2xl animate-pulse">
          </div>
          <div
            class="relative bg-white/40 backdrop-blur-md p-4 rounded-[2.5rem] border border-white/50 shadow-2xl animate-float">
            <OptimizedImage 
              :src="heroImg" 
              alt="Bloom by Chloe Perfume"
              imageClass="w-full aspect-[4/5] object-cover rounded-[2rem] shadow-lg"
              :eager="true"
            />
            <!-- Badge flottant -->
            <div
              class="absolute -bottom-6 -left-6 bg-white p-4 rounded-2xl shadow-xl border border-purple-50 animate-bounce-slow">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                  <Icon icon="solar:star-bold" class="w-6 h-6 text-purple-600" />
                </div>
                <div>
                  <p class="text-xs text-gray-500 font-bold">Produit Phare</p>
                  <p class="text-sm font-black text-gray-800">Best Seller 2025</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import OptimizedImage from './OptimizedImage.vue'
import heroImg from '../assets/image0.jpg'

const scrollToProducts = () => {
  const element = document.querySelector('#products');
  if (element) {
    const offset = 80;
    const elementPosition = element.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - offset;
    window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
  }
};

const scrollToStore = () => {
  const element = document.querySelector('#store');
  if (element) {
    const offset = 80;
    const elementPosition = element.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - offset;
    window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
  }
};
</script>

<style scoped>
@keyframes float {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-20px);
  }
}

@keyframes float-slow {

  0%,
  100% {
    transform: translate(0, 0);
  }

  50% {
    transform: translate(20px, 20px);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-float-slow {
  animation: float-slow 10s ease-in-out infinite;
}

.animate-bounce-slow {
  animation: bounce 4s infinite ease-in-out;
}

@keyframes bounce {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-10px);
  }
}
</style>