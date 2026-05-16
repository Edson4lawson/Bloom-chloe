<?php
/**
 * Récupération des vraies images depuis assets
 */

echo "🖼️ RÉCUPÉRATION DES VRAIES IMAGES ASSETS\n";
echo "=======================================\n\n";

$sourceDir = __DIR__ . '/../frontend/src/assets/';
$targetDir = __DIR__ . '/../images/products/';
$categoryDir = __DIR__ . '/../images/categories/';
$storeDir = __DIR__ . '/../images/stores/';

// Créer les dossiers cibles
foreach ([$targetDir, $categoryDir, $storeDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Dossier créé: " . basename($dir) . "\n";
    }
}

// Copier les images des produits
echo "\n🛍️  Copie des images produits...\n";
$productCount = 0;
for ($i = 1; $i <= 90; $i++) {
    $sourceFile = $sourceDir . "produit$i.jpg";
    $targetFile = $targetDir . "produit$i.jpg";
    
    if (file_exists($sourceFile)) {
        if (copy($sourceFile, $targetFile)) {
            $productCount++;
            echo "   ✅ produit$i.jpg\n";
        }
    }
}

// Copier les images des catégories
echo "\n📂 Copie des images catégories...\n";
$categoryCount = 0;
for ($i = 1; $i <= 12; $i++) {
    $sourceFile = $sourceDir . "categorie$i.jpg";
    $targetFile = $categoryDir . "categorie$i.jpg";
    
    if (file_exists($sourceFile)) {
        if (copy($sourceFile, $targetFile)) {
            $categoryCount++;
            echo "   ✅ categorie$i.jpg\n";
        }
    }
}

// Copier les images store
echo "\n🏪 Copie des images store...\n";
$storeCount = 0;
for ($i = 1; $i <= 12; $i++) {
    $sourceFile = $sourceDir . "store$i.jpg";
    $targetFile = $storeDir . "store$i.jpg";
    
    if (file_exists($sourceFile)) {
        if (copy($sourceFile, $targetFile)) {
            $storeCount++;
            echo "   ✅ store$i.jpg\n";
        }
    }
}

// Mettre à jour la base de données avec les vraies URLs
echo "\n🔧 MISE À JOUR BASE DE DONNÉES\n";
echo "===============================\n";

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");

    // Mettre à jour les URLs des produits (pour les 90 premiers produits)
    $updated = 0;
    for ($i = 1; $i <= 90; $i++) {
        $newUrl = "/images/products/produit$i.jpg";
        $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = ?");
        if ($stmt->execute([$newUrl, $i])) {
            $updated++;
        }
    }
    
    echo "✅ $updated URLs de produits mises à jour\n";

    // Mettre à jour les catégories avec leurs images
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY id LIMIT 12");
    $categories = $stmt->fetchAll();
    
    $catUpdated = 0;
    foreach ($categories as $index => $category) {
        $imageUrl = "/images/categories/categorie" . ($index + 1) . ".jpg";
        $updateStmt = $pdo->prepare("UPDATE categories SET image_url = ? WHERE id = ?");
        if ($updateStmt->execute([$imageUrl, $category['id']])) {
            $catUpdated++;
        }
    }
    
    echo "✅ $catUpdated URLs de catégories mises à jour\n";

} catch (Exception $e) {
    echo "❌ Erreur BDD: " . $e->getMessage() . "\n";
}

// Créer aussi les dossiers pour le frontend
$frontendImages = __DIR__ . '/../frontend/public/images';
if (!is_dir($frontendImages)) {
    mkdir($frontendImages, 0755, true);
}

// Copier vers frontend/public pour accès direct
exec("xcopy \"" . __DIR__ . "/../images\" \"" . $frontendImages . "\" /E /I /Y", $output, $return);

echo "\n📊 RÉCAPITULATIF FINAL\n";
echo "====================\n";
echo "✅ $productCount images produits copiées\n";
echo "✅ $categoryCount images catégories copiées\n";
echo "✅ $storeCount images store copiées\n";
echo "✅ Base de données mise à jour\n";
echo "✅ Images copiées vers frontend/public\n";

echo "\n🌐 URLs d'accès:\n";
echo "   - Produits: http://localhost:8000/images/products/produit1.jpg\n";
echo "   - Catégories: http://localhost:8000/images/categories/categorie1.jpg\n";
echo "   - Store: http://localhost:8000/images/stores/store1.jpg\n";

echo "\n🎯 TES VRAIES IMAGES SONT PRÊTES !\n";

?>
