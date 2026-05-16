<template>
  <div class="bg-white dark:bg-[rgb(43,44,43)] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-500 p-6 hover:shadow-md transition-all duration-300">
    <div class="flex items-center">
      <div 
        class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 transition-colors"
        :class="colorClasses"
      >
        <component :is="icon" class="w-6 h-6" />
      </div>
      <div>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">{{ title }}</p>
        <h3 ref="countUpRef" class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ formattedValue }}
        </h3>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { CountUp } from 'countup.js'

const props = defineProps({
  title: { type: String, required: true },
  value: { type: [String, Number], required: true },
  icon: { type: Object, required: true },
  color: { type: String, default: 'blue' },
  suffix: { type: String, default: '' }
})

const countUpRef = ref(null)
let countUpInstance = null

const formattedValue = computed(() => {
  if (typeof props.value === 'number') return props.value
  return props.value
})

const initCountUp = () => {
  if (countUpRef.value && typeof props.value === 'number') {
    countUpInstance = new CountUp(countUpRef.value, props.value, {
      duration: 3,
      startVal: 0,
      suffix: props.suffix,
      separator: ' ',
      decimalPlaces: 0
    })
    
    if (!countUpInstance.error) {
      countUpInstance.start()
    } else {
      console.error(countUpInstance.error)
    }
  }
}

watch(() => props.value, (newVal) => {
  if (countUpInstance && typeof newVal === 'number') {
    countUpInstance.update(newVal)
  } else {
    initCountUp()
  }
})

onMounted(() => {
  initCountUp()
})

const colorClasses = computed(() => {
  const colors = {
    blue: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
    green: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400',
    purple: 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
    yellow: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400',
    red: 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400'
  }
  return colors[props.color] || colors.blue
})
</script>
