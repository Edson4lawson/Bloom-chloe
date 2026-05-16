<?php
/**
 * Intégration finale des produits store dans la base de données
 */

require_once __DIR__ . '/config/db.php';

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    echo "🏪 INTÉGRATION DES PRODUITS STORE\n";
    echo "===============================\n\n";
    
    // Charger les données préparées
    $dataFile = __DIR__ . '/store_products_data.json';
    if (!file_exists($dataFile)) {
        echo "❌ Fichier de données store non trouvé. Exécutez d'abord prepare_store_integration.php\n";
        exit(1);
    }
    
    $storeProducts = json_decode(file_get_contents($dataFile), true);
    
    echo "📊 Produits store à intégrer: " . count($storeProducts) . "\n\n";
    
    $inserted = 0;
    $updated = 0;
    
    foreach ($storeProducts as $product) {
        // Vérifier si le produit existe déjà
        $checkStmt = $pdo->prepare("SELECT id, image_url FROM products WHERE name = ? AND source = 'store'");
        $checkStmt->execute([$product['name']]);
        $existing = $checkStmt->fetch();
        
        if ($existing) {
            // Mettre à jour l'image URL si nécessaire
            if ($existing['image_url'] !== $product['image_url']) {
                $updateStmt = $pdo->prepare("UPDATE products SET image_url = ?, description = ?, price = ?, category_id = ?, slug = ? WHERE id = ?");
                if ($updateStmt->execute([
                    $product['image_url'],
                    $product['description'],
                    $product['price'],
                    $product['category_id'],
                    $product['slug'],
                    $existing['id']
                ])) {
                    $updated++;
                    echo "🔄 Mis à jour: {$product['name']}\n";
                }
            } else {
                echo "ℹ️  Déjà existant: {$product['name']}\n";
            }
        } else {
            // Insérer nouveau produit
            $insertStmt = $pdo->prepare("
                INSERT INTO products (name, description, price, category_id, image_url, slug, source, stock_quantity, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'store', 10, NOW())
            ");
            
            if ($insertStmt->execute([
                $product['name'],
                $product['description'],
                $product['price'],
                $product['category_id'],
                $product['image_url'],
                $product['slug']
            ])) {
                $inserted++;
                echo "✅ Inséré: {$product['name']} - {$product['price']} FCFA\n";
            }
        }
    }
    
    echo "\n📈 RÉSULTATS\n";
    echo "===========\n";
    echo "✅ Produits insérés: $inserted\n";
    echo "🔄 Produits mis à jour: $updated\n";
    
    // Vérification finale
    $finalStmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE source = 'store'");
    $finalResult = $finalStmt->fetch();
    
    echo "📊 Total produits store: " . $finalResult['count'] . "\n";
    
    // Afficher les produits store
    echo "\n📦 LISTE DES PRODUITS STORE:\n";
    echo "========================\n";
    
    $listStmt = $pdo->query("
        SELECT id, name, price, image_url, category_id 
        FROM products 
        WHERE source = 'store' 
        ORDER BY id
    ");
    $storeItems = $listStmt->fetchAll();
    
    foreach ($storeItems as $item) {
        echo "🏪 ID: {$item['id']} | {$item['name']} | {$item['price']} FCFA | Catégorie: {$item['category_id']}\n";
        echo "    🖼️  {$item['image_url']}\n";
    }
    
    echo "\n🌐 ACCÈS AUX IMAGES STORE:\n";
    echo "========================\n";
    echo "Backend: http://localhost:8000/frontend/src/assets/store1.jpg\n";
    echo "Frontend: http://localhost:5xxx/frontend/src/assets/store1.jpg\n";
    echo "\n✅ Tous les produits store avec leurs vraies images sont prêts !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
