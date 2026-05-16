<?php
/**
 * Configuration correcte des chemins d'images
 */

echo "🔧 CONFIGURATION CORRECTE DES IMAGES\n";
echo "==================================\n\n";

echo "❌ PROBLÈME:\n";
echo "Le backend ne peut pas accéder à /src/assets/ (dossier frontend)\n";
echo "Les images doivent être accessibles par le backend ET le frontend\n\n";

echo "✅ SOLUTION:\n";
echo "Utiliser /frontend/src/assets/ comme chemin universel\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");

    // Mettre à jour les produits avec le bon chemin
    $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = ?");
    $updated = 0;
    
    for ($i = 1; $i <= 90; $i++) {
        $newUrl = "/frontend/src/assets/produit$i.jpg";
        if ($stmt->execute([$newUrl, $i])) {
            $updated++;
        }
    }
    
    echo "✅ $updated URLs produits mises à jour vers /frontend/src/assets/\n";

    // Mettre à jour les catégories
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY id LIMIT 12");
    $categories = $stmt->fetchAll();
    
    $catUpdated = 0;
    foreach ($categories as $index => $category) {
        $newUrl = "/frontend/src/assets/categorie" . ($index + 1) . ".jpg";
        $updateStmt = $pdo->prepare("UPDATE categories SET image_url = ? WHERE id = ?");
        if ($updateStmt->execute([$newUrl, $category['id']])) {
            $catUpdated++;
        }
    }
    
    echo "✅ $catUpdated URLs catégories mises à jour vers /frontend/src/assets/\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🌐 URLS FINALES:\n";
echo "Backend: http://localhost:8000/frontend/src/assets/produit1.jpg\n";
echo "Frontend: http://localhost:5xxx/frontend/src/assets/produit1.jpg\n";

echo "\n📋 ARCHITECTURE FINALE:\n";
echo "========================\n";
echo "📁 frontend/src/assets/     ← Tes 117 vraies images\n";
echo "🔗 Backend & Frontend      ← Même chemin /frontend/src/assets/\n";
echo "🗄️  Base de données         ← URLs pointant vers /frontend/src/assets/\n";

echo "\n✨ AVANTAGES:\n";
echo "✅ Une seule source d'images\n";
echo "✅ Accessible par backend ET frontend\n";
echo "✅ Pas de duplication\n";
echo "✅ URLs cohérentes\n";

?>
