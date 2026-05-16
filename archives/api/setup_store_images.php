<?php
/**
 * Vérification et intégration des images store
 */

require_once __DIR__ . '/config/db.php';

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    echo "🏪 VÉRIFICATION DES IMAGES STORE\n";
    echo "===============================\n\n";
    
    // Vérifier les produits store existants
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE source = 'store'");
    $result = $stmt->fetch();
    echo "📊 Produits store existants: " . $result['count'] . "\n";
    
    // Vérifier les images store disponibles
    $storeDir = __DIR__ . '/../frontend/src/assets/';
    $storeImages = [];
    
    for ($i = 1; $i <= 12; $i++) {
        $imageFile = $storeDir . "store$i.jpg";
        if (file_exists($imageFile)) {
            $storeImages[] = [
                'id' => 90 + $i, // IDs après les 90 produits existants
                'filename' => "store$i.jpg",
                'path' => "/frontend/src/assets/store$i.jpg"
            ];
            echo "✅ store$i.jpg trouvé\n";
        }
    }
    
    echo "\n📋 Images store disponibles: " . count($storeImages) . "\n";
    
    // Créer des produits store si nécessaire
    if ($result['count'] < count($storeImages)) {
        echo "\n🔧 CRÉATION DES PRODUITS STORE MANQUANTS\n";
        echo "====================================\n";
        
        $storeProductNames = [
            'Kit Soin Complet',
            'Pack Beauté Premium',
            'Ensemble Coiffure Pro',
            'Set Spa Maison',
            'Collection Wellness',
            'Box Soirée Glamour',
            'Pack Accessoires Mode',
            'Kit Maquillage Pro',
            'Set Parfumerie',
            'Collection Luxe',
            'Box Cadeau Parfaite',
            'Pack Complet Beauté'
        ];
        
        $inserted = 0;
        foreach ($storeImages as $index => $image) {
            $productName = $storeProductNames[$index] ?? "Store Product " . ($index + 1);
            
            // Vérifier si le produit existe déjà
            $checkStmt = $pdo->prepare("SELECT id FROM products WHERE name = ? AND source = 'store'");
            $checkStmt->execute([$productName]);
            
            if ($checkStmt->rowCount() == 0) {
                $insertStmt = $pdo->prepare("
                    INSERT INTO products (name, description, price, category_id, image_url, slug, source, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, 'store', 1)
                ");
                
                $description = "Produit exclusif de la collection store Bloom Chloé. Qualité premium et design élégant.";
                $price = rand(15000, 85000); // Prix aléatoire entre 15k et 85k
                $categoryId = rand(1, 12); // Catégorie aléatoire
                $slug = strtolower(str_replace(' ', '-', $productName)) . '-' . uniqid();
                
                if ($insertStmt->execute([
                    $productName,
                    $description,
                    $price,
                    $categoryId,
                    $image['path'],
                    $slug
                ])) {
                    $inserted++;
                    echo "✅ Créé: $productName - $price FCFA\n";
                }
            }
        }
        
        echo "\n📈 Produits store créés: $inserted\n";
    }
    
    // Mettre à jour les URLs d'images pour les produits store existants
    echo "\n🔧 MISE À JOUR DES URLs D'IMAGES STORE\n";
    echo "=====================================\n";
    
    $updated = 0;
    foreach ($storeImages as $image) {
        $updateStmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE name LIKE ? AND source = 'store'");
        $productName = "%Store Product " . ($image['id'] - 90) . "%";
        
        if ($updateStmt->execute([$image['path'], $productName])) {
            $updated++;
        }
    }
    
    echo "✅ URLs d'images store mises à jour: $updated\n";
    
    // Afficher le récapitulatif final
    echo "\n📊 RÉCAPITULATIF FINAL STORE\n";
    echo "==========================\n";
    
    $finalStmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE source = 'store'");
    $finalResult = $finalStmt->fetch();
    
    echo "🏪 Total produits store: " . $finalResult['count'] . "\n";
    echo "🖼️  Images store disponibles: " . count($storeImages) . "\n";
    echo "🔗 URLs configurées: /frontend/src/assets/storeX.jpg\n";
    
    // Afficher quelques exemples
    if ($finalResult['count'] > 0) {
        echo "\n📦 Exemples de produits store:\n";
        $exampleStmt = $pdo->query("SELECT name, price, image_url FROM products WHERE source = 'store' LIMIT 5");
        $examples = $exampleStmt->fetchAll();
        
        foreach ($examples as $example) {
            echo "   - {$example['name']} - {$example['price']} FCFA\n";
            echo "     Image: {$example['image_url']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
