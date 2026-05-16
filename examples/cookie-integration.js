// Exemple d'intégration du système de cookies avec Google Analytics et Meta Pixel
// À placer dans votre fichier main.js ou dans un composant dédié

import { useCookieConsent } from '@/composables/useCookieConsent'

// Configuration des services de tracking
const ANALYTICS_ID = 'G-XXXXXXXXXX' // Remplacez par votre ID Google Analytics
const META_PIXEL_ID = 'XXXXXXXXXXXXXXXX' // Remplacez par votre ID Meta Pixel
const TIKTOK_PIXEL_ID = 'XXXXXXXXXXXXXXXX' // Remplacez par votre ID TikTok Pixel

// Initialisation du système de consentement
export function initializeTracking() {
  const { hasConsent, loadGoogleAnalytics, loadMetaPixel, loadTikTokPixel } = useCookieConsent()

  // Configuration initiale de Google Analytics (mode consentement par défaut)
  if (typeof window !== 'undefined') {
    window.dataLayer = window.dataLayer || []
    function gtag(){dataLayer.push(arguments)}
    
    // Configuration par défaut : consentement refusé
    gtag('consent', 'default', {
      'ad_storage': 'denied',
      'analytics_storage': 'denied',
      'ad_user_data': 'denied',
      'ad_personalization': 'denied'
    })
    
    window.gtag = gtag
  }

  // Vérifier le consentement au chargement
  if (hasConsent.value) {
    loadTrackingScripts()
  }
}

// Chargement conditionnel des scripts
function loadTrackingScripts() {
  const { hasCategoryConsent, loadGoogleAnalytics, loadMetaPixel, loadTikTokPixel } = useCookieConsent()

  // Google Analytics
  if (hasCategoryConsent('analytics')) {
    loadGoogleAnalytics(ANALYTICS_ID)
      .then(() => {
        console.log('✅ Google Analytics chargé avec succès')
      })
      .catch((error) => {
        console.error('❌ Erreur lors du chargement de Google Analytics:', error)
      })
  }

  // Meta Pixel
  if (hasCategoryConsent('marketing')) {
    loadMetaPixel(META_PIXEL_ID)
      .then(() => {
        console.log('✅ Meta Pixel chargé avec succès')
      })
      .catch((error) => {
        console.error('❌ Erreur lors du chargement de Meta Pixel:', error)
      })
  }

  // TikTok Pixel
  if (hasCategoryConsent('marketing')) {
    loadTikTokPixel(TIKTOK_PIXEL_ID)
      .then(() => {
        console.log('✅ TikTok Pixel chargé avec succès')
      })
      .catch((error) => {
        console.error('❌ Erreur lors du chargement de TikTok Pixel:', error)
      })
  }
}

// Exemples d'événements de tracking
export const trackingEvents = {
  // E-commerce events
  pageView: (pageName) => {
    if (typeof window.gtag !== 'undefined') {
      window.gtag('config', ANALYTICS_ID, {
        page_title: pageName
      })
    }
    if (typeof window.fbq !== 'undefined') {
      window.fbq('track', 'PageView')
    }
  },

  addToCart: (productId, productName, price) => {
    if (typeof window.gtag !== 'undefined') {
      window.gtag('event', 'add_to_cart', {
        currency: 'XOF',
        value: price,
        items: [{
          item_id: productId,
          item_name: productName,
          quantity: 1
        }]
      })
    }
    if (typeof window.fbq !== 'undefined') {
      window.fbq('track', 'AddToCart', {
        content_name: productName,
        content_ids: [productId],
        content_type: 'product',
        value: price,
        currency: 'XOF'
      })
    }
  },

  purchase: (orderId, total, items) => {
    if (typeof window.gtag !== 'undefined') {
      window.gtag('event', 'purchase', {
        transaction_id: orderId,
        value: total,
        currency: 'XOF',
        items: items
      })
    }
    if (typeof window.fbq !== 'undefined') {
      window.fbq('track', 'Purchase', {
        value: total,
        currency: 'XOF',
        content_ids: items.map(item => item.id),
        content_type: 'product'
      })
    }
  },

  // Lead generation
  newsletterSignup: () => {
    if (typeof window.gtag !== 'undefined') {
      window.gtag('event', 'generate_lead', {
        event_category: 'engagement'
      })
    }
    if (typeof window.fbq !== 'undefined') {
      window.fbq('track', 'Lead')
    }
  },

  contactForm: () => {
    if (typeof window.gtag !== 'undefined') {
      window.gtag('event', 'contact', {
        event_category: 'engagement'
      })
    }
  }
}

// Utilisation dans Vue.js
export function useTracking() {
  const { hasCategoryConsent } = useCookieConsent()

  const trackEvent = (eventName, parameters = {}) => {
    // Vérifier le consentement avant de tracker
    if (!hasCategoryConsent('analytics')) {
      console.log('🚫 Tracking bloqué : pas de consentement pour les cookies analytiques')
      return
    }

    // Appeler l'événement approprié
    if (trackingEvents[eventName]) {
      trackingEvents[eventName](parameters)
    } else {
      console.warn(`Événement de tracking inconnu: ${eventName}`)
    }
  }

  return {
    trackEvent,
    events: trackingEvents
  }
}

// Instructions d'installation:

/*
1. Dans votre fichier main.js:

import { initializeTracking } from './examples/cookie-integration'

// Initialiser le tracking après le montage de l'app
app.mount('#app')
initializeTracking()

2. Dans vos composants Vue:

import { useTracking } from './examples/cookie-integration'

export default {
  setup() {
    const { trackEvent } = useTracking()

    const addToCart = (product) => {
      // Logique d'ajout au panier...
      
      // Tracking de l'événement
      trackEvent('addToCart', {
        productId: product.id,
        productName: product.name,
        price: product.price
      })
    }

    return { addToCart }
  }
}

3. Dans votre template HTML (si vous utilisez des scripts externes):

<!-- Google Analytics (chargé conditionnellement) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>

<!-- Meta Pixel (chargé conditionnellement) -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', 'XXXXXXXXXXXXXXXX');
fbq('track', 'PageView');
</script>

4. Configuration des variables d'environnement:

Dans votre fichier .env:
VITE_GA_ID=G-XXXXXXXXXX
VITE_META_PIXEL_ID=XXXXXXXXXXXXXXXX
VITE_TIKTOK_PIXEL_ID=XXXXXXXXXXXXXXXX

5. Tests et validation:

- Ouvrez les outils de développement
- Vérifiez l'onglet "Application" → "Cookies"
- Testez différents scénarios de consentement
- Validez que les scripts ne se chargent qu'avec le consentement approprié

6. Conformité RGPD:

- Le système est conforme au RGPD
- Consentement explicite requis
- Possibilité de retirer le consentement
- Transparence totale sur l'utilisation des cookies
- Durées de conservation limitées
*/
