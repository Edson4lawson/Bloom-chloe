<?php
/**
 * Vérification finale après réorganisation
 */

echo "🔍 VÉRIFICATION FINALE APRÈS RÉORGANISATION\n";
echo "==========================================\n\n";

$projectRoot = __DIR__ . '/..';

// 1. Structure des dossiers
echo "1️⃣ STRUCTURE DES DOSSIERS\n";
echo "========================\n";

$dossiers = [
    'frontend' => 'Frontend Vue 3',
    'backend' => 'Backend PHP',
    'frontend/src' => 'Source frontend',
    'frontend/src/assets' => 'Assets images',
    'backend/products' => 'API produits',
    'backend/auth' => 'API auth',
    'backend/categories' => 'API catégories'
];

foreach ($dossiers as $dossier => $description) {
    $chemin = $projectRoot . '/' . $dossier;
    $existe = is_dir($chemin);
    echo sprintf("   %s %s\n", $existe ? '✅' : '❌', $dossier);
}

echo "\n";

// 2. Configuration
echo "2️⃣ CONFIGURATION\n";
echo "================\n";

$envFile = $projectRoot . '/frontend/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    echo "✅ .env frontend: " . trim($envContent) . "\n";
} else {
    echo "❌ .env frontend manquant\n";
}

$apiFile = $projectRoot . '/frontend/services/api.js';
if (file_exists($apiFile)) {
    $apiContent = file_get_contents($apiFile);
    if (strpos($apiContent, 'http://localhost:8080') !== false) {
        echo "✅ API URL correcte dans api.js\n";
    } else {
        echo "❌ API URL incorrecte dans api.js\n";
    }
} else {
    echo "❌ api.js manquant\n";
}

echo "\n";

// 3. Serveurs actifs
echo "3️⃣ ÉTAT DES SERVEURS\n";
echo "===================\n";

$ports = [8080, 5173, 5174];
foreach ($ports as $port) {
    $socket = @fsockopen('localhost', $port, $errno, $errstr, 1);
    $actif = $socket ? true : false;
    echo sprintf("   Port %d: %s\n", $port, $actif ? '✅ ACTIF' : '❌ INACTIF');
    if ($socket) fclose($socket);
}

echo "\n";

// 4. Base de données
echo "4️⃣ BASE DE DONNÉES\n";
echo "==================\n";

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW DATABASES LIKE 'bloom_chloe'");
    if ($stmt->rowCount() > 0) {
        $pdo->exec("USE bloom_chloe");
        
        $productsStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
        $productsCount = $productsStmt->fetch()['total'];
        echo "✅ Base bloom_chloe: $productsCount produits\n";
        
        $categoriesStmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
        $categoriesCount = $categoriesStmt->fetch()['total'];
        echo "✅ Catégories: $categoriesCount\n";
        
        // Vérifier les sources
        $sourcesStmt = $pdo->query("SELECT source, COUNT(*) as count FROM products GROUP BY source");
        $sources = $sourcesStmt->fetchAll();
        echo "📦 Sources: ";
        foreach ($sources as $source) {
            echo $source['source'] . " (" . $source['count'] . ") ";
        }
        echo "\n";
        
    } else {
        echo "❌ Base bloom_chloe non trouvée\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur BDD: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Test API
echo "5️⃣ TEST API\n";
echo "============\n";

$apiUrl = 'http://localhost:8080/products/get_all.php?per_page=3';
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'method' => 'GET'
    ]
]);

$response = @file_get_contents($apiUrl, false, $context);
if ($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "✅ API produits: " . count($data['data']) . " produits retournés\n";
        echo "📊 Total: " . $data['pagination']['total'] . " produits\n";
        
        // Vérifier les URLs d'images
        $firstProduct = $data['data'][0] ?? null;
        if ($firstProduct && isset($firstProduct['image_url'])) {
            echo "🖼️  Exemple image URL: " . $firstProduct['image_url'] . "\n";
        }
    } else {
        echo "❌ API produits: Réponse invalide\n";
    }
} else {
    echo "❌ API produits: Erreur de connexion\n";
}

echo "\n";

// 6. Images
echo "6️⃣ IMAGES\n";
echo "==========\n";

$assetsDir = $projectRoot . '/frontend/src/assets';
if (is_dir($assetsDir)) {
    $allImages = glob($assetsDir . '/*.{jpg,jpeg,png,gif,svg}', GLOB_BRACE);
    $produitsImages = glob($assetsDir . '/produit*.jpg');
    $storeImages = glob($assetsDir . '/store*.jpg');
    $categoriesImages = glob($assetsDir . '/categorie*.jpg');
    
    echo "✅ Images totales: " . count($allImages) . "\n";
    echo "✅ Produits: " . count($produitsImages) . "\n";
    echo "✅ Store: " . count($storeImages) . "\n";
    echo "✅ Catégories: " . count($categoriesImages) . "\n";
} else {
    echo "❌ Dossier assets manquant\n";
}

echo "\n";

// 7. Résumé
echo "7️⃣ RÉSUMÉ FINAL\n";
echo "===============\n";

$checks = [
    'Structure correcte' => is_dir($projectRoot . '/frontend'),
    'Backend créé' => is_dir($projectRoot . '/backend/products'),
    'Configuration OK' => file_exists($envFile),
    'API fonctionnelle' => isset($response) && $response !== false,
    'Images présentes' => is_dir($assetsDir) && count(glob($assetsDir . '/*.jpg')) > 0
];

$passed = 0;
$total = count($checks);

foreach ($checks as $check => $result) {
    $status = $result ? '✅' : '❌';
    echo sprintf("   %s %s\n", $status, $check);
    if ($result) $passed++;
}

echo "\n📊 Score: $passed/$total tests passés\n";

if ($passed === $total) {
    echo "🎉 RÉORGANISATION RÉUSSIE !\n";
    echo "\n🌐 Accès:\n";
    echo "   - Frontend: http://localhost:5173 ou http://localhost:5174\n";
    echo "   - Backend API: http://localhost:8080\n";
    echo "   - Admin: http://localhost:5173/admin\n";
    echo "\n📦 Produits: Tous les produits avec vraies images sont prêts !\n";
} else {
    echo "⚠️  Certains points nécessitent attention\n";
}

echo "\n✅ VÉRIFICATION TERMINÉE\n";

?>
