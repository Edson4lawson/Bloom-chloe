# Guide de Déploiement : Neon (Database) + Render (Backend) + Vercel (Frontend)

Ce guide détaille les étapes exactes pour déployer l'application **Bloom Chloé** en production.

---

## 1. 🐘 Base de Données sur Neon (PostgreSQL)

1. Rendez-vous sur [https://console.neon.tech](https://console.neon.tech) et créez un projet nommé `bloom-chloe`.
2. Ouvrez l'onglet **SQL Editor** dans votre console Neon.
3. Copiez l'intégralité du script SQL contenu dans [`database/sql/neon_schema.sql`](file:///c:/laragon/www/Bloom-chloe/database/sql/neon_schema.sql) et exécutez-le.
   - Ce script crée toutes les tables (`users`, `roles`, `categories`, `products`, `orders`, `order_items`, `payments`, `cart`, `favorites`, etc.), les index et le compte administrateur initial.
4. Dans le tableau de bord Neon, copiez votre chaîne de connexion **Connection string** (PostgreSQL) :
   ```text
   postgres://neondb_owner:VOTRE_MOT_DE_PASSE@ep-xyz.eu-central-1.aws.neon.tech/neondb?sslmode=require
   ```

---

## 2. 🚀 Backend sur Render (API PHP / Docker)

1. Connectez-vous sur [https://dashboard.render.com](https://dashboard.render.com).
2. Cliquez sur **New +** -> **Web Service**.
3. Liez votre dépôt GitHub `Bloom-chloe`.
4. Configurez le service :
   - **Name :** `bloom-chloe-api`
   - **Environment :** `Docker`
   - **Dockerfile Path :** `./Dockerfile`
   - **Instance Type :** `Free`
5. Dans la section **Environment Variables**, ajoutez les clés suivantes :
   - `DATABASE_URL` = `<Votre chaîne de connexion Neon copiée à l'étape 1>`
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `false`
   - `FRONTEND_URL` = `https://bloom-chloe.vercel.app`
   - `API_URL` = `https://bloom-chloe-api.onrender.com`
   - `ALLOWED_ORIGINS` = `https://bloom-chloe.vercel.app,https://www.bloom-chloe.com`
   - `JWT_SECRET` = `<Générer une chaîne aléatoire de 64 caractères>`
   - `PAYMENT_SECRET_KEY` = `<Générer une clé secrète>`
6. Cliquez sur **Create Web Service**.
   - Render va construire l'image Docker et démarrer le serveur API PHP sur `https://bloom-chloe-api.onrender.com`.

---

## 3. ⚡ Frontend sur Vercel (Vue 3 / Vite)

1. Connectez-vous sur [https://vercel.com](https://vercel.com).
2. Cliquez sur **Add New...** -> **Project** et importez le dépôt `Bloom-chloe`.
3. Configurez les options du build :
   - **Framework Preset :** `Vite`
   - **Build Command :** `npm run build`
   - **Output Directory :** `dist`
   - **Install Command :** `npm install`
4. Dans la section **Environment Variables** sur Vercel :
   - `VITE_API_URL` = `https://bloom-chloe-api.onrender.com`
5. Cliquez sur **Deploy**.
   - Vercel va compiler le frontend et vous fournir l'URL publique `https://bloom-chloe.vercel.app`.

---

## 4. 🔑 Compte Administrateur Initial

Une fois le déploiement terminé :
- **URL Admin :** `https://bloom-chloe.vercel.app/admin/login`
- **Email :** `admin@bloom-chloe.com`
- **Mot de passe initial :** `AdminBloom2026!`
*(Pensez à modifier le mot de passe depuis le dashboard après la première connexion).*
