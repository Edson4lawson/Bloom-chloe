import axios from 'axios';

/**
 * Service API sécurisé avec gestion automatique des tokens
 * - Access Token: 15 minutes
 * - Refresh Token: 30 jours
 * - Renouvellement transparent des tokens expirés
 */

// URL de base de l'API - Configurée via les variables d'environnement
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost/api'; 

// Créer l'instance Axios
const api = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    timeout: 30000 // 30 secondes timeout
});

// Variable pour éviter les refresh multiples simultanés
let isRefreshing = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
    failedQueue.forEach(prom => {
        if (error) {
            prom.reject(error);
        } else {
            prom.resolve(token);
        }
    });
    failedQueue = [];
};

/**
 * Intercepteur de requête: Ajoute le token d'authentification
 */
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('access_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

/**
 * Intercepteur de réponse: Gère le renouvellement automatique des tokens
 */
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;
        
        // Si erreur 401 et qu'on n'a pas déjà tenté de refresh
        if (error.response?.status === 401 && !originalRequest._retry) {
            
            // Si c'est la requête de refresh qui échoue, déconnecter
            if (originalRequest.url?.includes('/auth/refresh.php')) {
                authService.forceLogout();
                return Promise.reject(error);
            }
            
            // Si un refresh est déjà en cours, mettre la requête en queue
            if (isRefreshing) {
                return new Promise((resolve, reject) => {
                    failedQueue.push({ resolve, reject });
                }).then(token => {
                    originalRequest.headers.Authorization = `Bearer ${token}`;
                    return api(originalRequest);
                }).catch(err => {
                    return Promise.reject(err);
                });
            }
            
            originalRequest._retry = true;
            isRefreshing = true;
            
            const refreshToken = localStorage.getItem('refresh_token');
            
            if (!refreshToken) {
                authService.forceLogout();
                return Promise.reject(error);
            }
            
            try {
                // Tenter de rafraîchir le token
                const response = await axios.post(`${API_URL}/auth/refresh.php`, {
                    refresh_token: refreshToken
                });
                
                const { access_token, refresh_token } = response.data;
                
                // Sauvegarder les nouveaux tokens
                localStorage.setItem('access_token', access_token);
                localStorage.setItem('refresh_token', refresh_token);
                
                // Mettre à jour le header de la requête originale
                originalRequest.headers.Authorization = `Bearer ${access_token}`;
                
                // Traiter les requêtes en queue
                processQueue(null, access_token);
                
                return api(originalRequest);
                
            } catch (refreshError) {
                processQueue(refreshError, null);
                authService.forceLogout();
                return Promise.reject(refreshError);
            } finally {
                isRefreshing = false;
            }
        }
        
        return Promise.reject(error);
    }
);

export const categoriesService = {
    getAll: () => api.get('/categories/get_all.php')
};

export const productsService = {
    getAll: (params) => api.get('/products/get_all.php', { params }),
    getOne: (id) => api.get(`/products/get_one.php?id=${id}`)
};

export const paymentService = {
    process: (data) => api.post('/payment/process.php', data)
};

// Service panier connecté au backend
export const cartService = {
    get: () => api.get('/cart/get.php'),
    add: (productId, quantity) => api.post('/cart/add.php', { product_id: productId, quantity }),
    update: (productId, quantity) => api.post('/cart/update.php', { product_id: productId, quantity }),
    remove: (productId) => api.post('/cart/remove.php', { product_id: productId })
};

// Service d'authentification sécurisé
export const authService = {
    /**
     * Connexion utilisateur
     */
    login: async (email, password) => {
        const response = await api.post('/auth/login.php', { email, password });
        
        if (response.data.access_token) {
            localStorage.setItem('access_token', response.data.access_token);
            localStorage.setItem('refresh_token', response.data.refresh_token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
        }
        
        return response;
    },
    
    /**
     * Inscription utilisateur
     */
    register: async (userData) => {
        const response = await api.post('/auth/register.php', userData);
        
        if (response.data.access_token) {
            localStorage.setItem('access_token', response.data.access_token);
            localStorage.setItem('refresh_token', response.data.refresh_token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
        }
        
        return response;
    },
    
    /**
     * Déconnexion
     */
    logout: async () => {
        try {
            const refreshToken = localStorage.getItem('refresh_token');
            await api.post('/auth/logout.php', { refresh_token: refreshToken });
        } catch (error) {
            console.error('Erreur lors de la déconnexion:', error);
        } finally {
            authService.forceLogout();
        }
    },
    
    /**
     * Déconnexion forcée (sans appel API)
     */
    forceLogout: () => {
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('user');
        // Optionnel: rediriger vers la page de connexion
        // window.location.href = '/login';
    },
    
    /**
     * Déconnexion de tous les appareils
     */
    logoutAll: async () => {
        try {
            const refreshToken = localStorage.getItem('refresh_token');
            await api.post('/auth/logout.php', { 
                refresh_token: refreshToken,
                logout_all: true 
            });
        } catch (error) {
            console.error('Erreur lors de la déconnexion:', error);
        } finally {
            authService.forceLogout();
        }
    },
    
    /**
     * Rafraîchir le token manuellement
     */
    refreshToken: async () => {
        const refreshToken = localStorage.getItem('refresh_token');
        if (!refreshToken) return null;
        
        try {
            const response = await axios.post(`${API_URL}/auth/refresh.php`, {
                refresh_token: refreshToken
            });
            
            localStorage.setItem('access_token', response.data.access_token);
            localStorage.setItem('refresh_token', response.data.refresh_token);
            
            return response.data.access_token;
        } catch (error) {
            authService.forceLogout();
            return null;
        }
    },
    
    /**
     * Vérifier si l'utilisateur est authentifié
     */
    isAuthenticated: () => !!localStorage.getItem('access_token'),
    
    /**
     * Récupérer les informations de l'utilisateur
     */
    getUser: () => {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },
    
    /**
     * Récupérer le token actuel
     */
    getToken: () => localStorage.getItem('access_token')
};

export default api;
