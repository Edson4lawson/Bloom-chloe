<?php
/**
 * BLOOM-CHLOE — Seed des produits de la section STORE
 * Ajoute les produits spécifiques avec la source 'store'
 */

require_once __DIR__ . '/api/config/db.php';

echo "=== BLOOM-CHLOE — Seed de la Section Store ===\n\n";

if (!isset($pdo)) {
    die("[ERREUR] Connexion BDD impossible\n");
}

// Récupérer les catégories
$catStmt = $pdo->query("SELECT id, name FROM categories");
$catMap = [];
while($row = $catStmt->fetch(PDO::FETCH_ASSOC)) {
    $catMap[$row['name']] = $row['id'];
}

$storeProducts = [
    ['name' => 'Mini Valise de Maquillage', 'category' => 'Accessoire de beauté', 'price' => 25000, 'image' => 'store1.jpg', 'desc' => 'Une valise compacte et élégante pour transporter tous vos essentiels beauté.'],
    ['name' => 'Set de Pinceaux Luxe', 'category' => 'Beauté et soin personnel', 'price' => 15000, 'image' => 'store2.jpg', 'desc' => '12 pinceaux professionnels en poils synthétiques ultra-doux.'],
    ['name' => 'Miroir LED Tactile', 'category' => 'Accessoire de beauté', 'price' => 12500, 'image' => 'store3.jpg', 'desc' => 'Miroir avec éclairage ajustable pour un maquillage parfait.'],
    ['name' => 'Organisateur Acrylique', 'category' => 'Accessoire', 'price' => 8500, 'image' => 'store4.jpg', 'desc' => 'Rangement transparent pour produits de beauté et bijoux.'],
    ['name' => 'Trousse de Toilette Bloom', 'category' => 'Collection Bloom', 'price' => 5500, 'image' => 'store5.jpg', 'desc' => 'Trousse élégante et imperméable aux couleurs de Bloom.'],
    ['name' => 'Kit Spa Maison', 'category' => 'Bien-être', 'price' => 18000, 'image' => 'store6.jpg', 'desc' => 'Tout le nécessaire pour une soirée détente à la maison.'],
];

$stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, stock, stock_quantity, image_url, rating, source, status) 
    VALUES (:cat_id, :name, :slug, :desc, :price, 50, 50, :image, 4.8, 'store', 'published')
    ON DUPLICATE KEY UPDATE source='store', image_url=VALUES(image_url)");

$count = 0;
foreach ($storeProducts as $p) {
    $catId = $catMap[$p['category']] ?? 1;
    $slug = 'store-' . strtolower(preg_replace('/[^a-z0-9]+/i', '-', $p['name']));
    
    $stmt->execute([
        ':cat_id' => $catId,
        ':name' => $p['name'],
        ':slug' => $slug,
        ':desc' => $p['desc'],
        ':price' => $p['price'],
        ':image' => 'src/assets/' . $p['image']
    ]);
    $count++;
    echo "[+] Store: {$p['name']}\n";
}

echo "\n✅ Successfully added $count store products!\n";
