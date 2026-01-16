<template>
    <section id="categories" class="py-16 scroll-mt-20 ">
        <div class="container mx-auto px-4">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-3">Découvrez Nos Collections d'accesoires😊...</h2>
                <p class="text-lg max-w-2xl mx-auto">
                    Trouvez les parfums qui correspondent à votre personnalité
                </p>
            </div>
            <!-- Categories Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="(category, index) in categories" :key="index"
                    :data-aos="'fade-up'" :data-aos-delay="100 * index"
                    class="group relative overflow-hidden rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 aspect-[4/5] sm:aspect-square lg:aspect-[4/5]">
                    <!-- Image -->
                    <OptimizedImage 
                        :src="category.image" 
                        :alt="category.alt"
                        imageClass="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                    />
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-purple-900/90 via-purple-900/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>
                    
                    <!-- Content -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 transform translate-y-2 group-hover:translate-y-0 transition-transform">
                        <h3 class="text-2xl font-bold text-white mb-2"> {{ category.title }} </h3>
                        <p class="text-purple-100 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                            {{ category.description }}
                        </p>
                        <div class="mt-4 w-10 h-1 bg-white rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import OptimizedImage from './OptimizedImage.vue'
import { categoriesService } from '@/services/api'

// Fallback images si pas d'image en DB
import product8 from '../assets/product8.jpg'
import product9 from '../assets/product9.jpg'
import product10 from '../assets/product10.jpg'
import product11 from '../assets/product11.jpg'
import product12 from '../assets/product12.jpg'
import product13 from '../assets/product13.jpg'
const categories = ref([

    { image: product8, title: "Men's Collection", description: "Bold & masculine scents", alt: "Men's Collection" },
    { image: product9, title: "Women's Collection", description: "Bold & masculine scents", alt: "Men's Collection" },
    { image: product10, title: "Men's Collection", description: "Bold & masculine scents", alt: "Men's Collection" },
    { image: product11, title: "Men's Collection", description: "Bold & masculine scents", alt: "Men's Collection" },
    { image: product12, title: "Men's Collection", description: "Bold & masculine scents", alt: "Men's Collection" },
    { image: product13, title: "Men's Collection", description: "Bold & masculine scents", alt: "Men's Collection" },
])
</script>