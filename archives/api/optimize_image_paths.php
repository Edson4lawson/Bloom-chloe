<?php
/**
 * Analyse et optimisation des chemins d'images
 */

echo "🔍 ANALYSE DES CHEMINS D'IMAGES\n";
echo "===============================\n\n";

// Vérifier la configuration actuelle
echo "1️⃣ ÉTAT ACTUEL DES IMAGES\n";
echo "--------------------------\n";

$paths = [
    'frontend/src/assets/' => 'Assets (développement)',
    'images/' => 'Images (backend)',
    'frontend/public/images/' => 'Public (production)'
];

foreach ($paths as $path => $description) {
    $fullPath = __DIR__ . '/../' . $path;
    if (is_dir($fullPath)) {
        $files = glob($fullPath . '*.{jpg,jpeg,png,gif,svg}', GLOB_BRACE);
        echo "✅ $description : " . count($files) . " fichiers\n";
    } else {
        echo "❌ $description : dossier inexistant\n";
    }
}

echo "\n2️⃣ PROBLÈME IDENTIFIÉ\n";
echo "-------------------\n";
echo "❌ Double stockage des images\n";
echo "❌ URLs incorrectes dans la base de données\n";
echo "❌ Confusion entre assets et public\n";

echo "\n3️⃣ SOLUTION OPTIMALE\n";
echo "-------------------\n";
echo "🎯 Utiliser directement les images depuis assets\n";
echo "🎯 Mettre à jour les URLs dans la base de données\n";
echo "🎯 Supprimer le dossier images/ dupliqué\n";

echo "\n4️⃣ MISE À JOUR DES URLs\n";
echo "-----------------------\n";

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");

    // Mettre à jour les produits pour utiliser /src/assets/
    $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = ?");
    $updated = 0;
    
    for ($i = 1; $i <= 90; $i++) {
        $newUrl = "/src/assets/produit$i.jpg";
        if ($stmt->execute([$newUrl, $i])) {
            $updated++;
        }
    }
    
    echo "✅ $updated URLs produits mises à jour vers /src/assets/\n";

    // Mettre à jour les catégories
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY id LIMIT 12");
    $categories = $stmt->fetchAll();
    
    $catUpdated = 0;
    foreach ($categories as $index => $category) {
        $newUrl = "/src/assets/categorie" . ($index + 1) . ".jpg";
        $updateStmt = $pdo->prepare("UPDATE categories SET image_url = ? WHERE id = ?");
        if ($updateStmt->execute([$newUrl, $category['id']])) {
            $catUpdated++;
        }
    }
    
    echo "✅ $catUpdated URLs catégories mises à jour vers /src/assets/\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n5️⃣ CONFIGURATION VITE RECOMMANDÉE\n";
echo "--------------------------------\n";
echo "Ajouter dans vite.config.js :\n";
echo "```\n";
echo "server: {\n";
echo "  fs: {\n";
echo "    allow: ['..']\n";
echo "  }\n";
echo "}\n";
echo "```\n";

echo "\n6️⃣ AVANTAGES DE CETTE APPROCHE\n";
echo "-----------------------------\n";
echo "✅ Plus de duplication d'images\n";
echo "✅ Images directement accessibles depuis assets\n";
echo "✅ Meilleure organisation du projet\n";
echo "✅ Réduction de la taille du projet\n";
echo "✅ URLs plus logiques et cohérentes\n";

echo "\n🎯 CONCLUSION\n";
echo "===========\n";
echo "Le dossier images/ n'est plus nécessaire.\n";
echo "Utilise directement /src/assets/ pour toutes les images.\n";

?>
