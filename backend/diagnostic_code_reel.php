<?php
/**
 * ANALYSE APPROFONDIE DU CODE SOURCE - DIAGNOSTIC RÉEL
 */

echo "🔍 ANALYSE APPROFONDIE DU CODE SOURCE\n";
echo "====================================\n\n";

$projectRoot = __DIR__ . '/..';

// 1. ANALYSE DES FICHIERS DE CONFIGURATION RÉELS
echo "1️⃣ CONFIGURATION RÉELLE\n";
echo "========================\n";

$envFile = $projectRoot . '/.env';
if (file_exists($envFile)) {
    echo "✅ .env trouvé à la racine\n";
    $envContent = file_get_contents($envFile);
    echo "   Contenu: " . trim($envContent) . "\n";
    $viteApiUrl = trim($envContent);
    echo "   VITE_API_URL: $viteApiUrl\n";
} else {
    echo "❌ .env manquant à la racine\n";
}

// Vérifier vite.config.js
$viteConfig = $projectRoot . '/vite.config.js';
if (file_exists($viteConfig)) {
    echo "✅ vite.config.js trouvé\n";
    $viteContent = file_get_contents($viteConfig);
    echo "   Taille: " . filesize($viteConfig) . " bytes\n";
} else {
    echo "❌ vite.config.js manquant\n";
}

echo "\n";

// 2. ANALYSE DES SERVICES API
echo "2️⃣ SERVICES API RÉELS\n";
echo "========================\n";

$apiServiceFile = $projectRoot . '/src/services/api.js';
if (file_exists($apiServiceFile)) {
    echo "✅ api.js trouvé\n";
    $apiContent = file_get_contents($apiServiceFile);
    
    // Analyser le contenu
    if (strpos($apiContent, 'withCredentials: true') !== false) {
        echo "   ⚠️  withCredentials: true détecté (problème CORS)\n";
    } elseif (strpos($apiContent, 'withCredentials: false') !== false) {
        echo "   ✅ withCredentials: false (OK pour CORS)\n";
    }
    
    if (strpos($apiContent, 'VITE_API_URL') !== false) {
        echo "   ✅ Utilisation de VITE_API_URL\n";
    }
    
    // Extraire l'URL de base
    if (preg_match('/const API_URL = import\.meta\.env\.VITE_API_URL.*?\'([^\'\"]+)\'/', $apiContent, $matches)) {
        echo "   📡 API_URL configurée: " . $matches[1] . "\n";
    }
    
    // Vérifier les services
    if (strpos($apiContent, 'productsService') !== false) {
        echo "   ✅ productsService utilisé\n";
    }
    if (strpos($apiContent, 'authService') !== false) {
        echo "   ✅ authService utilisé\n";
    }
} else {
    echo "❌ api.js manquant\n";
}

echo "\n";

// 3. ANALYSE DES STORES PINIA
echo "3️⃣ STORES PINIA RÉELS\n";
echo "========================\n";

$productsStoreFile = $projectRoot . '/src/stores/products.js';
if (file_exists($productsStoreFile)) {
    echo "✅ products.js store trouvé\n";
    $storeContent = file_get_contents($productsStoreFile);
    
    // Analyser le store
    if (strpos($storeContent, 'defineStore') !== false) {
        echo "   ✅ Utilisation de Pinia defineStore\n";
    }
    
    if (strpos($storeContent, 'productsService.getAll') !== false) {
        echo "   ✅ Appel à productsService.getAll\n";
    }
    
    if (strpos($storeContent, 'per_page: 200') !== false) {
        echo "   ✅ Requête de 200 produits\n";
    }
    
    // Vérifier la transformation des données
    if (strpos($storeContent, 'getProductImageUrl') !== false) {
        echo "   ✅ Utilisation de getProductImageUrl\n";
    }
    
    // Vérifier le cache
    if (strpos($storeContent, 'CACHE_DURATION') !== false) {
        echo "   ✅ Cache de 5 minutes configuré\n";
    }
} else {
    echo "❌ products.js store manquant\n";
}

echo "\n";

