<template>
  <router-view />
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
authStore.initializeFromStorage()
</script>
