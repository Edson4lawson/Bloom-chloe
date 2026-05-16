<template>
  <div class="relative" :style="{ height: height + 'px' }">
    <Line v-if="hasData" :data="chartData" :options="mergedOptions" />
    <div v-else class="flex items-center justify-center h-full text-slate-400 text-sm font-medium">
      Aucune donnée disponible
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { 
  Chart as ChartJS, 
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  Filler, 
  Tooltip, 
  Legend 
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip, Legend)

const props = defineProps({
  labels: {
    type: Array,
    default: () => []
  },
  datasets: {
    type: Array,
    default: () => []
  },
  height: {
    type: Number,
    default: 256
  },
  options: {
    type: Object,
    default: () => ({})
  }
})

const hasData = computed(() => props.labels.length > 0 && props.datasets.length > 0)

const chartData = computed(() => ({
  labels: props.labels,
  datasets: props.datasets.map(ds => ({
    label: ds.label || 'Données',
    data: ds.data || [],
    borderColor: ds.borderColor || '#8b5cf6',
    backgroundColor: ds.backgroundColor || 'rgba(139, 92, 246, 0.05)',
    fill: ds.fill !== undefined ? ds.fill : true,
    tension: ds.tension || 0.4,
    pointBackgroundColor: ds.pointBackgroundColor || ds.borderColor || '#8b5cf6',
    pointRadius: ds.pointRadius || 3,
    pointHoverRadius: ds.pointHoverRadius || 6,
    borderWidth: ds.borderWidth || 2,
  }))
}))

const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#1e293b',
      titleFont: { size: 12, weight: 'bold' },
      bodyFont: { size: 11 },
      padding: 12,
      cornerRadius: 12,
      displayColors: false
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: '#f1f5f9', drawBorder: false },
      ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
    },
    x: {
      grid: { display: false },
      ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  }
}

const mergedOptions = computed(() => ({
  ...defaultOptions,
  ...props.options
}))
</script>
