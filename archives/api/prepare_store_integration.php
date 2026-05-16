<?php
/**
 * Intégration complète des images store
 */

echo "🏪 INTÉGRATION DES IMAGES STORE\n";
echo "=============================\n\n";

// Vérifier les images store disponibles
$storeDir = __DIR__ . '/../frontend/src/assets/';
$storeImages = [];

echo "📁 Vérification des images store disponibles:\n";
for ($i = 1; $i <= 12; $i++) {
    $imageFile = $storeDir . "store$i.jpg";
    if (file_exists($imageFile)) {
        $storeImages[] = [
            'id' => 90 + $i,
            'filename' => "store$i.jpg",
            'path' => "/frontend/src/assets/store$i.jpg",
            'size' => filesize($imageFile)
        ];
        echo "✅ store$i.jpg (" . number_format(filesize($imageFile)) . " bytes)\n";
    }
}

echo "\n📊 Total images store trouvées: " . count($storeImages) . "\n";

// Noms des produits store
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

// Prix et descriptions
$storeProductData = [
    ['price' => 45000, 'desc' => 'Kit complet de soin du visage et du corps avec produits premium'],
    ['price' => 65000, 'desc' => 'Pack beauté premium pour une routine complète haut de gamme'],
    ['price' => 35000, 'desc' => 'Ensemble professionnel coiffure avec accessoires de qualité'],
    ['price' => 55000, 'desc' => 'Set spa maison pour détente et bien-être à domicile'],
    ['price' => 75000, 'desc' => 'Collection wellness avec produits aromathérapie et relaxation'],
    ['price' => 85000, 'desc' => 'Box soirée glamour avec maquillage et accessoires tendance'],
    ['price' => 28000, 'desc' => 'Pack accessoires mode pour compléter votre style'],
    ['price' => 42000, 'desc' => 'Kit maquillage professionnel avec brushes et cosmétiques'],
    ['price' => 38000, 'desc' => 'Set parfumerie avec fragrances exclusives Bloom Chloé'],
    ['price' => 95000, 'desc' => 'Collection luxe avec produits de grande marque'],
    ['price' => 52000, 'desc' => 'Box cadeau parfaite pour toutes les occasions spéciales'],
    ['price' => 68000, 'desc' => 'Pack complet beauté pour routine quotidienne experte']
];

echo "\n🔧 PRÉPARATION DES DONNÉES STORE\n";
echo "===============================\n";

$preparedData = [];
foreach ($storeImages as $index => $image) {
    $preparedData[] = [
        'name' => $storeProductNames[$index],
        'description' => $storeProductData[$index]['desc'],
        'price' => $storeProductData[$index]['price'],
        'image_url' => $image['path'],
        'slug' => strtolower(str_replace([' ', '-'], '-', $storeProductNames[$index])) . '-' . uniqid(),
        'category_id' => ($index % 12) + 1, // Répartir dans les 12 catégories
        'source' => 'store'
    ];
    
    echo "📦 {$preparedData[$index]['name']} - {$preparedData[$index]['price']} FCFA\n";
}

echo "\n📋 INSTRUCTIONS D'INTÉGRATION\n";
echo "============================\n";
echo "1. Démarrer MySQL/Laragon\n";
echo "2. Démarrer le backend: php -S localhost:8000\n";
echo "3. Exécuter: php integrate_store_products.php\n";
echo "4. Vérifier les produits store dans le dashboard\n\n";

echo "🎯 RÉSUMÉ\n";
echo "==========\n";
echo "✅ " . count($storeImages) . " images store prêtes\n";
echo "✅ " . count($preparedData) . " produits configurés\n";
echo "✅ URLs: /frontend/src/assets/storeX.jpg\n";
echo "✅ Prix: 28k - 95k FCFA\n";
echo "✅ Catégories: Réparties dans les 12 catégories\n\n";

echo "🌐 Les produits store seront accessibles via:\n";
echo "   - Backend: http://localhost:8000/frontend/src/assets/store1.jpg\n";
echo "   - Frontend: http://localhost:5xxx/frontend/src/assets/store1.jpg\n";

// Sauvegarder les données pour utilisation ultérieure
file_put_contents(__DIR__ . '/store_products_data.json', json_encode($preparedData, JSON_PRETTY_PRINT));
echo "💾 Données sauvegardées dans store_products_data.json\n";

?>
