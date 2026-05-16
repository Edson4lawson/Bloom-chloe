<template>
  <div v-if="visible" :class="toastClass" class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <span class="text-lg">{{ icon }}</span>
      </div>
      <div class="ml-3">
        <p class="text-sm font-medium">{{ message }}</p>
      </div>
      <div class="ml-4 flex-shrink-0">
        <button @click="close" class="text-white hover:opacity-75">
          <span class="sr-only">Fermer</span>
          ×
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  message: {
    type: String,
    required: true
  },
  duration: {
    type: Number,
    default: 5000
  }
})

const emit = defineEmits(['close'])

const visible = ref(true)

const toastClass = computed(() => {
  const classes = {
    success: 'bg-green-600 text-white',
    error: 'bg-red-600 text-white',
    warning: 'bg-yellow-600 text-white',
    info: 'bg-blue-600 text-white'
  }
  return classes[props.type]
})

const icon = computed(() => {
  const icons = {
    success: '✓',
    error: '✕',
    warning: '⚠',
    info: 'ℹ'
  }
  return icons[props.type]
})

const close = () => {
  visible.value = false
  emit('close')
}

onMounted(() => {
  if (props.duration > 0) {
    setTimeout(() => {
      close()
    }, props.duration)
  }
})
</script>
