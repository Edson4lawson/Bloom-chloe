<template>
    <div id="about" class="min-h-screen scroll-mt-20 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row gap-12 mb-20">
                <!-- Image Section -->
                <div class="lg:w-1/2">
                    <div class="relative rounded-2xl overflow-hidden aspect-[16/9] bg-primary/5 flex items-center justify-center shadow-2xl">
                        <img :src="product" alt="Luxury perfumes" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/70 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 text-black">
                            <h3 class="text-2xl font-bold text-accent">Since 2021</h3>
                            <p class="mt-2 text-white/90">Crafting perfumes with passion</p>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2 flex flex-col justify-center">
                    <h2 class="text-5xl font-bold text-primary mb-8">About <span class="text-accent">Us</span></h2>
                    <p class="text-xl text-secondary mb-6 leading-relaxed">
                        We are passionate artisans dedicated to creating exceptional fragrances that tell your unique story. Each perfume is crafted with the finest ingredients and meticulous attention to detail.
                    </p>

                    <div class="grid grid-cols-2 gap-6 mt-8">
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-accent/20 hover:shadow-2xl hover:border-accent/40 transition-all duration-300">
                            <div class="text-center">
                                <h4 class="text-3xl font-bold text-accent mb-2">500+</h4>
                                <p class="text-primary font-medium">Unique fragrances</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-lg border border-accent/20 hover:shadow-2xl hover:border-accent/40 transition-all duration-300">
                            <div class="text-center">
                                <h4 class="text-3xl font-bold text-accent mb-2">50+</h4>
                                <p class="text-primary font-medium">Countries worldwide</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-lg border border-accent/20 hover:shadow-2xl hover:border-accent/40 transition-all duration-300">
                            <div class="text-center">
                                <h4 class="text-3xl font-bold text-accent mb-2">100%</h4>
                                <p class="text-primary font-medium">Customer satisfaction</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-lg border border-accent/20 hover:shadow-2xl hover:border-accent/40 transition-all duration-300">
                            <div class="text-center">
                                <h4 class="text-3xl font-bold text-accent mb-2">24/7</h4>
                                <p class="text-primary font-medium">Customer support</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import product from '../assets/product13.jpg'
</script>