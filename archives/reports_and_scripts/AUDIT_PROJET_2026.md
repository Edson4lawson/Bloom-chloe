# 🔍 Audit Complet - Projet Bloom-Chloé

## 📋 Résumé Exécutif
Le projet **Bloom-Chloé** est une plateforme e-commerce moderne (Vue 3 + Vite + Tailwind v4) avec un backend PHP. Bien que le design soit premium et les fonctionnalités de base présentes, la structure interne souffre d'une forte fragmentation et d'une dette technique accumulée lors de phases de débogage successives.

---

## 🏗️ Architecture & Structure du Projet

### 1. Fragmentation de l'API
**Problème Majeur :** La plupart des fichiers dans le dossier `api/` (ex: `products/get_all.php`) ignorent les fichiers de configuration centraux (`api/config/db.php` et `api/config/headers.php`).
- **Risque :** Si les identifiants de base de données changent, il faut modifier des dizaines de fichiers.
- **Preuve :** 
  ```php
  // Dans api/products/get_all.php (Ligne 27)
  $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
  ```
- **Recommandation :** Utiliser `require_once __DIR__ . '/../config/db.php';` et `require_once __DIR__ . '/../config/headers.php';` dans tous les points d'entrée de l'API.

### 2. Redondance des Fichiers
Le projet contient de nombreuses versions des mêmes scripts :
- `get_all.php`, `get_all_new.php`, `get_all_simple.php`, `get_all_fixed.php`.
- Dossiers `_abandoned_api`, `_abandoned_src`, `_old_frontend_attempt`.
- **Recommandation :** Nettoyer les fichiers obsolètes après avoir validé la version stable.

### 3. Structure Node/Vite
- Le `package.json` est à la racine, mais l'utilisateur travaille dans le dossier `frontend/`.
- Les alias Vite pointent vers `./src` (racine) alors que le code source est dans `./frontend/src`.
- **Incohérence des variables d'environnement :**
  - Racine `.env` : `VITE_API_URL=http://localhost:8001`
  - Frontend `.env` : `VITE_API_URL=http://localhost:8080`
- **Recommandation :** Unifier la structure. Soit tout mettre à la racine, soit déplacer `package.json` dans `frontend/`.

---

## 🎨 Design & Frontend

### Points Forts ✅
- **Esthétique Premium :** Utilisation de gradients mesh, animations GSAP/AOS, et transitions de pages fluides.
- **Performance :** Lazy loading des composants (`defineAsyncComponent`) et utilisation de `Suspense`.
- **SEO :** Implémentation correcte des meta-tags via un composable `useSEO`.

### Points Faibles ⚠️
- **Système de Style :** Utilisation de Tailwind v4 (très récent). S'assurer que tous les plugins sont compatibles.
- **Injection de Styles :** Le fichier `Home.vue` injecte des styles CSS via JS au montage. Il serait préférable de les mettre dans un fichier CSS global ou un bloc `<style>`.

---

## 🔒 Sécurité

### 1. Gestion des Secrets
- La `JWT_SECRET` est présente en clair dans `api/.env`. C'est acceptable en développement local, mais critique pour la production.
- **Recommandation :** Ajouter `.env` au `.gitignore` (déjà fait, mais vérifier qu'aucune copie n'est commitée).

### 2. CORS
- Certains fichiers utilisent `header("Access-Control-Allow-Origin: *");` (ex: `get_all.php`).
- D'autres utilisent une whitelist (ex: `headers.php`).
- **Recommandation :** Utiliser exclusivement la whitelist de `headers.php`.

### 3. Validation des Données
- Le backend utilise des requêtes préparées (`PDO::prepare`), ce qui est une excellente pratique contre les injections SQL.

---

## 🛠️ Plan d'Action Recommandé

| Phase | Action | Priorité |
| :--- | :--- | :--- |
| **1. Unification** | Refactoriser les fichiers API pour utiliser `config/db.php`. | 🔴 Critique |
| **2. Nettoyage** | Supprimer les fichiers `_new`, `_fixed` et dossiers `_abandoned`. | 🟡 Importante |
| **3. Config** | Harmoniser les fichiers `.env` et les ports de l'API. | 🟠 Haute |
| **4. Déploiement** | Préparer un script de build propre pour le frontend. | 🟢 Faible |

---

> [!IMPORTANT]
> Le projet est fonctionnel mais sa maintenance deviendra cauchemardesque sans une unification immédiate de la gestion de la base de données et des headers.
