/**
 * Utilitaire pour construire les URLs d'images produits
 */

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8001'

/**
 * Construit l'URL absolue d'une image produit
 * Gère les différents formats possibles d'URL stockés en base
 */
export function getProductImageUrl(url) {
  if (!url) return '/placeholder.jpg'
  
  // Si c'est déjà une URL absolue
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url
  }
  
  // Si c'est un chemin relatif vers les assets frontend
  if (url.startsWith('/frontend/src/assets/') || url.startsWith('frontend/src/assets/')) {
    const filename = url.split('/').pop()
    return new URL(`../assets/${filename}`, import.meta.url).href
  }
  
  // Si c'est un chemin relatif vers src/assets
  if (url.startsWith('/src/assets/') || url.startsWith('src/assets/')) {
    const filename = url.split('/').pop()
    return new URL(`../assets/${filename}`, import.meta.url).href
  }
  
  // Si c'est juste un nom de fichier
  if (!url.includes('/')) {
    return new URL(`../assets/${url}`, import.meta.url).href
  }
  
  // Fallback: essayer comme chemin relatif au backend
  return `${API_URL}${url.startsWith('/') ? '' : '/'}${url}`
}

export default { getProductImageUrl }
