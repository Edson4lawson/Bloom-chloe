<?php
/**
 * Création des dossiers d'images et ajout d'images par défaut
 */

echo "🖼️ CRÉATION DES DOSSIERS D'IMAGES\n";
echo "===============================\n\n";

// Créer les dossiers d'images
$imageDirs = [
    'images',
    'images/products',
    'images/thumbnails',
    'frontend/public/images',
    'frontend/public/images/products',
    'frontend/public/images/thumbnails'
];

foreach ($imageDirs as $dir) {
    $fullPath = __DIR__ . '/../' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Dossier créé: $dir\n";
        } else {
            echo "❌ Erreur création dossier: $dir\n";
        }
    } else {
        echo "ℹ️  Dossier existe déjà: $dir\n";
    }
}

echo "\n🎨 CRÉATION D'IMAGES PAR DÉFAUT\n";
echo "================================\n";

// Créer une image placeholder simple avec SVG
function createPlaceholder($filename, $text, $width = 300, $height = 300) {
    $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="#f0f0f0"/>
    <rect width="100%" height="100%" fill="#e0e0e0" opacity="0.5"/>
    <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="14" fill="#666" text-anchor="middle" dominant-baseline="middle">' . $text . '</text>
</svg>';

    $dirs = [
        __DIR__ . '/../images/products/',
        __DIR__ . '/../frontend/public/images/products/'
    ];

    foreach ($dirs as $dir) {
        $filepath = $dir . $filename;
        if (file_put_contents($filepath, $svg)) {
            echo "✅ Image créée: $filename\n";
        }
    }
}

// Créer des images placeholders pour les 20 premiers produits
for ($i = 1; $i <= 20; $i++) {
    createPlaceholder("produit$i.jpg", "Produit $i");
}

// Créer quelques images génériques supplémentaires
createPlaceholder("placeholder-product.jpg", "Image produit");
createPlaceholder("no-image.jpg", "Image non disponible");
createPlaceholder("default-thumb.jpg", "Thumbnail");

echo "\n🔧 MISE À JOUR DES URLS D'IMAGES\n";
echo "================================\n";

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");

    // Mettre à jour les URLs d'images pour qu'elles pointent vers les bons dossiers
    $stmt = $pdo->query("SELECT id, image_url FROM products WHERE image_url IS NOT NULL AND image_url != ''");
    $products = $stmt->fetchAll();

    $updated = 0;
    foreach ($products as $product) {
        // Extraire le nom de fichier de l'URL actuelle
        $filename = basename($product['image_url']);
        
        // Mettre à jour avec le bon chemin
        $newUrl = "/images/products/" . $filename;
        
        $updateStmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = ?");
        if ($updateStmt->execute([$newUrl, $product['id']])) {
            $updated++;
        }
    }

    echo "✅ $updated URLs d'images mises à jour\n";

    // Pour les produits sans image, ajouter une image par défaut
    $stmt = $pdo->query("UPDATE products SET image_url = '/images/products/placeholder-product.jpg' WHERE image_url IS NULL OR image_url = ''");
    echo "✅ Produits sans image mis à jour avec placeholder\n";

} catch (Exception $e) {
    echo "❌ Erreur mise à jour BDD: " . $e->getMessage() . "\n";
}

echo "\n📊 RÉCAPITULATIF\n";
echo "==============\n";
echo "✅ Dossiers d'images créés\n";
echo "✅ Images placeholders générées (20 + génériques)\n";
echo "✅ URLs d'images mises à jour dans la base\n";
echo "\n🌐 Les images seront maintenant accessibles via:\n";
echo "   - http://localhost:8000/images/products/produit1.jpg\n";
echo "   - http://localhost:5xxx/images/products/produit1.jpg\n";
echo "\n🎯 Prochaine étape: Démarrer les serveurs pour tester!\n";

?>
