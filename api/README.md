# API Bloom Chloe E-commerce

Cette API fournit les fonctionnalités backend pour une application e-commerce Vue.js 3, incluant la gestion des utilisateurs, des produits, des paniers, des favoris, des commandes et des paiements.

## Configuration requise

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx) avec support PHP
- Extensions PHP requises : PDO, JSON, OpenSSL, mbstring

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone [URL_DU_REPO]
   cd Bloom-chloe/api
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL
   - Importer le fichier `database.sql` à la racine du projet
   - Configurer les paramètres de connexion dans `config/db.php`

3. **Configurer le serveur web**
   - Configurer le répertoire `api` comme racine du serveur web
   - Activer la réécriture d'URL (mod_rewrite pour Apache)
   - Configurer le fichier `.htaccess` si nécessaire

## Structure de l'API

```
api/
├── auth/
│   ├── login.php
│   └── register.php
├── cart/
│   ├── add.php
│   ├── get.php
│   ├── remove.php
│   └── update.php
├── categories/
├── config/
│   ├── db.php
│   └── headers.php
├── favorites/
│   ├── add.php
│   ├── get.php
│   └── remove.php
├── middleware/
│   └── auth.php
├── orders/
│   ├── create.php
│   └── get.php
└── payment/
    └── create.php
```

## Documentation des endpoints

### Authentification

#### Inscription
```http
POST /api/auth/register
Content-Type: application/json

{
  "email": "utilisateur@example.com",
  "password": "motdepasse123",
  "first_name": "Jean",
  "last_name": "Dupont",
  "address": "123 Rue de Paris",
  "phone": "+33123456789"
}
```

#### Connexion
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "utilisateur@example.com",
  "password": "motdepasse123"
}
```

### Produits

#### Lister les produits
```http
GET /api/products/get_all?page=1&per_page=10&category_id=1&search=robe
Authorization: Bearer VOTRE_TOKEN
```

#### Obtenir un produit
```http
GET /api/products/get_one?id=1
# ou
GET /api/products/get_one?slug=nom-du-produit
```

### Panier

#### Ajouter au panier
```http
POST /api/cart/add
Authorization: Bearer VOTRE_TOKEN
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2
}
```

#### Voir le panier
```http
GET /api/cart/get
Authorization: Bearer VOTRE_TOKEN
```

### Favoris

#### Ajouter aux favoris
```http
POST /api/favorites/add
Authorization: Bearer VOTRE_TOKEN
Content-Type: application/json

{
  "product_id": 1
}
```

### Commandes

#### Créer une commande
```http
POST /api/orders/create
Authorization: Bearer VOTRE_TOKEN
Content-Type: application/json

{
  "shipping_address": "123 Rue de Paris, 75001 Paris",
  "billing_address": "123 Rue de Paris, 75001 Paris",
  "payment_method": "credit_card",
  "customer_note": "Livraison à l'étage 2"
}
```

### Paiement

#### Traiter un paiement
```http
POST /api/payment/create
Authorization: Bearer VOTRE_TOKEN
Content-Type: application/json

{
  "order_id": 1,
  "payment_method": "credit_card",
  "card_number": "4242 4242 4242 4242",
  "card_expiry": "12/25",
  "card_cvc": "123",
  "card_name": "Jean Dupont"
}
```

## Exemple d'intégration avec Vue.js 3

### Configuration d'axios
```javascript
// src/utils/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Intercepteur pour ajouter le token d'authentification
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
```

### Exemple de service d'authentification
```javascript
// src/services/auth.js
import api from '@/utils/api';

export const authService = {
  async login(email, password) {
    try {
      const response = await api.post('/auth/login', { email, password });
      localStorage.setItem('auth_token', response.data.token);
      return response.data.user;
    } catch (error) {
      throw new Error(error.response?.data?.error || 'Erreur de connexion');
    }
  },

  async register(userData) {
    try {
      const response = await api.post('/auth/register', userData);
      localStorage.setItem('auth_token', response.data.token);
      return response.data.user;
    } catch (error) {
      throw new Error(error.response?.data?.error || "Erreur lors de l'inscription");
    }
  },

  logout() {
    localStorage.removeItem('auth_token');
  },

  isAuthenticated() {
    return !!localStorage.getItem('auth_token');
  }
};
```

### Exemple de composant de connexion
```vue
<template>
  <form @submit.prevent="handleSubmit">
    <input v-model="email" type="email" placeholder="Email" required>
    <input v-model="password" type="password" placeholder="Mot de passe" required>
    <button type="submit" :disabled="loading">
      {{ loading ? 'Chargement...' : 'Se connecter' }}
    </button>
    <div v-if="error" class="error">{{ error }}</div>
  </form>
</template>

<script>
import { ref } from 'vue';
import { authService } from '@/services/auth';

export default {
  setup() {
    const email = ref('');
    const password = ref('');
    const loading = ref(false);
    const error = ref('');

    const handleSubmit = async () => {
      try {
        loading.value = true;
        error.value = '';
        await authService.login(email.value, password.value);
        // Rediriger vers la page d'accueil ou le tableau de bord
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    return { email, password, loading, error, handleSubmit };
  }
};
</script>
```

## Sécurité

- Tous les mots de passe sont hachés avec `password_hash`
- Les tokens JWT sont utilisés pour l'authentification
- Protection contre les attaques CSRF
- Validation des entrées utilisateur
- Gestion des erreurs sécurisée

## Déploiement

1. Configurer un serveur web (Apache/Nginx) avec PHP
2. Configurer une base de données MySQL
3. Configurer les variables d'environnement dans `config/db.php`
4. Activer le module de réécriture d'URL
5. Déployer les fichiers sur le serveur

## Licence

MIT
