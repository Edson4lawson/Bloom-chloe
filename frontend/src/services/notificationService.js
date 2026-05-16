import { ref } from 'vue'

const notifications = ref([])
let nextId = 1

export const useNotifications = () => {
  const addNotification = ({ type = 'info', message, duration = 5000 }) => {
    const id = nextId++
    notifications.value.push({ id, type, message })

    if (duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, duration)
    }
  }

  const removeNotification = (id) => {
    notifications.value = notifications.value.filter(n => n.id !== id)
  }

  return {
    notifications,
    addNotification,
    removeNotification
  }
}
