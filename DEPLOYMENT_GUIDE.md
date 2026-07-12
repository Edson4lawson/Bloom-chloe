# GUIDE DE DÉPLOIEMENT PRODUCTION - BLOOM-CHLOÉ

Ce guide explique comment déployer Bloom Chloé en production sur Render (backend PHP) et Vercel (frontend Vue).

---

## 📋 TABLE DES MATIÈRES

1. [Architecture de déploiement](#architecture)
2. [Prérequis](#prérequis)
3. [Étape 1: Configuration Render (Backend PHP)](#render)
4. [Étape 2: Configuration Vercel (Frontend Vue)](#vercel)
5. [Étape 3: Configuration Base de Données PostgreSQL](#database)
6. [Étape 4: Variables d'Environnement Production](#env)
7. [Étape 5: Services Externes](#services)
8. [Étape 6: DNS et Domaines](#dns)
9. [Étape 7: Sauvegardes et Monitoring](#monitoring)
10. [Coûts Estimés](#costs)

---

## 🏗️ ARCHITECTURE DE DÉPLOIEMENT

```
┌─────────────────┐         ┌─────────────────┐
│   Frontend      │         │    Backend      │
│   (Vercel)      │◄────────►│    (Render)     │
│   Vue.js        │  HTTPS   │   PHP 8.2       │
│   Port 443      │         │   Port 443      │
└─────────────────┘         └────────┬────────┘
                                      │
                                      ▼
                              ┌─────────────────┐
                              │  PostgreSQL     │
                              │  (Render)       │
                              │  Port 5432      │
                              └─────────────────┘
```

---

## 📦 PRÉREQUIS

- Compte Render (https://render.com) - Gratuit pour démarrer
- Compte Vercel (https://vercel.com) - Gratuit
- Compte GitHub (pour le dépôt de code)
- Domaine personnalisé (optionnel, recommandé pour la production)

---

## 🚀 ÉTAPE 1: CONFIGURATION RENDER (BACKEND PHP)

### 1.1 Créer un compte Render

1. Allez sur https://render.com
2. Créez un compte avec GitHub
3. Connectez votre compte GitHub

### 1.2 Créer une Base de Données PostgreSQL

1. Dans Render, cliquez sur **"New +"** → **"PostgreSQL"**
2. Configurez :
   - **Name**: `bloom-chloe-db`
   - **Database**: `bloom_chloe`
   - **User**: `bloom_chloe_user`
   - **Region**: Choisissez la région la plus proche de vos utilisateurs (ex: Frankfurt pour l'Europe)
   - **Plan**: Free (pour démarrer) ou Standard ($7/mois pour la production)
3. Cliquez sur **"Create Database"**
4. **IMPORTANT**: Notez les informations de connexion :
   - Internal Database URL
   - Database Name
   - Database User
   - Database Password

### 1.3 Créer le Service Web PHP

1. Dans Render, cliquez sur **"New +"** → **"Web Service"**
2. Connectez votre dépôt GitHub Bloom-Chloé
3. Configurez :
   - **Name**: `bloom-chloe-api`
   - **Region**: Même région que la base de données
   - **Branch**: `main`
   - **Root Directory**: `backend`
   - **Runtime**: `Docker`
   - **Dockerfile Path**: `Dockerfile`
4. **Environment Variables** (voir section 4)
5. Cliquez sur **"Create Web Service"**

### 1.4 Configurer le Dockerfile

Assurez-vous que le `Dockerfile` à la racine du projet est configuré correctement :

```dockerfile
FROM php:8.2-fpm-alpine

# Installer les extensions PHP nécessaires
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    oniguruma-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd pdo pdo_mysql zip mbstring opcache

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers
COPY backend/ /var/www/html/
COPY .env.production /var/www/html/.env

# Permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
```

---

## 🎨 ÉTAPE 2: CONFIGURATION VERCEL (FRONTEND VUE)

### 2.1 Créer un compte Vercel

1. Allez sur https://vercel.com
2. Créez un compte avec GitHub
3. Connectez votre compte GitHub

### 2.2 Déployer le Frontend

1. Dans Vercel, cliquez sur **"Add New..."** → **"Project"**
2. Sélectionnez votre dépôt GitHub Bloom-Chloé
3. Configurez :
   - **Framework Preset**: Vue.js
   - **Root Directory**: `frontend`
   - **Build Command**: `npm run build`
   - **Output Directory**: `dist`
4. **Environment Variables** (voir section 4)
5. Cliquez sur **"Deploy"**

### 2.3 Configurer vercel.json

Créez le fichier `frontend/vercel.json` :

```json
{
  "buildCommand": "npm run build",
  "outputDirectory": "dist",
  "devCommand": "npm run dev",
  "installCommand": "npm install",
  "framework": "vite",
  "rewrites": [
    {
      "source": "/(.*)",
      "destination": "/index.html"
    }
  ],
  "headers": [
    {
      "source": "/(.*)",
      "headers": [
        {
          "key": "X-Content-Type-Options",
          "value": "nosniff"
        },
        {
          "key": "X-Frame-Options",
          "value": "DENY"
        },
        {
          "key": "X-XSS-Protection",
          "value": "1; mode=block"
        },
        {
          "key": "Referrer-Policy",
          "value": "strict-origin-when-cross-origin"
        }
      ]
    }
  ]
}
```

---

## 🗄️ ÉTAPE 3: CONFIGURATION BASE DE DONNÉES POSTGRESQL

### 3.1 Exécuter les Migrations

Une fois la base de données créée sur Render :

1. Ouvrez votre base de données PostgreSQL sur Render
2. Cliquez sur **"Connect"** → **"External Connection"**
3. Copiez la **Internal Database URL**
4. Exécutez les migrations localement :

```powershell
# Installer les dépendances PHP
composer require pgsql

# Exécuter les migrations
php backend/scripts/migrate_postgres.php
```

### 3.2 Créer le Script de Migration PostgreSQL

Créez `backend/scripts/migrate_postgres.php` :

```php
<?php
require_once __DIR__ . '/../config/db.php';

try {
    $pdo->beginTransaction();
    
    // Exécuter les fichiers de migration
    $migrations = [
        '001_init_tables.txt',
        '002_2fa_tables.txt',
        '003_rbac_permissions.txt'
    ];
    
    foreach ($migrations as $migration) {
        $file = __DIR__ . '/../../database/migrations/' . $migration;
        if (file_exists($file)) {
            $sql = file_get_contents($file);
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            
            echo "Migration $migration exécutée avec succès\n";
        }
    }
    
    $pdo->commit();
    echo "Toutes les migrations ont été exécutées avec succès\n";
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Erreur lors des migrations: " . $e->getMessage() . "\n";
    exit(1);
}
```

---

## 🔐 ÉTAPE 4: VARIABLES D'ENVIRONNEMENT PRODUCTION

### 4.1 Générer les Clés de Sécurité

```powershell
# Générer JWT_SECRET
php -r "echo bin2hex(random_bytes(32));"

# Générer ENCRYPTION_KEY
php -r "echo bin2hex(random_bytes(32));"

# Générer PAYMENT_SECRET_KEY
php -r "echo bin2hex(random_bytes(32));"
```

### 4.2 Variables d'Environnement Render (Backend)

Dans Render → Web Service → Environment Variables :

```bash
# Environnement
APP_ENV=production
APP_DEBUG=false

# Base de données PostgreSQL
DATABASE_URL=postgresql://user:password@host:5432/bloom_chloe
DB_HOST=your-db-host.render.com
DB_PORT=5432
DB_NAME=bloom_chloe
DB_USER=your_db_user
DB_PASSWORD=your_db_password

# Sécurité
JWT_SECRET=votre_jwt_secret_64_caracteres
ENCRYPTION_KEY=votre_encryption_key_64_caracteres
PAYMENT_SECRET_KEY=votre_payment_key_64_caracteres

# API & Frontend
FRONTEND_URL=https://bloom-chloe.vercel.app
API_URL=https://bloom-chloe-api.onrender.com
ALLOWED_ORIGINS=https://bloom-chloe.vercel.app,https://www.bloom-chloe.com,https://bloom-chloe.com

# Sessions
SESSION_LIFETIME=900
COOKIE_SECURE=true
COOKIE_HTTPONLY=true
COOKIE_SAMESITE=strict

# Rate Limiting
RATE_LIMIT_ENABLED=true
RATE_LIMIT_LOGIN_ATTEMPTS=5
RATE_LIMIT_LOGIN_WINDOW=300
RATE_LIMIT_API_REQUESTS=100
RATE_LIMIT_API_WINDOW=60
```

### 4.3 Variables d'Environnement Vercel (Frontend)

Dans Vercel → Project Settings → Environment Variables :

```bash
VITE_API_URL=https://bloom-chloe-api.onrender.com
VITE_STRIPE_PUBLIC_KEY=pk_live_votre_cle_stripe
VITE_RECAPTCHA_SITE_KEY=votre_cle_recaptcha
```

---

## 🌐 ÉTAPE 5: SERVICES EXTERNES

### 5.1 Stripe (Paiements)

1. Allez sur https://dashboard.stripe.com
2. Créez un compte et vérifiez votre entreprise
3. Obtenez les clés **Live** (pas Test) :
   - Publishable Key: `pk_live_...`
   - Secret Key: `sk_live_...`
4. Configurez le Webhook :
   - URL: `https://bloom-chloe-api.onrender.com/payment/stripe-webhook`
   - Events: `payment_intent.succeeded`, `payment_intent.failed`
5. Ajoutez dans Render Environment Variables :
   ```bash
   STRIPE_PUBLIC_KEY=pk_live_...
   STRIPE_SECRET_KEY=sk_live_...
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```

### 5.2 SendGrid (Emails)

1. Allez sur https://signup.sendgrid.com
2. Créez un compte et vérifiez votre domaine
3. Créez un API Key avec accès "Mail Send"
4. Configurez Sender Authentication (SPF/DKIM)
5. Ajoutez dans Render Environment Variables :
   ```bash
   SENDGRID_API_KEY=SG.votre_cle
   SENDGRID_FROM_EMAIL=noreply@bloom-chloe.com
   SENDGRID_FROM_NAME=Bloom Chloé
   ```

### 5.3 Cloudinary (Images)

1. Allez sur https://cloudinary.com
2. Créez un compte
3. Obtenez les clés API
4. Créez un Upload Preset unsigned pour les uploads
5. Ajoutez dans Render Environment Variables :
   ```bash
   CLOUDINARY_CLOUD_NAME=votre_cloud_name
   CLOUDINARY_API_KEY=votre_api_key
   CLOUDINARY_API_SECRET=votre_api_secret
   CLOUDINARY_UPLOAD_PRESET=votre_preset
   ```

### 5.4 reCAPTCHA v3 (Google)

1. Allez sur https://www.google.com/recaptcha/admin
2. Créez un compte reCAPTCHA v3
3. Ajoutez vos domaines :
   - bloom-chloe.vercel.app
   - bloom-chloe-api.onrender.com
   - bloom-chloe.com (si vous avez un domaine)
4. Ajoutez dans Render et Vercel :
   ```bash
   # Render (Backend)
   RECAPTCHA_SECRET_KEY=votre_cle_secrete
   
   # Vercel (Frontend)
   VITE_RECAPTCHA_SITE_KEY=votre_cle_site
   ```

---

## 🌍 ÉTAPE 6: DNS ET DOMAINES

### 6.1 Configurer le Domaine Backend (Render)

1. Dans Render → Web Service → Settings → Custom Domains
2. Cliquez sur **"Add Custom Domain"**
3. Entrez: `api.bloom-chloe.com`
4. Suivez les instructions pour configurer le DNS :
   - Type: CNAME
   - Name: api
   - Value: your-service-name.onrender.com

### 6.2 Configurer le Domaine Frontend (Vercel)

1. Dans Vercel → Project Settings → Domains
2. Cliquez sur **"Add Domain"**
3. Entrez: `bloom-chloe.com`
4. Suivez les instructions pour configurer le DNS :
   - Type: A
   - Name: @
   - Value: 76.76.21.21
   - Type: CNAME
   - Name: www
   - Value: cname.vercel-dns.com

### 6.3 Configurer SPF/DKIM/DMARC (Emails)

Voir le fichier `SPF_DKIM_DMARC_CONFIG.md` pour les instructions détaillées.

---

## 💾 ÉTAPE 7: SAUVEGARDES ET MONITORING

### 7.1 Sauvegardes Automatiques (Render)

Render fait des sauvegardes automatiques de PostgreSQL :
- **Free**: 1 sauvegarde par jour (rétention 7 jours)
- **Standard**: Sauvegardes continues (rétention 30 jours)

Pour exporter une sauvegarde :
1. Render → PostgreSQL → Backups
2. Cliquez sur **"Export"** pour télécharger

### 7.2 Monitoring (Render)

Render fournit :
- **Metrics**: CPU, Mémoire, Réseau
- **Logs**: Logs d'application en temps réel
- **Alerts**: Alertes par email pour les erreurs

### 7.3 Monitoring (Vercel)

Vercel fournit :
- **Analytics**: Analytics de performance
- **Logs**: Logs de build et d'exécution
- **Speed Insights**: Insights de performance web

---

## 💰 COÛTS ESTIMÉS (MENSUELS)

| Service | Plan | Coût |
|---------|------|------|
| **Render (Backend)** | Free | $0 |
| Render (Backend) | Standard | $7/mois |
| **Render (PostgreSQL)** | Free | $0 |
| Render (PostgreSQL) | Standard | $7/mois |
| **Vercel (Frontend)** | Hobby | $0 |
| Vercel (Frontend) | Pro | $20/mois |
| **Stripe** | Pay-as-you-go | 2.9% + $0.30/transaction |
| **SendGrid** | Free | 100 emails/jour |
| SendGrid | Basic | $15/mois (40,000 emails) |
| **Cloudinary** | Free | 25GB stockage |
| Cloudinary | Plus | $99/mois |
| **Domaine** | - | $10-15/an |

**Total (Démarrage)**: $0/mois (plans gratuits)
**Total (Production)**: ~$50-150/mois

---

## ✅ CHECKLIST DE DÉPLOIEMENT

- [ ] Comptes créés (Render, Vercel, GitHub)
- [ ] Base de données PostgreSQL créée
- [ ] Migrations exécutées
- [ ] Backend déployé sur Render
- [ ] Frontend déployé sur Vercel
- [ ] Variables d'environnement configurées
- [ ] Stripe configuré
- [ ] SendGrid configuré
- [ ] Cloudinary configuré
- [ ] reCAPTCHA configuré
- [ ] Domaines configurés
- [ ] DNS propagé
- [ ] SSL/HTTPS activé
- [ ] Sauvegardes configurées
- [ ] Monitoring activé
- [ ] Tests de production effectués

---

## 🧪 TESTS DE PRODUCTION

### Test Backend

```bash
# Test de santé
curl https://bloom-chloe-api.onrender.com/

# Test d'authentification
curl -X POST https://bloom-chloe-api.onrender.com/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### Test Frontend

1. Ouvrez `https://bloom-chloe.vercel.app`
2. Testez l'inscription
3. Testez la connexion
4. Testez le panier
5. Testez le paiement (mode test Stripe)

---

## 📞 SUPPORT

- **Render Documentation**: https://render.com/docs
- **Vercel Documentation**: https://vercel.com/docs
- **Stripe Documentation**: https://stripe.com/docs
- **SendGrid Documentation**: https://sendgrid.com/docs
- **Cloudinary Documentation**: https://cloudinary.com/documentation

---

## 🔄 MISES À JOUR

Pour déployer une mise à jour :

1. Pushez les modifications sur GitHub
2. Render et Vercel déploieront automatiquement
3. Vérifiez les logs en cas d'erreur

---

## 🚨 DÉPANNAGE

### Erreur de connexion à la base de données

Vérifiez que `DATABASE_URL` est correct dans Render Environment Variables.

### Erreur CORS

Vérifiez que `ALLOWED_ORIGINS` contient votre domaine frontend.

### Erreur Stripe

Vérifiez que vous utilisez les clés **Live** et non Test.

### Erreur Email

Vérifiez que votre domaine est vérifié dans SendGrid (SPF/DKIM).

---

**Bon déploiement ! 🚀**
