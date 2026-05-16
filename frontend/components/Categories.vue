<template>
    <section id="categories" class="py-16 scroll-mt-20 ">
        <div class="container mx-auto px-4">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-3">Decouvrez Nos Collections d'accessoires 😊...</h2>
                <p class="text-lg max-w-2xl mx-auto">
                    Trouvez les articles de choix qui correspondent a votre personnalite
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

import cat1 from '../assets/categorie1.jpg'
import cat2 from '../assets/categorie2.jpg'
import cat3 from '../assets/categorie3.jpg'
import cat4 from '../assets/categorie4.jpg'
import cat5 from '../assets/categorie5.jpg'
import cat6 from '../assets/categorie6.jpg'
import cat7 from '../assets/categorie7.jpg'
import cat8 from '../assets/categorie8.jpg'
import cat9 from '../assets/categorie9.jpg'
import cat10 from '../assets/categorie10.jpg'
import cat11 from '../assets/categorie11.jpg'
import cat12 from '../assets/categorie12.jpg'

const categories = ref([
    { image: cat1, title: "Montre Nibosi Doree", description: "Montre sportive et élégante en acier doré avec cadran noir, date et index lumineux. À‰tanche 50 m, idéale pour un style moderne et dynamique." },
    { image: cat2, title: "Bague chevaliere etoile", description: "Chevalière minimaliste en métal doré avec gravure d'étoile. Design moderne et unisexe, parfaite pour un look chic et discret." },
    { image: cat3, title: "Parfum Valentino Uomo", description: "Parfum masculin élégant aux notes boisées et orientales. Son flacon iconique au design prismatique reflète le luxe italien." },
    { image: cat4, title: "Parfum Khamrah Lattafa", description: "Parfum oriental riche et épicé dans un flacon luxueux aux motifs géométriques. Une fragrance intense inspirée de la parfumerie arabe." },
    { image: cat5, title: "Bague Chevalière", description: "Chevalière imposante aux gravures baroques détaillées et plateau noir. Un bijou puissant au style royal, symbole de caractère et d'élégance." },
    { image: cat6, title: "Bracelet Cubaine Bitcoin", description: "Bracelet doré À  chaîne cubaine avec médaillon Bitcoin serti de cristaux. Style hip-hop luxueux, parfait pour affirmer une allure moderne et audacieuse." },
    { image: cat7, title: "Collier & Boucles d'Oreilles", description: "Ensemble élégant en or rose composé d'un collier et de boucles d'oreilles assorties. Design goutte serti de cristaux scintillants, parfait pour mariages et occasions spéciales." },
    { image: cat8, title: "Boucles d'Oreilles Perles", description: "Boucles pendantes raffinées associant cristaux baguette lumineux et perles nacrées. Un bijou chic et intemporel pour sublimer vos tenues de jour comme de soirée." },
    { image: cat9, title: "Parfum Pure XS", description: "Fragrance féminine intense et sensuelle dans un flacon sculptural bordeaux, orné d'un serpent doré. Un parfum audacieux qui incarne le luxe et la séduction." },
    { image: cat10, title: "Collier Multi-rangs Doré", description: "Collier tendance À  trois chaînes superposées avec pendentifs étoile, médaille et cadenas. Style moderne et bohème chic, idéal pour un look élégant au quotidien." },
    { image: cat11, title: "Boucles d'Oreilles Goutte", description: "Puces d'oreilles minimalistes en forme de goutte d'eau. Finition dorée brillante, légères et confortables, parfaites pour un style sobre et intemporel." },
    { image: cat12, title: "Parfum J'adore Dior", description: "Parfum féminin iconique aux notes florales raffinées. Son flacon amphore doré incarne l'élégance et le luxe intemporel de la maison Dior. Parfait pour sublimer votre compagnie" },
])
</script>

