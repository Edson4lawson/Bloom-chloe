<template>
  <button
    @click="themeStore.toggleTheme"
    class="relative p-2 rounded-xl bg-slate-100 dark:bg-[rgb(43,44,43)] text-slate-500 dark:text-slate-400 hover:text-accent transition-all duration-300 group overflow-hidden border border-transparent dark:border-slate-500"
    :title="themeStore.isDark ? 'Passer en mode jour' : 'Passer en mode nuit'"
  >
    <!-- Background glow effect on hover -->
    <div class="absolute inset-0 bg-accent/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
    
    <div class="relative flex items-center justify-center">
      <!-- Sun Icon -->
      <Sun
        v-if="themeStore.isDark"
        class="w-5 h-5 animate-in slide-in-from-bottom-2 duration-500"
      />
      <!-- Moon Icon -->
      <Moon
        v-else
        class="w-5 h-5 animate-in slide-in-from-top-2 duration-500"
      />
    </div>
  </button>
</template>

<script setup>
import { Sun, Moon } from 'lucide-vue-next'
import { useThemeStore } from '../store/theme'

const themeStore = useThemeStore()
</script>
