<?php
/**
 * DIAGNOSTIC COMPLET ET DÉTAILLÉ DU PROJET BLOOM-CHLOE
 * Analyse réelle du code et de l'infrastructure
 */

echo "🔍 DIAGNOSTIC COMPLET DÉTAILLÉ - BLOOM-CHLOE\n";
echo "===============================================\n\n";

// 1. ANALYSE DE L'ARCHITECTURE
echo "1️⃣ ARCHITECTURE DU PROJET\n";
echo "========================\n";

$projectRoot = __DIR__ . '/..';
echo "📁 Racine: $projectRoot\n";

$dossiers = [
    'backend' => 'API PHP',
    'frontend' => 'Vue 3 + Vite',
    'frontend/src' => 'Source Vue',
    'frontend/public' => 'Public frontend',
    'frontend/src/assets' => 'Assets images',
    'database' => 'Scripts BDD'
];

foreach ($dossiers as $dossier => $description) {
    $chemin = $projectRoot . '/' . $dossier;
    $existe = is_dir($chemin);
    $fichiers = $existe ? count(glob($chemin . '/*')) : 0;
    echo sprintf("   %s %s (%d éléments)\n", $existe ? '✅' : '❌', $dossier, $fichiers);
}

echo "\n";

// 2. VÉRIFICATION DES SERVEURS ACTIFS
echo "2️⃣ ÉTAT DES SERVEURS\n";
echo "=====================\n";

$ports = [8000, 8080, 5173, 5174];
foreach ($ports as $port) {
    $socket = @fsockopen('localhost', $port, $errno, $errstr, 1);
    $actif = $socket ? true : false;
    echo sprintf("   Port %d: %s\n", $port, $actif ? '✅ ACTIF' : '❌ INACTIF');
    if ($socket) fclose($socket);
}

echo "\n";

