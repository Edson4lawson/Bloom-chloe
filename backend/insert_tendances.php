<?php
require_once 'config/db.php';

$tendances = [
    ["name" => "Montre Nibosi Doree", "desc" => "Montre sportive et élégante en acier doré avec cadran noir, date et index lumineux. Étanche 50 m, idéale pour un style moderne et dynamique.", "img" => "src/assets/categorie1.jpg", "price" => 45000],
    ["name" => "Bague chevaliere etoile", "desc" => "Chevalière minimaliste en métal doré avec gravure d'étoile. Design moderne et unisexe, parfaite pour un look chic et discret.", "img" => "src/assets/categorie2.jpg", "price" => 15000],
    ["name" => "Parfum Valentino Uomo", "desc" => "Parfum masculin élégant aux notes boisées et orientales. Son flacon iconique au design prismatique reflète le luxe italien.", "img" => "src/assets/categorie3.jpg", "price" => 65000],
    ["name" => "Parfum Khamrah Lattafa", "desc" => "Parfum oriental riche et épicé dans un flacon luxueux aux motifs géométriques. Une fragrance intense inspirée de la parfumerie arabe.", "img" => "src/assets/categorie4.jpg", "price" => 35000],
    ["name" => "Bague Chevalière", "desc" => "Chevalière imposante aux gravures baroques détaillées et plateau noir. Un bijou puissant au style royal, symbole de caractère et d'élégance.", "img" => "src/assets/categorie5.jpg", "price" => 18000],
    ["name" => "Bracelet Cubaine Bitcoin", "desc" => "Bracelet doré à chaîne cubaine avec médaillon Bitcoin serti de cristaux. Style hip-hop luxueux, parfait pour affirmer une allure moderne et audacieuse.", "img" => "src/assets/categorie6.jpg", "price" => 25000],
    ["name" => "Collier & Boucles d'Oreilles", "desc" => "Ensemble élégant en or rose composé d'un collier et de boucles d'oreilles assorties. Design goutte serti de cristaux scintillants, parfait pour mariages et occasions spéciales.", "img" => "src/assets/categorie7.jpg", "price" => 42000],
    ["name" => "Boucles d'Oreilles Perles", "desc" => "Boucles pendantes raffinées associant cristaux baguette lumineux et perles nacrées. Un bijou chic et intemporel pour sublimer vos tenues de jour comme de soirée.", "img" => "src/assets/categorie8.jpg", "price" => 22000],
    ["name" => "Parfum Pure XS", "desc" => "Fragrance féminine intense et sensuelle dans un flacon sculptural bordeaux, orné d'un serpent doré. Un parfum audacieux qui incarne le luxe et la séduction.", "img" => "src/assets/categorie9.jpg", "price" => 58000],
    ["name" => "Collier Multi-rangs Doré", "desc" => "Collier tendance à trois chaînes superposées avec pendentifs étoile, médaille et cadenas. Style moderne et bohème chic, idéal pour un look élégant au quotidien.", "img" => "src/assets/categorie10.jpg", "price" => 28000],
    ["name" => "Boucles d'Oreilles Goutte", "desc" => "Puces d'oreilles minimalistes en forme de goutte d'eau. Finition dorée brillante, légères et confortables, parfaites pour un style sobre et intemporel.", "img" => "src/assets/categorie11.jpg", "price" => 12000],
    ["name" => "Parfum J'adore Dior", "desc" => "Parfum féminin iconique aux notes florales raffinées. Son flacon amphore doré incarne l'élégance et le luxe intemporel de la maison Dior. Parfait pour sublimer votre compagnie", "img" => "src/assets/categorie12.jpg", "price" => 75000]
];

// Catégorie par défaut pour les tendances : "Collection Bloom" (ID 31)
$categoryId = 31;

try {
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, category_id, image_url, source, is_featured, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'tendance', 1, NOW(), NOW())");

    foreach ($tendances as $t) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $t['name'])));
        $stmt->execute([$t['name'], $slug, $t['desc'], $t['price'], $categoryId, $t['img']]);
    }

    echo "Insertion des 12 produits Tendances réussie !\n";
} catch (Exception $e) {
    echo "Erreur d'insertion : " . $e->getMessage() . "\n";
}
?>
