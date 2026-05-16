import { defineStore } from 'pinia'
import { ref } from 'vue'

/**
 * UI Store — Gère l'état global de l'interface utilisateur
 */
export const useUiStore = defineStore('ui', () => {
  const isLoading = ref(false)
  const notification = ref(null)
  const sidebarOpen = ref(false)
  const modalStack = ref([])

  const showNotification = (message, type = 'success', duration = 3000) => {
    notification.value = { message, type, id: Date.now() }
    if (duration > 0) {
      setTimeout(() => {
        notification.value = null
      }, duration)
    }
  }

  const clearNotification = () => {
    notification.value = null
  }

  const setLoading = (val) => {
    isLoading.value = val
  }

  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
  }

  const pushModal = (modalId) => {
    if (!modalStack.value.includes(modalId)) {
      modalStack.value.push(modalId)
    }
  }

  const popModal = () => {
    modalStack.value.pop()
  }

  return {
    isLoading,
    notification,
    sidebarOpen,
    modalStack,
    showNotification,
    clearNotification,
    setLoading,
    toggleSidebar,
    pushModal,
    popModal
  }
})
