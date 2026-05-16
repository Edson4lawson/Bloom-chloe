import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { cartService } from '@/services/api';
import { useAuthStore } from './auth';

export const useCartStore = defineStore('cart', () => {
  const items = ref([]);
  const loading = ref(false);
  const error = ref(null);
  const authStore = useAuthStore();
  
  // Getters
  const totalItems = computed(() => 
    items.value.reduce((total, item) => total + item.quantity, 0)
  );
  
  const subtotal = computed(() => 
    items.value.reduce((total, item) => 
      total + (parseFloat(item.price) * item.quantity), 0
    )
  );
  
  // Au Bénin, livraison souvent fixe ou selon zone
  const shippingFee = computed(() => 
    totalItems.value === 0 ? 0 : (subtotal.value > 50000 ? 0 : 2000)
  );
  
  const cartTotal = computed(() => 
    subtotal.value + shippingFee.value
  );

  // Actions
  async function fetchCart() {
    if (!authStore.isAuthenticated) return;

    loading.value = true;
    error.value = null;
    try {
      const response = await cartService.get();
      // On fusionne le panier local avec celui du serveur
      const serverItems = response.data.items || [];
      
      if (serverItems.length > 0) {
           items.value = serverItems.map(item => ({
                id: item.product_id || item.id,
                product_id: item.product_id || item.id,
                name: item.name,
                price: parseFloat(item.price),
                image: item.image_url,
                quantity: parseInt(item.quantity)
           }));
      }
    } catch (err) {
      console.warn('Sync panier backend échoué');
    } finally {
      loading.value = false;
    }
  }
  
  async function addToCart(product, quantity = 1) {
    const authStore = useAuthStore();
    const productId = product.product_id || product.id;
    const existing = items.value.find(i => (i.product_id || i.id) === productId);
    
    // Mise à jour locale immédiate
    if (existing) {
      existing.quantity += quantity;
    } else {
      items.value.push({
        id: productId,
        product_id: productId,
        name: product.title || product.name,
        price: parseFloat(product.price),
        image: product.thumbnail || product.image || '/src/assets/produit1.jpg',
        quantity: quantity
      });
    }

    // Sync si connecté - Sans bloquer le UI
    if (authStore.isAuthenticated) {
      cartService.add(productId, quantity).catch(err => {
        console.error('Erreur sync panier backend', err);
      });
    }
  }
  
  async function updateQuantity(productId, quantity) {
    if (quantity <= 0) {
      return removeFromCart(productId);
    }

    const item = items.value.find(i => (i.product_id || i.id) === productId);
    if (item) {
      item.quantity = quantity;
    }

    if (authStore.isAuthenticated) {
      try {
        await cartService.update(productId, quantity);
      } catch (err) {
        console.error('Erreur update panier backend');
      }
    }
  }

  async function removeFromCart(productId) {
    items.value = items.value.filter(i => (i.product_id || i.id) !== productId);

    if (authStore.isAuthenticated) {
      try {
        await cartService.remove(productId);
      } catch (err) {
        console.error('Erreur remove panier backend');
      }
    }
  }

  async function clearCart() {
    items.value = [];
    if (authStore.isAuthenticated) {
      try {
        // En supposant que le backend a un endpoint clear, sinon loop
        // Ici on loop par sécurité si pas d'endpoint /clear
        const response = await cartService.get();
        const currentItems = response.data.items || [];
        await Promise.all(currentItems.map(item => cartService.remove(item.product_id || item.id)));
      } catch (err) {
        console.error('Erreur clear panier backend');
      }
    }
  }

  return {
    items, loading, error,
    totalItems, subtotal, cartTotal, shippingFee,
    fetchCart, addToCart, updateQuantity, removeFromCart, clearCart
  };
}, {
  persist: true
});
