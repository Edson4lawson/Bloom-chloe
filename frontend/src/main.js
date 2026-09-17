import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import router from './router'
import './style.css'
import App from './App.vue'

// ── Iconify offline bundle ──────────────────────────────────────────────────
// Pre-loads the full Solar icon set locally so no CDN requests are made.
// This prevents ERR_INTERNET_DISCONNECTED errors on api.iconify.design.
import { addCollection } from '@iconify/vue'
import solarIcons from '@iconify-json/solar/icons.json'
addCollection(solarIcons)
// ───────────────────────────────────────────────────────────────────────────

const app = createApp(App)
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

app.use(pinia)
app.use(router)
app.mount('#app')