// 3. ANALYSE DE LA BASE DE DONNÉES
echo "3️⃣ BASE DE DONNÉES\n";
echo "==================\n";

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si la base bloom_chloe existe
    $stmt = $pdo->query("SHOW DATABASES LIKE 'bloom_chloe'");
    $dbExists = $stmt->rowCount() > 0;
    
    if ($dbExists) {
        $pdo->exec("USE bloom_chloe");
        echo "   ✅ Base bloom_chloe: EXISTE\n";
        
        // Compter les tables
        $tablesStmt = $pdo->query("SHOW TABLES");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
        echo "   📊 Tables: " . implode(', ', $tables) . "\n";
        
        // Compter les produits
        $productsStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
        $productsCount = $productsStmt->fetch()['total'];
        echo "   🛍️  Produits: $productsCount\n";
        
        // Compter les catégories
        $categoriesStmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
        $categoriesCount = $categoriesStmt->fetch()['total'];
        echo "   📂 Catégories: $categoriesCount\n";
        
        // Compter les utilisateurs
        $usersStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
        $usersCount = $usersStmt->fetch()['total'];
        echo "   👤 Utilisateurs: $usersCount\n";
        
        // Vérifier les produits avec images
        $imagesStmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE image_url IS NOT NULL AND image_url != ''");
        $imagesCount = $imagesStmt->fetch()['total'];
        echo "   🖼️  Produits avec images: $imagesCount\n";
        
        // Vérifier les sources
        $sourcesStmt = $pdo->query("SELECT source, COUNT(*) as count FROM products GROUP BY source");
        $sources = $sourcesStmt->fetchAll();
        echo "   📦 Sources: ";
        foreach ($sources as $source) {
            echo $source['source'] . " (" . $source['count'] . ") ";
        }
        echo "\n";
        
    } else {
        echo "   ❌ Base bloom_chloe: INEXISTANTE\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur BDD: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. ANALYSE DES FICHIERS DE CONFIGURATION
echo "4️⃣ FICHIERS DE CONFIGURATION\n";
echo "=============================\n";

$configFiles = [
    'frontend/.env' => 'Variables environnement frontend',
    'backend/config/db.php' => 'Configuration BDD backend',
    'frontend/vite.config.js' => 'Configuration Vite',
    'frontend/package.json' => 'Dépendances frontend',
    'frontend/src/services/api.js' => 'Service API Axios'
];

foreach ($configFiles as $file => $description) {
    $chemin = $projectRoot . '/' . $file;
    $existe = file_exists($chemin);
    $taille = $existe ? filesize($chemin) : 0;
    echo sprintf("   %s %s (%d bytes)\n", $existe ? '✅' : '❌', $file, $taille);
    
    if ($existe && strpos($file, '.env') !== false) {
        $content = file_get_contents($chemin);
        echo "      Contenu: " . trim($content) . "\n";
    }
}

echo "\n";

// 5. ANALYSE DES IMAGES
echo "5️⃣ IMAGES ET ASSETS\n";
echo "====================\n";

$assetsDir = $projectRoot . '/frontend/src/assets';
if (is_dir($assetsDir)) {
    $images = glob($assetsDir . '/*.{jpg,jpeg,png,gif,svg}', GLOB_BRACE);
    $produitsImages = glob($assetsDir . '/produit*.jpg');
    $storeImages = glob($assetsDir . '/store*.jpg');
    $categoriesImages = glob($assetsDir . '/categorie*.jpg');
    
    echo "   ✅ Dossier assets: EXISTE\n";
    echo "   🖼️  Images totales: " . count($images) . "\n";
    echo "   🛍️  Images produits: " . count($produitsImages) . "\n";
    echo "   🏪 Images store: " . count($storeImages) . "\n";
    echo "   📂 Images catégories: " . count($categoriesImages) . "\n";
    
    // Vérifier quelques images
    for ($i = 1; $i <= min(5, count($produitsImages)); $i++) {
        $image = $produitsImages[$i-1] ?? null;
        if ($image && file_exists($image)) {
            $size = filesize($image);
            echo "      produit$i.jpg: " . number_format($size) . " bytes ✅\n";
        }
    }
} else {
    echo "   ❌ Dossier assets: INEXISTANT\n";
}

echo "\n";

// 6. ANALYSE DES COMPOSANTS FRONTEND
echo "6️⃣ COMPOSANTS FRONTEND\n";
echo "=======================\n";

$componentsDir = $projectRoot . '/frontend/src/components';
if (is_dir($componentsDir)) {
    $components = glob($componentsDir . '/*.vue');
    echo "   ✅ Composants Vue: " . count($components) . "\n";
    
    foreach ($components as $component) {
        $nom = basename($component, '.vue');
        $taille = filesize($component);
        echo "      📄 $nom.vue (" . number_format($taille) . " bytes)\n";
    }
} else {
    echo "   ❌ Dossier components: INEXISTANT\n";
}

// Vérifier les stores
$storesDir = $projectRoot . '/frontend/src/stores';
if (is_dir($storesDir)) {
    $stores = glob($storesDir . '/*.js');
    echo "   ✅ Stores Pinia: " . count($stores) . "\n";
    
    foreach ($stores as $store) {
        $nom = basename($store, '.js');
        $taille = filesize($store);
        echo "      🗄️ $nom.js (" . number_format($taille) . " bytes)\n";
    }
}

echo "\n";

// 7. ANALYSE DES ENDPOINTS BACKEND
echo "7️⃣ ENDPOINTS BACKEND\n";
echo "====================\n";

$backendDir = $projectRoot . '/backend';
if (is_dir($backendDir)) {
    $endpoints = [];
    
    // Scanner les dossiers principaux
    $dirs = ['products', 'auth', 'categories', 'orders'];
    foreach ($dirs as $dir) {
        $dirPath = $backendDir . '/' . $dir;
        if (is_dir($dirPath)) {
            $files = glob($dirPath . '/*.php');
            foreach ($files as $file) {
                $endpoint = basename($file, '.php');
                $endpoints[] = "$dir/$endpoint";
            }
        }
    }
    
    echo "   ✅ Endpoints trouvés: " . count($endpoints) . "\n";
    foreach ($endpoints as $endpoint) {
        echo "      🔗 $endpoint.php\n";
    }
} else {
    echo "   ❌ Dossier backend: INEXISTANT\n";
}

echo "\n";

// 8. TESTS DES ENDPOINTS CRITIQUES
echo "8️⃣ TESTS DES ENDPOINTS\n";
echo "=====================\n";

$endpointsToTest = [
    'products/get_all.php' => 'GET /products/get_all.php',
    'auth/login.php' => 'POST /auth/login.php',
    'categories/get_all.php' => 'GET /categories/get_all.php'
];

foreach ($endpointsToTest as $endpoint => $description) {
    $url = "http://localhost:8080/$endpoint";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'method' => 'GET'
        ]
    ]);
    
    $start = microtime(true);
    $response = @file_get_contents($url, false, $context);
    $time = round((microtime(true) - $start) * 1000, 2);
    
    if ($response) {
        $data = json_decode($response, true);
        $status = $data && isset($data['data']) ? '✅ OK' : '⚠️  ERREUR';
        echo sprintf("   %s %s (%.2fms)\n", $status, $description, $time);
    } else {
        echo "   ❌ $description (ÉCHEC)\n";
    }
}