// 4. ANALYSE DES COMPOSANTS VUE
echo "4️⃣ COMPOSANTS VUE RÉELS\n";
echo "========================\n";

$componentsDir = $projectRoot . '/src/components';
if (is_dir($componentsDir)) {
    $components = glob($componentsDir . '/*.vue');
    echo "✅ " . count($components) . " composants Vue trouvés\n";
    
    foreach ($components as $component) {
        $nom = basename($component, '.vue');
        $content = file_get_contents($component);
        
        echo "   📄 $nom.vue\n";
        
        // Analyser le contenu
        if (strpos($content, '<script setup>') !== false) {
            echo "      ✅ Composition API\n";
        }
        if (strpos($content, 'useProductStore') !== false) {
            echo "      ✅ Utilise useProductStore\n";
        }
        if (strpos($content, 'fetchProducts') !== false) {
            echo "      ✅ Appelle fetchProducts\n";
        }
    }
} else {
    echo "❌ Dossier components manquant\n";
}

echo "\n";

// 5. ANALYSE DES IMAGES RÉELLES
echo "5️⃣ IMAGES RÉELLES\n";
echo "===================\n";

$assetsDir = $projectRoot . '/src/assets';
if (is_dir($assetsDir)) {
    $allImages = glob($assetsDir . '/*.{jpg,jpeg,png,gif,svg}', GLOB_BRACE);
    $produitsImages = glob($assetsDir . '/produit*.jpg');
    $storeImages = glob($assetsDir . '/store*.jpg');
    $categoriesImages = glob($assetsDir . '/categorie*.jpg');
    
    echo "✅ Dossier assets trouvé\n";
    echo "   📊 Images totales: " . count($allImages) . "\n";
    echo "   🛍️  Images produits: " . count($produitsImages) . "\n";
    echo "   🏪 Images store: " . count($storeImages) . "\n";
    echo "   📂 Images catégories: " . count($categoriesImages) . "\n";
    
    // Vérifier les noms de fichiers
    $expectedProduits = 90;
    $expectedStore = 12;
    $expectedCategories = 12;
    
    if (count($produitsImages) >= $expectedProduits) {
        echo "   ✅ Images produits complètes ($expectedProduits attendues)\n";
    } else {
        echo "   ⚠️  Images produits incomplètes (" . count($produitsImages) . "/$expectedProduits)\n";
    }
    
    if (count($storeImages) >= $expectedStore) {
        echo "   ✅ Images store complètes ($expectedStore attendues)\n";
    } else {
        echo "   ⚠️  Images store incomplètes (" . count($storeImages) . "/$expectedStore)\n";
    }
} else {
    echo "❌ Dossier assets manquant\n";
}

echo "\n";

// 6. ANALYSE DE LA BASE DE DONNÉES
echo "6️⃣ BASE DE DONNÉES RÉELLE\n";
echo "==========================\n";

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si la base existe
    $stmt = $pdo->query("SHOW DATABASES LIKE 'bloom_chloe'");
    if ($stmt->rowCount() > 0) {
        $pdo->exec("USE bloom_chloe");
        echo "✅ Base bloom_chloe trouvée\n";
        
        // Analyser la structure
        $tablesStmt = $pdo->query("SHOW TABLES");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
        echo "   📊 Tables: " . implode(', ', $tables) . "\n";
        
        // Compter les enregistrements
        $productsStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
        $productsCount = $productsStmt->fetch()['total'];
        echo "   🛍️  Produits: $productsCount\n";
        
        // Analyser les URLs d'images dans la BDD
        $imageUrlsStmt = $pdo->query("SELECT image_url FROM products WHERE image_url IS NOT NULL LIMIT 10");
        $imageUrls = $imageUrlsStmt->fetchAll(PDO::FETCH_COLUMN);
        echo "   🖼️  Exemples d'URLs images:\n";
        foreach ($imageUrls as $url) {
            echo "      - $url\n";
        }
        
        // Vérifier les sources
        $sourcesStmt = $pdo->query("SELECT source, COUNT(*) as count FROM products GROUP BY source");
        $sources = $sourcesStmt->fetchAll();
        echo "   📦 Sources produits:\n";
        foreach ($sources as $source) {
            echo "      - {$source['source']}: {$source['count']}\n";
        }
        
    } else {
        echo "❌ Base bloom_chloe non trouvée\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur BDD: " . $e->getMessage() . "\n";
}

