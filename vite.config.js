import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    tailwindcss()
  ],
  
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  
  // ========================================
  // OPTIMISATIONS DE PERFORMANCE
  // ========================================
  
  build: {
    // Séparation du code en chunks
    rollupOptions: {
      output: {
        manualChunks: {
          // Vendor chunks - bibliotheques tierces
          'vendor-vue': ['vue', 'vue-router', 'pinia'],
          'vendor-ui': ['@iconify/vue', 'sweetalert2'],
          'vendor-animation': ['aos'],
        },
        // Noms de fichiers avec hash pour le cache
        chunkFileNames: 'assets/js/[name]-[hash].js',
        entryFileNames: 'assets/js/[name]-[hash].js',
        assetFileNames: 'assets/[ext]/[name]-[hash].[ext]'
      }
    },
    
    // Taille de chunk optimale
    chunkSizeWarningLimit: 500,
    
    // Minification avancée
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,  // Supprimer console.log en production
        drop_debugger: true,
        pure_funcs: ['console.log', 'console.info']
      }
    },
    
    // Génération de sourcemaps uniquement en dev
    sourcemap: false,
    
    // Assets inline < 4kb
    assetsInlineLimit: 4096
  },
  
  // Optimisations du serveur de dev
  server: {
    // Pre-bundling des dépendances
    warmup: {
      clientFiles: [
        './src/components/Header.vue',
        './src/components/Hero.vue',
        './src/components/Products.vue'
      ]
    }
  },
  
  // Optimisation des dépendances
  optimizeDeps: {
    include: [
      'vue',
      'pinia',
      '@iconify/vue',
      'axios'
    ]
  }
})

