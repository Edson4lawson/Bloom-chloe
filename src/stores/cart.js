import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { cartService } from '@/services/api';

export const useCartStore = defineStore('cart', () => {
  const items = ref([]);
  const loading = ref(false);
  const error = ref(null);
  
  // Getters
  const totalItems = computed(() => 
    items.value.reduce((total, item) => total + item.quantity, 0)
  );
  
  const subtotal = computed(() => 
    items.value.reduce((total, item) => 
      total + (parseFloat(item.price) * item.quantity), 0
    ).toFixed(2)
  );
  
  const cartTotal = computed(() => {
    if (!items.value.length) return '0.00';
    const shipping = 5.99;
    const taxRate = 0.20;
    const tax = parseFloat(subtotal.value) * taxRate;
    return (parseFloat(subtotal.value) + shipping + tax).toFixed(2);
  });
  
  const shippingFee = computed(() => 
    items.value.length > 0 ? '5.99' : '0.00'
  );
  
  const taxAmount = computed(() => 
    (parseFloat(subtotal.value) * 0.20).toFixed(2)
  );

  // Actions
  async function fetchCart() {
    loading.value = true;
    error.value = null;
    try {
      const response = await cartService.get();
      // Sync from server ONLY if server has data
      if (response.data?.items?.length > 0) {
        items.value = response.data.items;
      }
    } catch (err) {
      console.warn('Cart API sync failed, using local persisted state.');
    } finally {
      loading.value = false;
    }
  }
  
  async function addToCart(product, quantity = 1) {
    loading.value = true;
    error.value = null;
    
    // Optimistic Update Locally
    const productId = product.product_id || product.id;
    const existing = items.value.find(i => (i.product_id || i.id) === productId);
    
    if (existing) {
      existing.quantity += quantity;
    } else {
      items.value.push({
        id: productId,
        product_id: productId,
        name: product.title || product.name,
        price: parseFloat(product.price),
        image: product.thumbnail || product.image || '/placeholder-perfume.jpg',
        quantity: quantity
      });
    }

    try {
      await cartService.add(productId, quantity);
    } catch (err) {
      console.warn('Backend add failed, kept in local cart.');
    } finally {
      loading.value = false;
    }
  }
  
  async function updateCartItem(productId, quantity) {
    const item = items.value.find(i => (i.product_id || i.id) === productId);
    if (item) {
      if (quantity <= 0) {
        await removeFromCart(productId);
        return;
      }
      item.quantity = quantity;
    }

    try {
      await cartService.update(productId, quantity);
    } catch (err) {
      console.warn('Backend update failed.');
    }
  }

  async function removeFromCart(productId) {
    items.value = items.value.filter(i => (i.product_id || i.id) !== productId);
    try {
      await cartService.remove(productId);
    } catch (err) {
      console.warn('Backend removal failed.');
    }
  }

  async function clearCart() {
    items.value = [];
    try {
      await cartService.get().then(res => {
         const ids = (res.data.items || []).map(item => item.product_id || item.id);
         return Promise.all(ids.map(id => cartService.remove(id)));
      });
    } catch (err) {
      console.warn('Backend clear failed.');
    }
  }

  return {
    items, loading, error,
    totalItems, subtotal, cartTotal, shippingFee, taxAmount,
    fetchCart, addToCart, updateCartItem, removeFromCart, clearCart,
    updateQuantity: updateCartItem
  };
}, {
  persist: true
});
