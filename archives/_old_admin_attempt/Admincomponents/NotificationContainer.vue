<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <NotificationToast
      v-for="notification in notifications"
      :key="notification.id"
      :type="notification.type"
      :message="notification.message"
      :duration="0"
      @close="removeNotification(notification.id)"
    />
  </div>
</template>

<script setup>
import { useNotifications } from '../Services/notificationService.js'
import NotificationToast from './NotificationToast.vue'

const { notifications, removeNotification } = useNotifications()
</script>