echo "\n";

// 7. ANALYSE DES ENDPOINTS BACKEND
echo "7️⃣ ENDPOINTS BACKEND RÉELS\n";
echo "========================\n";

$backendDir = $projectRoot . '/backend';
if (is_dir($backendDir)) {
    $endpoints = [];
    
    // Scanner tous les fichiers PHP
    $phpFiles = glob($backendDir . '/*.php', GLOB_BRACE);
    $phpFiles = array_merge($phpFiles, glob($backendDir . '/**/*.php', GLOB_BRACE));
    
    foreach ($phpFiles as $file) {
        $relativePath = str_replace([$backendDir . '/', '\\'], ['', '/'], $file);
        $endpoints[] = $relativePath;
    }
    
    echo "✅ " . count($endpoints) . " fichiers PHP trouvés\n";
    
    // Vérifier les endpoints critiques
    $criticalEndpoints = [
        'products/get_all.php',
        'auth/login.php',
        'auth/register.php',
        'categories/get_all.php'
    ];
    
    foreach ($criticalEndpoints as $endpoint) {
        $fullPath = $backendDir . '/' . $endpoint;
        if (file_exists($fullPath)) {
            echo "   ✅ $endpoint\n";
            $content = file_get_contents($fullPath);
            
            // Analyser le contenu
            if (strpos($content, 'header(\'Access-Control-Allow-Origin\')') !== false) {
                echo "      ✅ CORS configuré\n";
            }
            if (strpos($content, 'json_encode') !== false) {
                echo "      ✅ Réponse JSON\n";
            }
        } else {
            echo "   ❌ $endpoint (manquant)\n";
        }
    }
} else {
    echo "❌ Dossier backend manquant\n";
}

echo "\n";

// 8. SYNTHÈSE DES PROBLÈMES RÉELS
echo "8️⃣ SYNTHÈSE DES PROBLÈMES\n";
echo "========================\n";

$problemesReels = [];

// Vérifier la cohérence frontend/backend
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, '8080') !== false) {
        $problemesReels[] = "Frontend configuré pour port 8080";
    }
}

if (file_exists($apiServiceFile)) {
    $apiContent = file_get_contents($apiServiceFile);
    if (strpos($apiContent, 'withCredentials: true') !== false) {
        $problemesReels[] = "withCredentials: true dans Axios (problème CORS)";
    }
}

if (is_dir($assetsDir)) {
    $produitsImages = glob($assetsDir . '/produit*.jpg');
    if (count($produitsImages) < 90) {
        $problemesReels[] = "Images produits incomplètes (" . count($produitsImages) . "/90)";
    }
}

echo "   Problèmes identifiés: " . count($problemesReels) . "\n";
foreach ($problemesReels as $probleme) {
    echo "   ❌ $probleme\n";
}

if (empty($problemesReels)) {
    echo "   ✅ Aucun problème structurel détecté\n";
}

echo "\n";

// 9. SOLUTIONS CONCRÈTES
echo "9️⃣ SOLUTIONS CONCRÈTES\n";
echo "========================\n";

echo "   1. 🔧 CORRECTIONS IMMÉDIATES:\n";
echo "      - Vérifier que .env contient le bon port d'API\n";
echo "      - S'assurer que withCredentials: false dans api.js\n";
echo "      - Démarrer le backend sur le bon port\n";

echo "\n   2. 🚀 DÉMARRAGE CORRECT:\n";
echo "      - Backend: php -S localhost:8080 -t backend\n";
echo "      - Frontend: npm run dev (depuis la racine)\n";

echo "\n   3. 📊 VÉRIFICATIONS:\n";
echo "      - Base de données bloom_chloe accessible\n";
echo "      - Images dans src/assets/ accessibles\n";
echo "      - API répond correctement\n";

echo "\n✅ ANALYSE TERMINÉE\n";

?>
