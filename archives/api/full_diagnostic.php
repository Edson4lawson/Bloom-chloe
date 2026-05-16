<?php
/**
 * Diagnostic complet du projet Bloom Chloé
 */

echo "🔍 DIAGNOSTIC COMPLET - BLOOM CHLOÉ\n";
echo "=====================================\n\n";

// Test de connexion MySQL
echo "1️⃣ TEST DE CONNEXION MYSQL\n";
echo "------------------------\n";

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion MySQL réussie\n";
    
    // Vérifier les bases de données
    $stmt = $pdo->query("SHOW DATABASES LIKE '%bloom%'");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($databases)) {
        echo "📁 Bases bloom trouvées : " . implode(', ', $databases) . "\n";
        
        foreach ($databases as $db) {
            echo "\n🔍 Analyse de la base : $db\n";
            try {
                $pdo->exec("USE $db");
                
                // Vérifier les tables
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "   📋 Tables (" . count($tables) . ") : " . implode(', ', $tables) . "\n";
                
                // Compter les produits
                if (in_array('products', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
                    $productCount = $stmt->fetch()['count'];
                    echo "   🛍️  Produits : $productCount\n";
                    
                    if ($productCount > 0) {
                        echo "   📦 Liste des produits :\n";
                        $stmt = $pdo->query("SELECT id, name, price, category_id FROM products ORDER BY id LIMIT 10");
                        $products = $stmt->fetchAll();
                        
                        foreach ($products as $product) {
                            echo "      - ID: {$product['id']}, Nom: {$product['name']}, Prix: {$product['price']} FCFA\n";
                        }
                        
                        if ($productCount > 10) {
                            echo "      ... et " . ($productCount - 10) . " autres produits\n";
                        }
                    }
                }
                
                // Compter les utilisateurs
                if (in_array('users', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                    $userCount = $stmt->fetch()['count'];
                    echo "   👥 Utilisateurs : $userCount\n";
                    
                    // Vérifier les admins
                    $stmt = $pdo->query("SELECT email, role FROM users WHERE role = 'admin'");
                    $admins = $stmt->fetchAll();
                    if (!empty($admins)) {
                        echo "   👤 Admins : " . count($admins) . "\n";
                        foreach ($admins as $admin) {
                            echo "      - {$admin['email']}\n";
                        }
                    }
                }
                
                // Compter les commandes
                if (in_array('orders', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
                    $orderCount = $stmt->fetch()['count'];
                    echo "   🛒 Commandes : $orderCount\n";
                }
                
                // Compter les catégories
                if (in_array('categories', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
                    $categoryCount = $stmt->fetch()['count'];
                    echo "   📂 Catégories : $categoryCount\n";
                    
                    if ($categoryCount > 0) {
                        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY id LIMIT 5");
                        $categories = $stmt->fetchAll();
                        echo "      Liste : ";
                        $catNames = [];
                        foreach ($categories as $cat) {
                            $catNames[] = $cat['name'];
                        }
                        echo implode(', ', $catNames);
                        if ($categoryCount > 5) {
                            echo " ... et " . ($categoryCount - 5) . " autres";
                        }
                        echo "\n";
                    }
                }
                
            } catch (Exception $e) {
                echo "   ❌ Erreur base $db : " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "❌ Aucune base bloom trouvée\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur MySQL : " . $e->getMessage() . "\n";
}

echo "\n2️⃣ TEST DES SERVEURS\n";
echo "-------------------\n";

// Test backend PHP
echo "🔧 Test Backend PHP (port 8000)...\n";
$backendUrl = 'http://localhost:8000';
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'method' => 'GET'
    ]
]);

if (@file_get_contents($backendUrl, false, $context) !== false) {
    echo "✅ Backend PHP accessible\n";
    
    // Test API produits
    $apiUrl = 'http://localhost:8000/products/get_all.php';
    if (@file_get_contents($apiUrl, false, $context) !== false) {
        echo "✅ API produits fonctionnelle\n";
    } else {
        echo "❌ API produits inaccessible\n";
    }
} else {
    echo "❌ Backend PHP inaccessible\n";
}

// Test frontend
echo "\n🌐 Test Frontend...\n";
$frontendPorts = [5173, 5174, 5175, 5176, 5177];
$frontendFound = false;

foreach ($frontendPorts as $port) {
    $url = "http://localhost:$port";
    if (@file_get_contents($url, false, $context) !== false) {
        echo "✅ Frontend trouvé sur port $port : $url\n";
        $frontendFound = true;
        break;
    }
}

if (!$frontendFound) {
    echo "❌ Frontend inaccessible\n";
}

echo "\n3️⃣ ÉTAT DES FICHIERS\n";
echo "-------------------\n";

// Vérifier les fichiers importants
$files = [
    'frontend/package.json' => 'Package Frontend',
    'frontend/.env' => 'Variables Frontend',
    'backend/config/db.php' => 'Config BDD Backend',
    'database/sql/schema.sql' => 'Schéma SQL',
    'backend/products/get_all.php' => 'API Produits'
];

foreach ($files as $file => $description) {
    if (file_exists(__DIR__ . '/../' . $file)) {
        echo "✅ $description\n";
    } else {
        echo "❌ $description manquant\n";
    }
}

echo "\n4️⃣ RÉSUMÉ\n";
echo "----------\n";
echo "📊 État général du projet :\n";
echo "   - Base de données : " . (isset($databases) && !empty($databases) ? "✅ OK" : "❌ KO") . "\n";
echo "   - Backend PHP : " . (@file_get_contents('http://localhost:8000', false, $context) !== false ? "✅ OK" : "❌ KO") . "\n";
echo "   - Frontend : " . ($frontendFound ? "✅ OK" : "❌ KO") . "\n";

if (isset($productCount)) {
    echo "\n🛍️  Produits dans la base : $productCount\n";
}

echo "\n🎯 Recommandations :\n";
if (!isset($databases) || empty($databases)) {
    echo "   - Créer/restaurer la base de données\n";
}
if (@file_get_contents('http://localhost:8000', false, $context) === false) {
    echo "   - Démarrer le backend PHP\n";
}
if (!$frontendFound) {
    echo "   - Démarrer le frontend Vite\n";
}

echo "\n🚀 Prochaines étapes :\n";
echo "   1. Démarrer Laragon/MySQL si nécessaire\n";
echo "   2. Démarrer le backend : php -S localhost:8000\n";
echo "   3. Démarrer le frontend : npm run dev\n";
echo "   4. Accéder au site et dashboard admin\n";

?>
