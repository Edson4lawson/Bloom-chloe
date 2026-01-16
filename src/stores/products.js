import { defineStore } from 'pinia';
import { ref } from 'vue';

// Helper to get image URL dynamically
const modules = import.meta.glob('../assets/*.jpg', { eager: true });
const getImageUrl = (name) => {
  // Try exact match first
  let path = `../assets/${name}`;
  if (modules[path]) return modules[path].default || modules[path];
  
  // Try finding it in the list if keys are slightly different
  const key = Object.keys(modules).find(k => k.includes(name));
  return key ? (modules[key].default || modules[key]) : '';
};

export const useProductStore = defineStore('products', () => {
    const products = ref([]);
    const loading = ref(false);

    // Initial Static Data (Translated & Expanded)
    const staticProducts = [
        // Existing products translated/refined
        {
          id: 1,
          title: 'Fleur de Cerisier',
          category: 'Eau de Parfum',
          price: 89.99,
          rating: 4.8,
          stock: 15,
          thumbnail: getImageUrl('image1.jpg'),
          description: 'Un parfum délicat aux notes florales de cerisier en fleur, parfait pour une touche printanière et romantique.'
        },
        {
          id: 2,
          title: 'Fraîcheur Absolue',
          category: 'Eau de Cologne',
          price: 65.50,
          rating: 4.5,
          stock: 22,
          thumbnail: getImageUrl('image2.jpg'),
          description: 'Une fraîcheur incomparable avec des notes d\'agrumes pétillants et de menthe poivrée vivifiante.'
        },
        {
          id: 3,
          title: 'Santal Royal',
          category: 'Extrait de Parfum',
          price: 120.00,
          rating: 4.9,
          stock: 8,
          thumbnail: getImageUrl('image3.jpg'),
          description: 'Un sillage chaleureux et enveloppant, mêlant la richesse du bois de santal à une vanille bourbon onctueuse.'
        },
        {
            id: 4,
            title: 'Brume d\'Été',
            category: 'Eau de Toilette',
            price: 75.00,
            rating: 4.3,
            stock: 30,
            thumbnail: getImageUrl('image4.jpg'),
            description: 'Une eau légère comme une caresse, évoquant les matins ensoleillés et la rosée fraîche.'
        },
        {
            id: 5,
            title: 'Orient Mystique',
            category: 'Eau de Parfum',
            price: 95.00,
            rating: 4.7,
            stock: 12,
            thumbnail: getImageUrl('image5.jpg'),
            description: 'Un parfum envoûtant et profond aux notes d\'ambre gris, d\'épices rares et d\'un soupçon de mystère.'
        },
        {
            id: 6,
            title: 'Zeste Citronné',
            category: 'Eau de Cologne',
            price: 55.00,
            rating: 4.2,
            stock: 25,
            thumbnail: getImageUrl('image6.jpg'),
            description: 'Une explosion vitaminée de citron et de pamplemousse pour une énergie débordante toute la journée.'
        },
        {
            id: 7,
            title: 'Rose Éternelle',
            category: 'Eau de Parfum',
            price: 85.00,
            rating: 4.6,
            stock: 18,
            thumbnail: getImageUrl('image7.jpg'),
            description: 'L\'élégance intemporelle d\'un bouquet de roses de Grasse, sublimée par une touche de jasmin blanc.'
        },
        // Nouveaux produits (Images WhatsApp / Accessoires de Luxe)
        {
            id: 8,
            title: 'Élixir Nocturne',
            category: 'Sac de Soirée',
            price: 145.00,
            rating: 5.0,
            stock: 5,
            thumbnail: getImageUrl('parfum-luxe-01.jpg'),
            description: 'Un accessoire indispensable pour vos soirées, alliant élégance et fonctionnalité avec une finition impeccable.'
        },
        {
            id: 9,
            title: 'Or Blanc',
            category: 'Montre Prestige',
            price: 115.00,
            rating: 4.8,
            stock: 10,
            thumbnail: getImageUrl('parfum-luxe-02.jpg'),
            description: 'L\'incarnation du luxe minimaliste. Un design épuré qui capture la lumière et attire tous les regards.'
        },
        {
            id: 10,
            title: 'Prestige Gold',
            category: 'Pochette Luxe',
            price: 180.00,
            rating: 4.9,
            stock: 3,
            thumbnail: getImageUrl('parfum-luxe-03.jpg'),
            description: 'L\'ultime raffinement. Une pièce maîtresse ornée de détails dorés pour une allure majestueuse.'
        },
        {
            id: 11,
            title: 'Jardin Secret',
            category: 'Bracelet Fin',
            price: 78.00,
            rating: 4.4,
            stock: 20,
            thumbnail: getImageUrl('parfum-luxe-04.jpg'),
            description: 'Un bijou délicat inspiré par la nature, parfait pour ajouter une touche de romantisme à votre tenue.'
        },
        {
            id: 12,
            title: 'Velours Noir',
            category: 'Sac à Main',
            price: 130.00,
            rating: 4.7,
            stock: 7,
            thumbnail: getImageUrl('parfum-luxe-05.jpg'),
            description: 'Doux au toucher et structuré. Ce sac offre une contenance idéale et un style résolument moderne.'
        },
        {
            id: 13,
            title: 'Essence Divine',
            category: 'Collier',
            price: 105.00,
            rating: 4.6,
            stock: 14,
            thumbnail: getImageUrl('parfum-luxe-06.jpg'),
            description: 'Une pièce rayonnante qui illumine le visage. Conçue pour celles qui cherchent à se démarquer.'
        },
        {
            id: 14,
            title: 'Charisme',
            category: 'Lunettes de Soleil',
            price: 98.00,
            rating: 4.5,
            stock: 16,
            thumbnail: getImageUrl('parfum-luxe-07.jpg'),
            description: 'Pour celles qui osent. Une monture audacieuse qui affirme votre personnalité avec style.'
        },
        {
            id: 15,
            title: 'Douce Rêverie',
            category: 'Foulard Soie',
            price: 60.00,
            rating: 4.3,
            stock: 28,
            thumbnail: getImageUrl('parfum-luxe-08.jpg'),
            description: 'Légèreté et douceur. Un accessoire versatile à porter autour du cou ou accroché à votre sac.'
        }
    ];

    const fetchProducts = async () => {
        // En développement, on recharge toujours pour voir les changements
        // if(products.value.length > 0) return; 
        
        loading.value = true;
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Enrich data
        products.value = staticProducts.map(product => ({
            ...product,
            discount: Math.random() > 0.8 ? Math.floor(Math.random() * 20) + 10 : 0,
        }));
        loading.value = false;
    };

    const getFeaturedProduct = () => {
        if(products.value.length === 0) return staticProducts[9]; // Prestige Gold usually
        // Return random product or best rated
        return products.value.find(p => p.id === 10) || products.value[0]; 
    };

    return {
        products,
        loading,
        fetchProducts,
        getFeaturedProduct
    };
}, {
  persist: true
});
