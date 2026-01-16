import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useWishlistStore = defineStore('wishlist', () => {
  const items = ref([]);
  
  // Getters
  const totalItems = computed(() => items.value.length);
  const isEmpty = computed(() => items.value.length === 0);

  // Actions
  function addToWishlist(product) {
    // Avoid duplicates
    if (!isInWishlist(product.product_id || product.id)) {
        items.value.push({
            id: product.id,
            product_id: product.id,
            name: product.title || product.name,
            price: parseFloat(product.price),
            image: product.thumbnail || product.image || '/placeholder-perfume.jpg',
            category: product.category || 'Parfum'
        });
    }
  }

  function removeFromWishlist(productId) {
    items.value = items.value.filter(item => (item.id || item.product_id) !== productId);
  }

  function toggleWishlist(product) {
      if (isInWishlist(product.id)) {
          removeFromWishlist(product.id);
          return false; // Removed
      } else {
          addToWishlist(product);
          return true; // Added
      }
  }
  
  function isInWishlist(productId) {
    return items.value.some(item => (item.id || item.product_id) === productId);
  }

  function clearWishlist() {
    items.value = [];
  }

  return {
    items,
    totalItems,
    isEmpty,
    addToWishlist,
    removeFromWishlist,
    toggleWishlist,
    isInWishlist,
    clearWishlist
  };
}, {
  persist: true
});