echo "\n";

// 9. ANALYSE DES DÉPENDANCES
echo "9️⃣ DÉPENDANCES ET VERSIONS\n";
echo "==========================\n";

// Vérifier package.json
$packageJson = $projectRoot . '/frontend/package.json';
if (file_exists($packageJson)) {
    $content = json_decode(file_get_contents($packageJson), true);
    echo "   ✅ package.json trouvé\n";
    
    if (isset($content['dependencies'])) {
        echo "   📦 Dépendances principales:\n";
        foreach ($content['dependencies'] as $dep => $version) {
            echo "      $dep: $version\n";
        }
    }
    
    if (isset($content['devDependencies'])) {
        echo "   🔧 Dépendances dev:\n";
        foreach ($content['devDependencies'] as $dep => $version) {
            echo "      $dep: $version\n";
        }
    }
}

// Vérifier version PHP
echo "   🐘 Version PHP: " . PHP_VERSION . "\n";

echo "\n";

// 10. RÉSUMÉ DES PROBLÈMES
echo "🔍 RÉSUMÉ DES PROBLÈMES IDENTIFIÉS\n";
echo "====================================\n";

$problemes = [];

// Vérifier les problèmes courants
if (!file_exists($projectRoot . '/frontend/.env')) {
    $problemes[] = "❌ Fichier .env manquant";
}

if (!is_dir($projectRoot . '/frontend/src/assets')) {
    $problemes[] = "❌ Dossier assets manquant";
}

$socket8000 = @fsockopen('localhost', 8000, $errno, $errstr, 1);
$socket8080 = @fsockopen('localhost', 8080, $errno, $errstr, 1);
if (!$socket8000 && !$socket8080) {
    $problemes[] = "❌ Aucun serveur backend actif (ports 8000/8080)";
}

echo "   Problèmes détectés: " . count($problemes) . "\n";
foreach ($problemes as $probleme) {
    echo "   $probleme\n";
}

if (empty($problemes)) {
    echo "   ✅ Aucun problème critique détecté\n";
}

echo "\n";

// 11. RECOMMANDATIONS
echo "💡 RECOMMANDATIONS\n";
echo "=================\n";

echo "   1. 🚀 Démarrer les serveurs:\n";
echo "      - Backend: php -S localhost:8080 -t backend\n";
echo "      - Frontend: cd frontend && npm run dev\n";

echo "\n   2. 🔧 Vérifier la configuration:\n";
echo "      - VITE_API_URL dans .env doit pointer vers le bon port\n";
echo "      - withCredentials: false dans api.js pour éviter les CORS\n";

echo "\n   3. 📊 Base de données:\n";
echo "      - S'assurer que MySQL/Laragon est démarré\n";
echo "      - Vérifier que la base bloom_chloe existe\n";

echo "\n   4. 🖼️  Images:\n";
echo "      - Les images doivent être dans frontend/src/assets/\n";
echo "      - URLs dans la BDD doivent pointer vers /frontend/src/assets/\n";

echo "\n✅ DIAGNOSTIC TERMINÉ\n";
echo "========================\n";

?>
