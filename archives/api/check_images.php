<?php
/**
 * Vérification des images des produits
 */

require_once __DIR__ . '/config/db.php';

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    echo "🔍 VÉRIFICATION DES IMAGES DES PRODUITS\n";
    echo "====================================\n\n";
    
    // Récupérer tous les produits avec leurs images
    $stmt = $pdo->query("
        SELECT id, name, image_url, source 
        FROM products 
        ORDER BY id 
        LIMIT 20
    ");
    $products = $stmt->fetchAll();
    
    echo "📊 Analyse des 20 premiers produits:\n\n";
    
    $withImages = 0;
    $withThumbnails = 0;
    $totalChecked = count($products);
    
    foreach ($products as $product) {
        echo "🛍️  ID: {$product['id']} - {$product['name']}\n";
        echo "   🖼️  Image URL: " . ($product['image_url'] ?? 'NULL') . "\n";
        
        // Vérifier si les chemins d'images existent
        if ($product['image_url']) {
            $imagePath = __DIR__ . '/../' . $product['image_url'];
            if (file_exists($imagePath)) {
                echo "   ✅ Image trouvée: " . $imagePath . "\n";
                $withImages++;
            } else {
                echo "   ❌ Image manquante: " . $imagePath . "\n";
            }
        } else {
            echo "   ⚠️  Pas d'image URL définie\n";
        }
        
        echo "\n";
    }
    
    // Statistiques
    echo "📈 STATISTIQUES:\n";
    echo "   - Produits vérifiés: $totalChecked\n";
    echo "   - Avec images: $withImages\n";
    echo "   - Taux images: " . round(($withImages / $totalChecked) * 100, 1) . "%\n\n";
    
    // Vérifier les dossiers d'images
    echo "📁 VÉRIFICATION DES DOSSIERS D'IMAGES:\n";
    $imageDirs = [
        'images',
        'assets/images', 
        'public/images',
        'storage/images',
        'uploads/images',
        'img'
    ];
    
    foreach ($imageDirs as $dir) {
        $fullPath = __DIR__ . '/../' . $dir;
        if (is_dir($fullPath)) {
            $files = glob($fullPath . '/*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE);
            echo "   ✅ $dir : " . count($files) . " fichiers\n";
        } else {
            echo "   ❌ $dir : dossier inexistant\n";
        }
    }
    
    echo "\n🎯 RECOMMANDATIONS:\n";
    if ($withImages < $totalChecked * 0.5) {
        echo "   - Plus de 50% des images manquent\n";
        echo "   - Créer les dossiers d'images manquants\n";
        echo "   - Ajouter des images par défaut\n";
    }
    
    if ($withImages == 0) {
        echo "   - Aucune image trouvée\n";
        echo "   - Utiliser des placeholders ou images par défaut\n";
        echo "   - Mettre à jour les URLs d'images dans la base\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
