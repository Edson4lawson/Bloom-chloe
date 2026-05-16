import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { productsService } from "@/services/api";

export const useProductStore = defineStore(
  "products",
  () => {
    const products = ref([]);
    const loading = ref(false);
    const error = ref(null);

    // Getters
    const productsBySource = computed(() => (source) =>
      products.value.filter((p) => p.source === source)
    );

    const mainProducts = computed(() =>
      products.value.filter((p) => p.source === "produit")
    );

    const storeProducts = computed(() =>
      products.value.filter((p) => p.source === "store")
    );

    /**
     * Récupère tous les produits depuis l'API backend
     * Charge tous les produits en une seule requête (per_page=200)
     */
    const fetchProducts = async () => {
      // Ne pas recharger si on a déjà assez de produits (évite les appels inutiles)
      if (products.value.length > 10) return;

      loading.value = true;
      error.value = null;

      try {
        const response = await productsService.getAll({
          per_page: 200,
          sort_by: "created_at",
          sort_order: "ASC",
        });

        const data = response.data;

        // L'API retourne { data: [...], pagination: {...} }
        const rawProducts = data.data || data;

        // Mapper les champs de la BDD vers le format attendu par le frontend
        products.value = rawProducts.map((p) => ({
          id: p.id,
          product_id: p.id,
          title: p.name,
          name: p.name,
          category: p.category_name || p.category || "",
          price: parseFloat(p.price),
          rating: parseFloat(p.rating) || 4.5,
          stock: parseInt(p.stock) || parseInt(p.stock_quantity) || 0,
          thumbnail: p.image_url
            ? (p.image_url.startsWith("http") ? p.image_url : `/src/assets/${p.image_url}`)
            : "/placeholder-product.jpg",
          image: p.image_url || "/placeholder-product.jpg",
          source: p.source || "produit",
          description: p.description || "",
          slug: p.slug || "",
          status: p.status || "published",
          // Discount aléatoire (comme avant)
          discount:
            Math.random() > 0.8
              ? Math.floor(Math.random() * 20) + 10
              : 0,
        }));
      } catch (err) {
        console.error("Erreur lors du chargement des produits:", err);
        error.value =
          err.response?.data?.error ||
          "Impossible de charger les produits";
        // Garder les produits déjà en cache via persist
      } finally {
        loading.value = false;
      }
    };

    /**
     * Force le rechargement des produits
     */
    const refreshProducts = async () => {
      products.value = [];
      await fetchProducts();
    };

    /**
     * Récupère le produit phare (featured)
     */
    const getFeaturedProduct = () => {
      if (products.value.length === 0) return null;
      // Retourner le produit id=10 (Moulinex Golden-Crown) ou le premier
      return (
        products.value.find((p) => p.id === 10 || p.id === "10") ||
        products.value[0]
      );
    };

    /**
     * Récupère un produit par son ID
     */
    const getProductById = (id) => {
      return products.value.find(
        (p) => p.id === id || p.id === parseInt(id)
      );
    };

    /**
     * Recherche de produits par texte
     */
    const searchProducts = (query) => {
      if (!query || query.trim().length < 2) return [];
      const q = query.toLowerCase().trim();
      return products.value.filter(
        (p) =>
          (p.title || p.name || "").toLowerCase().includes(q) ||
          (p.description || "").toLowerCase().includes(q) ||
          (p.category || "").toLowerCase().includes(q)
      );
    };

    return {
      products,
      loading,
      error,
      mainProducts,
      storeProducts,
      productsBySource,
      fetchProducts,
      refreshProducts,
      getFeaturedProduct,
      getProductById,
      searchProducts,
    };
  },
  {
    persist: true,
  }
);
