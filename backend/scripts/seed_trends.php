<?php
/**
 * BLOOM-CHLOE — Seed des produits "Nos Tendances du Moment"
 * Insère les 12 articles tendances affichés dans Categories.vue avec source = 'tendance'
 */

require_once __DIR__ . '/../config/db.php';

echo "=== BLOOM-CHLOE — Seed des Tendances ===\n\n";

if (!isset($pdo)) {
    die("[ERREUR] Impossible de se connecter à la base de données\n");
}

$trends = [
    [
        'id' => 301,
        'name' => 'Montre Nibosi Dorée',
        'category' => 'Accessoire High-Tech',
        'price' => 35000,
        'stock' => 0,
        'image' => 'categorie1.jpg',
        'desc' => "Montre sportive et élégante en acier doré avec cadran noir, date et index lumineux. Étanche 50 m, idéale pour un style moderne et dynamique."
    ],
    [
        'id' => 302,
        'name' => 'Bague chevalière étoile',
        'category' => 'Accessoire de beauté',
        'price' => 15000,
        'stock' => 0,
        'image' => 'categorie2.jpg',
        'desc' => "Chevalière minimaliste en métal doré avec gravure d'étoile. Design moderne et unisexe, parfaite pour un look chic et discret."
    ],
    [
        'id' => 303,
        'name' => 'Parfum Valentino Uomo',
        'category' => 'Collection Bloom',
        'price' => 65000,
        'stock' => 0,
        'image' => 'categorie3.jpg',
        'desc' => "Parfum masculin élégant aux notes boisées et orientales. Son flacon iconique au design prismatique reflète le luxe italien."
    ],
    [
        'id' => 304,
        'name' => 'Parfum Khamrah Lattafa',
        'category' => 'Collection Bloom',
        'price' => 45000,
        'stock' => 0,
        'image' => 'categorie4.jpg',
        'desc' => "Parfum oriental riche et épicé dans un flacon luxueux aux motifs géométriques. Une fragrance intense inspirée de la parfumerie arabe."
    ],
    [
        'id' => 305,
        'name' => 'Bague Chevalière Royale',
        'category' => 'Accessoire de beauté',
        'price' => 18000,
        'stock' => 0,
        'image' => 'categorie5.jpg',
        'desc' => "Chevalière imposante aux gravures baroques détaillées et plateau noir. Un bijou puissant au style royal, symbole de caractère et d'élégance."
    ],
    [
        'id' => 306,
        'name' => 'Bracelet Cubaine Bitcoin',
        'category' => 'Accessoire',
        'price' => 22000,
        'stock' => 0,
        'image' => 'categorie6.jpg',
        'desc' => "Bracelet doré à chaîne cubaine avec médaillon Bitcoin serti de cristaux. Style hip-hop luxueux, parfait pour affirmer une allure moderne et audacieuse."
    ],
    [
        'id' => 307,
        'name' => 'Collier & Boucles d\'Oreilles',
        'category' => 'Accessoire de beauté',
        'price' => 28000,
        'stock' => 0,
        'image' => 'categorie7.jpg',
        'desc' => "Ensemble élégant en or rose composé d'un collier et de boucles d'oreilles assorties. Design goutte serti de cristaux scintillants, parfait pour mariages et occasions spéciales."
    ],
    [
        'id' => 308,
        'name' => 'Boucles d\'Oreilles Perles',
        'category' => 'Accessoire de beauté',
        'price' => 16000,
        'stock' => 0,
        'image' => 'categorie8.jpg',
        'desc' => "Boucles pendantes raffinées associant cristaux baguette lumineux et perles nacrées. Un bijou chic et intemporel pour sublimer vos tenues de jour comme de soirée."
    ],
    [
        'id' => 309,
        'name' => 'Parfum Pure XS',
        'category' => 'Collection Bloom',
        'price' => 55000,
        'stock' => 0,
        'image' => 'categorie9.jpg',
        'desc' => "Fragrance féminine intense et sensuelle dans un flacon sculptural bordeaux, orné d'un serpent doré. Un parfum audacieux qui incarne le luxe et la séduction."
    ],
    [
        'id' => 310,
        'name' => 'Collier Multi-rangs Doré',
        'category' => 'Accessoire de beauté',
        'price' => 19500,
        'stock' => 0,
        'image' => 'categorie10.jpg',
        'desc' => "Collier tendance à trois chaînes superposées avec pendentifs étoile, médaille et cadenas. Style moderne et bohème chic, idéal pour un look élégant au quotidien."
    ],
    [
        'id' => 311,
        'name' => 'Boucles d\'Oreilles Goutte',
        'category' => 'Accessoire de beauté',
        'price' => 12000,
        'stock' => 0,
        'image' => 'categorie11.jpg',
        'desc' => "Puces d'oreilles minimalistes en forme de goutte d'eau. Finition dorée brillante, légères et confortables, parfaites pour un style sobre et intemporel."
    ],
    [
        'id' => 312,
        'name' => 'Parfum J\'adore Dior',
        'category' => 'Collection Bloom',
        'price' => 75000,
        'stock' => 0,
        'image' => 'categorie12.jpg',
        'desc' => "Parfum féminin iconique aux notes florales raffinées. Son flacon amphore doré incarne l'élégance et le luxe intemporel de la maison Dior."
    ]
];

// Récupérer les catégories
$catStmt = $pdo->query("SELECT id, name FROM categories");
$catMap = [];
while ($row = $catStmt->fetch(PDO::FETCH_ASSOC)) {
    $catMap[$row['name']] = $row['id'];
}

$stmt = $pdo->prepare("
    INSERT INTO products (id, category_id, name, slug, description, price, stock, stock_quantity, image_url, rating, source, status)
    VALUES (:id, :cat_id, :name, :slug, :desc, :price, :stock, :stock_qty, :image, 4.9, 'tendance', 'published')
    ON DUPLICATE KEY UPDATE
        category_id=VALUES(category_id),
        name=VALUES(name),
        slug=VALUES(slug),
        description=VALUES(description),
        price=VALUES(price),
        stock=VALUES(stock),
        stock_quantity=VALUES(stock_quantity),
        image_url=VALUES(image_url),
        source='tendance',
        status='published'
");

$inserted = 0;
foreach ($trends as $t) {
    $catId = $catMap[$t['category']] ?? null;
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $t['name']))));
    $slug = trim($slug, '-') . '-' . $t['id'];

    $stmt->execute([
        ':id' => $t['id'],
        ':cat_id' => $catId,
        ':name' => $t['name'],
        ':slug' => $slug,
        ':desc' => $t['desc'],
        ':price' => $t['price'],
        ':stock' => $t['stock'],
        ':stock_qty' => $t['stock'],
        ':image' => $t['image']
    ]);
    $inserted++;
    echo "[+] Tendance: {$t['name']} ({$t['price']} FCFA, stock: {$t['stock']})\n";
}

echo "\n✅ $inserted produits de tendances insérés avec succès !\n";
