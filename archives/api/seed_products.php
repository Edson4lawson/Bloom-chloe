<?php
/**
 * Script de seed - Insertion des produits dans la base de données
 * 
 * Usage: php backend/seed_products.php
 * 
 * Ce script insère tous les produits statiques (qui étaient dans products.js)
 * dans la table products de la base de données MySQL.
 */

require_once __DIR__ . '/config/db.php';

echo "🌱 Seed des produits Bloom-Chloe...\n\n";

// =============================================================================
// Mapping catégorie name -> id depuis la BDD
// =============================================================================
$catStmt = $pdo->query('SELECT id, name FROM categories');
$categoriesDb = $catStmt->fetchAll();
$categoryMap = [];
foreach ($categoriesDb as $cat) {
    $categoryMap[strtolower(trim($cat['name']))] = $cat['id'];
}

/**
 * Trouve le category_id correspondant à un nom de catégorie
 */
function findCategoryId($categoryName, $categoryMap) {
    $key = strtolower(trim($categoryName));
    if (isset($categoryMap[$key])) {
        return $categoryMap[$key];
    }
    // Recherche partielle
    foreach ($categoryMap as $name => $id) {
        if (strpos($key, $name) !== false || strpos($name, $key) !== false) {
            return $id;
        }
    }
    return null;
}

/**
 * Génère un slug à partir d'un titre
 */
function generateSlug($title, $id) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    if (empty($slug)) {
        $slug = 'produit';
    }
    return $slug . '-' . $id;
}

// =============================================================================
// TOUS LES PRODUITS (extraits de products.js)
// =============================================================================
$products = [
    // === PRODUITS PRINCIPAUX ===
    ['id' => 1, 'title' => 'Vibro masseur', 'category' => 'Bien-être et relaxation', 'price' => 4500, 'rating' => 4.8, 'stock' => 50, 'image' => 'produit1.jpg', 'source' => 'produit', 'description' => 'Massage sur mesure pour un corps zen et des muscles détendus.'],
    ['id' => 2, 'title' => 'Séchoir à main', 'category' => 'Accessoire de coiffure', 'price' => 6500, 'rating' => 4.5, 'stock' => 22, 'image' => 'produit2.jpg', 'source' => 'produit', 'description' => 'Séchez et stylez vos cheveux en un geste avec notre séchoir.'],
    ['id' => 3, 'title' => 'Kit pédicure - manucure', 'category' => 'Soin personnel', 'price' => 7000, 'rating' => 4.9, 'stock' => 38, 'image' => 'produit3.jpg', 'source' => 'produit', 'description' => 'Prenez soin de vos mains et pieds avec nos outils de pro pour une beauté parfaite.'],
    ['id' => 4, 'title' => 'Fer à lisser', 'category' => 'Beauté et soin personnel', 'price' => 5000, 'rating' => 4.3, 'stock' => 30, 'image' => 'produit4.jpg', 'source' => 'produit', 'description' => 'Fer à lisser pour vos cheveux.'],
    ['id' => 5, 'title' => 'Peigne chauffant', 'category' => 'Beauté et soin personnel', 'price' => 5000, 'rating' => 4.7, 'stock' => 42, 'image' => 'produit5.jpg', 'source' => 'produit', 'description' => 'Un peigne chauffant pour des cheveux lisses et stylés.'],
    ['id' => 6, 'title' => 'Sèche-ongles', 'category' => 'Esthétique et soin personnel', 'price' => 5500, 'rating' => 4.2, 'stock' => 25, 'image' => 'produit6.jpg', 'source' => 'produit', 'description' => 'Des ongles secs et parfaits en quelques secondes.'],
    ['id' => 7, 'title' => 'Power bank', 'category' => 'Accessoire tech', 'price' => 7000, 'rating' => 4.6, 'stock' => 58, 'image' => 'produit7.jpg', 'source' => 'produit', 'description' => 'Une charge rapide et durable pour votre téléphone où que vous soyez.'],
    ['id' => 8, 'title' => 'Vibro masseur portable', 'category' => 'Bien-être et relaxation', 'price' => 5000, 'rating' => 5.0, 'stock' => 5, 'image' => 'produit8.jpg', 'source' => 'produit', 'description' => 'Un accessoire indispensable pour vos soirées, alliant élégance et fonctionnalité avec une finition impeccable.'],
    ['id' => 9, 'title' => 'Ceinture anti-douleur menstruelle', 'category' => 'Santé féminine', 'price' => 6500, 'rating' => 4.8, 'stock' => 70, 'image' => 'produit9.jpg', 'source' => 'produit', 'description' => 'Une ceinture anti-douleur menstruelle pour soulager vos douleurs.'],
    ['id' => 10, 'title' => 'Moulinex Golden-Crown', 'category' => 'Accessoire de cuisine', 'price' => 15500, 'rating' => 4.9, 'stock' => 3, 'image' => 'produit10.jpg', 'source' => 'produit', 'description' => 'Moulez vos épices fraîches avec Moulinex pour des saveurs intenses.'],
    ['id' => 11, 'title' => 'Bouilloire', 'category' => 'Accessoire de cuisine', 'price' => 5500, 'rating' => 4.4, 'stock' => 20, 'image' => 'produit11.jpg', 'source' => 'produit', 'description' => 'Une bouilloire pour faciliter votre quotidien.'],
    ['id' => 12, 'title' => 'Gel de douche', 'category' => 'Accessoire de douche', 'price' => 4500, 'rating' => 4.7, 'stock' => 7, 'image' => 'produit12.jpg', 'source' => 'produit', 'description' => 'Un gel de douche doux et naturel.'],
    ['id' => 13, 'title' => 'Découpe-légumes', 'category' => 'Accessoire de cuisine', 'price' => 6000, 'rating' => 4.6, 'stock' => 14, 'image' => 'produit13.jpg', 'source' => 'produit', 'description' => 'Un découpe-légumes rapide et efficace pour des légumes frais et naturels.'],
    ['id' => 14, 'title' => 'Moulinex Silver-Crest', 'category' => 'Accessoire de cuisine', 'price' => 12500, 'rating' => 4.5, 'stock' => 16, 'image' => 'produit14.jpg', 'source' => 'produit', 'description' => 'Moulez vos épices fraîches avec Moulinex pour des saveurs intenses.'],
    ['id' => 15, 'title' => 'Présentoir à miroir', 'category' => 'Accessoire', 'price' => 7000, 'rating' => 4.3, 'stock' => 28, 'image' => 'produit15.jpg', 'source' => 'produit', 'description' => 'Un présentoir rotatif pour une visibilité maximale de vos articles.'],
    ['id' => 16, 'title' => 'Pistolet de massage à plusieurs embouts', 'category' => 'Bien-être et relaxation', 'price' => 7500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit16.jpg', 'source' => 'produit', 'description' => 'Un pistolet de massage à plusieurs embouts pour des massages efficaces et confortables.'],
    ['id' => 17, 'title' => 'Moustiquaire pliable 2/3 places', 'category' => 'Bien-être et santé', 'price' => 7500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit17.jpg', 'source' => 'produit', 'description' => 'Une moustiquaire pliable pour une protection efficace et confortable.'],
    ['id' => 18, 'title' => 'Gourde thermos', 'category' => 'Accessoire', 'price' => 5500, 'rating' => 4.5, 'stock' => 100, 'image' => 'produit18.jpg', 'source' => 'produit', 'description' => 'Votre alliée pour des boissons à la température parfaite.'],
    ['id' => 19, 'title' => 'Siège de bain intime', 'category' => 'Soin personnel', 'price' => 5500, 'rating' => 4.5, 'stock' => 40, 'image' => 'produit19.jpg', 'source' => 'produit', 'description' => 'Un siège de bain intime pour une toilette confortable et hygiénique.'],
    ['id' => 20, 'title' => 'Langue lécheuse', 'category' => 'Bien-être et plaisir personnel', 'price' => 7000, 'rating' => 5.0, 'stock' => 70, 'image' => 'produit20.jpg', 'source' => 'produit', 'description' => 'Explorez vos fantasmes, sensation par sensation pour plus de frissons.'],
    ['id' => 21, 'title' => 'Tondeuse', 'category' => 'Accessoire de beauté', 'price' => 7000, 'rating' => 4.5, 'stock' => 70, 'image' => 'produit21.jpg', 'source' => 'produit', 'description' => 'Une tondeuse pour des cheveux beaux et soignés.'],
    ['id' => 22, 'title' => 'Batteuse électrique', 'category' => 'Accessoire de cuisine', 'price' => 5000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit22.jpg', 'source' => 'produit', 'description' => 'Une alliée de taille pour les pâtissiers.'],
    ['id' => 23, 'title' => 'Miroir', 'category' => 'Accessoire de chambre', 'price' => 13500, 'rating' => 4.5, 'stock' => 45, 'image' => 'produit23.jpg', 'source' => 'produit', 'description' => 'Un miroir de chambre pour savoir qui est le plus beau.'],
    ['id' => 24, 'title' => 'Épilateur électrique', 'category' => 'Soin personnel et beauté', 'price' => 5000, 'rating' => 4.5, 'stock' => 90, 'image' => 'produit24.jpg', 'source' => 'produit', 'description' => 'Un épilateur électrique pour une peau lisse et sexy.'],
    ['id' => 25, 'title' => "Tuyau d'arrosage flexible (30m)", 'category' => 'Jardinage et lavage', 'price' => 6000, 'rating' => 4.5, 'stock' => 50, 'image' => 'produit25.jpg', 'source' => 'produit', 'description' => "Un tuyau d'arrosage flexible pour prendre soin de votre jardin ou laver vos véhicules."],
    ['id' => 26, 'title' => 'Genouillère', 'category' => 'Sport et bien-être', 'price' => 4500, 'rating' => 4.5, 'stock' => 100, 'image' => 'produit26.jpg', 'source' => 'produit', 'description' => 'Une genouillère pour une activité sportive sans gêne.'],
    ['id' => 27, 'title' => "Support d'ordinateur", 'category' => 'Accessoire de bureau', 'price' => 2500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit27.jpg', 'source' => 'produit', 'description' => "Un support d'ordinateur pour travailler sans gêne."],
    ['id' => 28, 'title' => 'Aspirateur 3 en 1', 'category' => 'Ménage', 'price' => 5000, 'rating' => 4.5, 'stock' => 18, 'image' => 'produit28.jpg', 'source' => 'produit', 'description' => 'Un aspirateur 3 en 1 pour un nettoyage efficace et rapide.'],
    ['id' => 29, 'title' => 'Sac à main', 'category' => 'Accessoire de sortie', 'price' => 6000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit29.jpg', 'source' => 'produit', 'description' => 'Un sac à main pour vos sorties.'],
    ['id' => 30, 'title' => 'Sac à main', 'category' => 'Accessoire de sortie', 'price' => 5000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit30.jpg', 'source' => 'produit', 'description' => 'Un sac à main pour vos sorties.'],
    ['id' => 31, 'title' => 'Brosse électrique', 'category' => 'Soin corporel', 'price' => 3000, 'rating' => 4.5, 'stock' => 19, 'image' => 'produit31.jpg', 'source' => 'produit', 'description' => 'Nettoie en profondeur, exfolie en douceur. Réveillez votre peau avec notre brosse visage.'],
    ['id' => 33, 'title' => 'Plaque chauffante à tête noire', 'category' => 'Accessoire de cuisine', 'price' => 6000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit33.jpg', 'source' => 'produit', 'description' => "Cuisinez en un clin d'œil avec notre plaque chauffante rapide et sécurisée !"],
    ['id' => 34, 'title' => 'Plaque chauffante avec tête en fer', 'category' => 'Accessoire de cuisine', 'price' => 6000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit34.jpg', 'source' => 'produit', 'description' => "Cuisinez en un clin d'œil avec notre plaque chauffante rapide et sécurisée !"],
    ['id' => 35, 'title' => 'Plaque chauffante à tête noire double foyer', 'category' => 'Accessoire de cuisine', 'price' => 12000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit35.jpg', 'source' => 'produit', 'description' => "Cuisinez en un clin d'œil avec notre plaque chauffante deux fois plus rapide et sécurisée !"],
    ['id' => 36, 'title' => 'Gourde thermos', 'category' => 'Accessoire', 'price' => 4000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit36.jpg', 'source' => 'produit', 'description' => 'Garde ton café chaud, ton thé froid partout toute la journée.'],
    ['id' => 37, 'title' => 'Range-vêtements 2/3 battants', 'category' => 'Accessoire de chambre', 'price' => 15000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit37.jpg', 'source' => 'produit', 'description' => 'Rangez, organisez, gagnez de la place et maintenez vos vêtements propres.'],
    ['id' => 38, 'title' => 'Moulinex 2 en 1', 'category' => 'Accessoire de cuisine', 'price' => 8000, 'rating' => 4.5, 'stock' => 110, 'image' => 'produit38.jpg', 'source' => 'produit', 'description' => 'Presse, mixe, déguste : les jus de fruits à portée de main.'],
    ['id' => 39, 'title' => 'Armoire 3 battants', 'category' => 'Accessoire de chambre', 'price' => 32000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit39.jpg', 'source' => 'produit', 'description' => 'Rangez, organisez, gagnez de la place et maintenez vos vêtements propres.'],
    ['id' => 40, 'title' => 'Armoire 4 battants', 'category' => 'Collection Bloom', 'price' => 38000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit40.jpg', 'source' => 'produit', 'description' => 'Rangez, organisez, gagnez de la place et maintenez vos vêtements propres.'],
    ['id' => 41, 'title' => 'Louches en silicone (lot de 19 pièces)', 'category' => 'Accessoire de cuisine', 'price' => 12000, 'rating' => 4.5, 'stock' => 70, 'image' => 'produit41.jpg', 'source' => 'produit', 'description' => "Cuisinez sans souci, l'ustensile qui résiste à tout !"],
    ['id' => 42, 'title' => 'Verre à champagne doré', 'category' => 'Accessoire', 'price' => 8000, 'rating' => 4.5, 'stock' => 19, 'image' => 'produit42.jpg', 'source' => 'produit', 'description' => "Brillez en or avec ce verre à champagne doré. L'élégance à chaque toast !"],
    ['id' => 43, 'title' => 'Trépied 360°', 'category' => 'Accessoire de tournage', 'price' => 12000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit43.jpg', 'source' => 'produit', 'description' => "Stabilisez vos clichés, sublimez vos vues. Le trépied qu'il vous faut."],
    ['id' => 44, 'title' => 'Gourde', 'category' => 'Accessoire personnel', 'price' => 3500, 'rating' => 4.5, 'stock' => 90, 'image' => 'produit44.jpg', 'source' => 'produit', 'description' => 'Vos gourdes pour vous hydrater à tout moment.'],
    ['id' => 45, 'title' => 'Trépied 1m70', 'category' => 'Accessoire de tournage', 'price' => 5000, 'rating' => 4.5, 'stock' => 30, 'image' => 'produit45.jpg', 'source' => 'produit', 'description' => "Stabilisez vos clichés, sublimez vos vues. Le trépied qu'il vous faut."],
    ['id' => 46, 'title' => 'Projecteur LED RL-1200/1800', 'category' => 'Accessoire de tournage', 'price' => 12500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit46.jpg', 'source' => 'produit', 'description' => 'Améliorez la qualité de vos photos et vidéos avec une luminosité professionnelle.'],
    ['id' => 47, 'title' => 'Ensemble de couteaux', 'category' => 'Accessoire de cuisine', 'price' => 3000, 'rating' => 4.5, 'stock' => 20, 'image' => 'produit47.jpg', 'source' => 'produit', 'description' => 'Des lames ultra-tranchantes et des manches ergonomiques pour une coupe parfaite à chaque préparation.'],
    ['id' => 48, 'title' => 'Fer à lisser', 'category' => 'Accessoire de beauté', 'price' => 5000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit48.jpg', 'source' => 'produit', 'description' => 'Ayez des cheveux toujours bien soignés.'],
    ['id' => 49, 'title' => 'Mini ventilateur', 'category' => 'Bien-être', 'price' => 3000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit49.jpg', 'source' => 'produit', 'description' => 'Un mini ventilateur idéal pour plus de fraîcheur.'],
    ['id' => 50, 'title' => 'Découpe-légumes', 'category' => 'Accessoire de cuisine', 'price' => 6000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit50.jpg', 'source' => 'produit', 'description' => 'Faites vos salades de fruits en un temps record.'],
    ['id' => 51, 'title' => 'Grille barbecue', 'category' => 'Accessoire de cuisine', 'price' => 10000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit51.jpg', 'source' => 'produit', 'description' => 'Grillez, dorez et savourez vos viandes.'],
    ['id' => 52, 'title' => 'Porte-épices rotatif', 'category' => 'Accessoire de cuisine', 'price' => 12000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit52.jpg', 'source' => 'produit', 'description' => 'Vos épices toutes réunies en un seul lieu.'],
    ['id' => 54, 'title' => 'Hachoir', 'category' => 'Accessoire de cuisine', 'price' => 6500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit54.jpg', 'source' => 'produit', 'description' => "Coupez, hachez et émincez vos légumes en un clin d'œil."],
    ['id' => 55, 'title' => 'Porte-épices rotatif', 'category' => 'Accessoire de cuisine', 'price' => 12000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit55.jpg', 'source' => 'produit', 'description' => 'Vos épices toutes réunies en un même lieu.'],
    ['id' => 56, 'title' => 'Bouilloire en verre', 'category' => 'Accessoire de cuisine', 'price' => 8000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit56.jpg', 'source' => 'produit', 'description' => 'Prenez votre café et thé chaud.'],
    ['id' => 58, 'title' => 'Étagère de douche 5 en 1', 'category' => 'Accessoire de douche', 'price' => 7500, 'rating' => 4.5, 'stock' => 80, 'image' => 'produit58.jpg', 'source' => 'produit', 'description' => 'Rangez, ordonnez et gagnez de la place dans votre douche.'],
    ['id' => 59, 'title' => 'Glacière chauffante', 'category' => 'Accessoire de cuisine', 'price' => 5500, 'rating' => 4.5, 'stock' => 90, 'image' => 'produit59.jpg', 'source' => 'produit', 'description' => 'Chauffez vos plats en tout lieu.'],
    ['id' => 60, 'title' => 'Porte-épices rotatif', 'category' => 'Accessoire de cuisine', 'price' => 10000, 'rating' => 4.5, 'stock' => 189, 'image' => 'produit60.jpg', 'source' => 'produit', 'description' => 'Vos épices toutes au même endroit.'],
    ['id' => 61, 'title' => "Table d'appoint", 'category' => 'Accessoire de bureau', 'price' => 14000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit61.jpg', 'source' => 'produit', 'description' => "Sublimez votre espace avec notre table d'appoint."],
    ['id' => 62, 'title' => 'Gel de douche', 'category' => 'Bien-être et soin de la peau', 'price' => 6500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit62.jpg', 'source' => 'produit', 'description' => 'Votre gel de douche pour une peau lisse et éclatante.'],
    ['id' => 63, 'title' => 'Armoire 2 battants', 'category' => 'Accessoire de chambre', 'price' => 20500, 'rating' => 4.5, 'stock' => 69, 'image' => 'produit63.jpg', 'source' => 'produit', 'description' => 'Rangez, ordonnez et gagnez de la place dans votre chambre.'],
    ['id' => 64, 'title' => 'Ring light 10 pouces', 'category' => 'Accessoire de tournage', 'price' => 5500, 'rating' => 4.5, 'stock' => 80, 'image' => 'produit64.jpg', 'source' => 'produit', 'description' => 'Améliorez la qualité de vos photos et vidéos.'],
    ['id' => 65, 'title' => 'Casque Bluetooth', 'category' => 'Bien-être et relaxation', 'price' => 5000, 'rating' => 4.5, 'stock' => 110, 'image' => 'produit65.jpg', 'source' => 'produit', 'description' => 'Son pur, musique immersive. Plongez dans le beat et isolez-vous.'],
    ['id' => 66, 'title' => 'Découpe-légumes', 'category' => 'Accessoire de cuisine', 'price' => 5500, 'rating' => 4.5, 'stock' => 60, 'image' => 'produit66.jpg', 'source' => 'produit', 'description' => 'Vos légumes en fines tranches prêts à être savourés.'],
    ['id' => 67, 'title' => 'Parfum 4 en 1', 'category' => 'Soin corporel', 'price' => 4500, 'rating' => 4.5, 'stock' => 100, 'image' => 'produit67.jpg', 'source' => 'produit', 'description' => 'Découvrez nos parfums aux fragrances exquises.'],
    ['id' => 68, 'title' => 'Tapis super absorbant', 'category' => 'Accessoire de douche', 'price' => 2000, 'rating' => 4.5, 'stock' => 100, 'image' => 'produit68.jpg', 'source' => 'produit', 'description' => 'Absorbe, sèche, protège.'],
    ['id' => 69, 'title' => 'Hachoir manuel', 'category' => 'Accessoire de cuisine', 'price' => 4000, 'rating' => 4.5, 'stock' => 70, 'image' => 'produit69.jpg', 'source' => 'produit', 'description' => 'Hachez vos légumes, oignons et herbes en quelques secondes.'],
    ['id' => 70, 'title' => 'Four 3 en 1', 'category' => 'Accessoire de cuisine', 'price' => 19500, 'rating' => 4.5, 'stock' => 30, 'image' => 'produit70.jpg', 'source' => 'produit', 'description' => 'Faites vos petits gâteaux en quelques secondes.'],
    ['id' => 71, 'title' => 'Casserole de table lot de 13 pièces', 'category' => 'Accessoire de cuisine', 'price' => 14000, 'rating' => 4.5, 'stock' => 55, 'image' => 'produit71.jpg', 'source' => 'produit', 'description' => 'Servez vos repas dans des casseroles élégantes.'],
    ['id' => 72, 'title' => 'Table pliable', 'category' => 'Accessoire de chambre', 'price' => 5500, 'rating' => 5.0, 'stock' => 60, 'image' => 'produit72.jpg', 'source' => 'produit', 'description' => 'Pratique pour les pique-niques.'],
    ['id' => 73, 'title' => 'Machine à laver pliable', 'category' => 'Accessoire', 'price' => 15000, 'rating' => 4.5, 'stock' => 80, 'image' => 'produit73.jpg', 'source' => 'produit', 'description' => 'Lavez et séchez vos linges où que vous soyez.'],
    ['id' => 74, 'title' => 'Gourde thermos 3 en 1', 'category' => 'Accessoire personnel', 'price' => 7500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit74.jpg', 'source' => 'produit', 'description' => 'Prenez vos cafés chauds, vos thés froids.'],
    ['id' => 75, 'title' => 'Thermos', 'category' => 'Accessoire personnel', 'price' => 4000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit75.jpg', 'source' => 'produit', 'description' => 'Prenez vos cafés et vos thés à la bonne température.'],
    ['id' => 77, 'title' => 'Plaque chauffante à deux têtes en fer', 'category' => 'Accessoire de cuisine', 'price' => 12000, 'rating' => 4.5, 'stock' => 18, 'image' => 'produit77.jpg', 'source' => 'produit', 'description' => "Cuisinez en un clin d'œil avec notre plaque chauffante."],
    ['id' => 79, 'title' => 'Étagère de chaussures', 'category' => 'Accessoire de chambre', 'price' => 13000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit79.jpg', 'source' => 'produit', 'description' => 'Ayez vos chaussures rangées au même endroit.'],
    ['id' => 80, 'title' => 'Thermos à tasses', 'category' => 'Accessoire personnel', 'price' => 4000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit80.jpg', 'source' => 'produit', 'description' => 'Ayez vos infusions chaudes sur vous en tout lieu.'],
    ['id' => 81, 'title' => 'Fer Binatone', 'category' => 'Accessoire de chambre', 'price' => 10500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit81.jpg', 'source' => 'produit', 'description' => "Ayez vos habits bien repassés pour plus d'élégance."],
    ['id' => 83, 'title' => 'Thermos Marado 3L', 'category' => 'Accessoire personnel', 'price' => 10000, 'rating' => 4.5, 'stock' => 710, 'image' => 'produit83.jpg', 'source' => 'produit', 'description' => 'Vos infusions chaudes, notre préoccupation.'],
    ['id' => 84, 'title' => 'Pèse-aliment', 'category' => 'Accessoire de cuisine', 'price' => 4000, 'rating' => 4.5, 'stock' => 60, 'image' => 'produit84.jpg', 'source' => 'produit', 'description' => "Mangez sain c'est bien, et la quantité qu'il faut."],
    ['id' => 85, 'title' => 'Robot pâtissier', 'category' => 'Accessoire de cuisine', 'price' => 15000, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit85.jpg', 'source' => 'produit', 'description' => 'Robot batteur pour les pâtissiers.'],
    ['id' => 86, 'title' => 'Range vaisselle', 'category' => 'Range vaisselle', 'price' => 8000, 'rating' => 4.5, 'stock' => 150, 'image' => 'produit86.jpg', 'source' => 'produit', 'description' => 'Rangez vos vaisselles en un lieu pour une cuisine propre.'],
    ['id' => 87, 'title' => 'Machine à pop-corn', 'category' => 'Accessoire de cuisine', 'price' => 8500, 'rating' => 4.5, 'stock' => 90, 'image' => 'produit87.jpg', 'source' => 'produit', 'description' => "Faites vos pop-corn en moins de temps qu'il ne le faut pour le dire."],
    ['id' => 88, 'title' => 'Cuisinière Roch à miroir 3 foyers', 'category' => 'Accessoire de cuisine', 'price' => 19500, 'rating' => 4.5, 'stock' => 60, 'image' => 'produit88.jpg', 'source' => 'produit', 'description' => 'Cuisinez vos plats avec rapidité.'],
    ['id' => 89, 'title' => 'Porte-manteau', 'category' => 'Accessoire de chambre', 'price' => 15500, 'rating' => 4.5, 'stock' => 10, 'image' => 'produit89.jpg', 'source' => 'produit', 'description' => 'Rangez, ordonnez, gagnez de la place dans votre chambre.'],

    // === STORE (Nouveautés) ===
    ['id' => 101, 'title' => 'Couvre-matelas + taies imperméables', 'category' => 'Accessoire de chambre', 'price' => 7500, 'rating' => 5.0, 'stock' => 5, 'image' => 'store1.jpg', 'source' => 'store', 'description' => "Protège le matelas et les oreillers contre l'eau et les taches. Doux, respirant et très confortable pour un sommeil agréable."],
    ['id' => 102, 'title' => "Table d'appoint (salon / salle à manger)", 'category' => 'Mobilier de maison', 'price' => 14000, 'rating' => 4.9, 'stock' => 10, 'image' => 'store2.jpg', 'source' => 'store', 'description' => 'Table pratique et élégante pour salon ou salle à manger. Idéale pour poser objets, boissons ou décoration.'],
    ['id' => 103, 'title' => 'Carafe + 4 verres', 'category' => 'Art de la table', 'price' => 6500, 'rating' => 4.8, 'stock' => 12, 'image' => 'store3.jpg', 'source' => 'store', 'description' => 'Ensemble pratique et élégant pour servir vos boissons. Parfait pour la maison ou les invités.'],
    ['id' => 104, 'title' => 'Chic gourde Thermos (maintien de température)', 'category' => 'Accessoire personnel', 'price' => 4000, 'rating' => 4.7, 'stock' => 8, 'image' => 'store4.jpg', 'source' => 'store', 'description' => 'Garde les boissons chaudes ou froides pendant plusieurs heures. Design chic, idéale pour le travail ou les déplacements.'],
    ['id' => 105, 'title' => 'Raclette', 'category' => 'Accessoire anti-insecte', 'price' => 3000, 'rating' => 4.9, 'stock' => 86, 'image' => 'store5.jpg', 'source' => 'store', 'description' => "Ustensile efficace pour se débarrasser des insectes. Simple d'utilisation et efficace."],
    ['id' => 106, 'title' => 'Kit perceuse', 'category' => 'Bricolage', 'price' => 10000, 'rating' => 5.0, 'stock' => 44, 'image' => 'store6.jpg', 'source' => 'store', 'description' => 'Kit complet pour bricolage et réparations à domicile. Pratique, robuste et polyvalent.'],
    ['id' => 107, 'title' => 'Serviette de bain compressée', 'category' => 'Accessoire de douche', 'price' => 1000, 'rating' => 4.8, 'stock' => 97, 'image' => 'store7.jpg', 'source' => 'store', 'description' => "Compacte, légère et très absorbante. Idéale pour voyage, sport ou sorties."],
    ['id' => 108, 'title' => 'Chic gourde', 'category' => 'Accessoire personnel', 'price' => 4000, 'rating' => 4.7, 'stock' => 95, 'image' => 'store8.jpg', 'source' => 'store', 'description' => 'Gourde moderne et pratique pour un usage quotidien. Facile à transporter et résistante.'],
    ['id' => 109, 'title' => 'Moulinex à sec', 'category' => 'Accessoire de cuisine', 'price' => 5500, 'rating' => 5.0, 'stock' => 103, 'image' => 'store9.jpg', 'source' => 'store', 'description' => 'Permet de moudre rapidement épices et aliments secs. Pratique et indispensable en cuisine.'],
    ['id' => 110, 'title' => "Agrandisseur d'écran", 'category' => 'Accessoire High-Tech', 'price' => 2600, 'rating' => 4.9, 'stock' => 35, 'image' => 'store10.jpg', 'source' => 'store', 'description' => "Agrandit l'écran du téléphone pour plus de confort visuel. Idéal pour vidéos et films."],
    ['id' => 111, 'title' => 'Étagère de douche', 'category' => 'Accessoire de douche', 'price' => 1500, 'rating' => 4.8, 'stock' => 89, 'image' => 'store11.jpg', 'source' => 'store', 'description' => "Rangement pratique pour accessoires de bain. Facile à installer et résistante à l'humidité."],
    ['id' => 112, 'title' => 'Carafe + verres', 'category' => 'Art de la table', 'price' => 7000, 'rating' => 5.0, 'stock' => 202, 'image' => 'store12.jpg', 'source' => 'store', 'description' => 'Ensemble élégant pour servir toutes vos boissons. Idéal pour la maison ou le bureau.'],
];

// =============================================================================
// INSERTION DANS LA BASE DE DONNÉES
// =============================================================================

$insertStmt = $pdo->prepare('
    INSERT INTO products (id, name, slug, description, price, stock, stock_quantity, image_url, rating, source, category_id, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "published")
    ON DUPLICATE KEY UPDATE 
        name=VALUES(name), description=VALUES(description), price=VALUES(price), 
        stock=VALUES(stock), stock_quantity=VALUES(stock_quantity), image_url=VALUES(image_url),
        rating=VALUES(rating), source=VALUES(source), category_id=VALUES(category_id)
');

$inserted = 0;
$errors = 0;

foreach ($products as $p) {
    $slug = generateSlug($p['title'], $p['id']);
    $categoryId = findCategoryId($p['category'], $categoryMap);
    
    try {
        $insertStmt->execute([
            $p['id'],
            $p['title'],
            $slug,
            $p['description'],
            $p['price'],
            $p['stock'],
            $p['stock'],  // stock_quantity = stock
            $p['image'],
            $p['rating'],
            $p['source'],
            $categoryId
        ]);
        $inserted++;
        echo "  ✅ #{$p['id']} - {$p['title']}\n";
    } catch (PDOException $e) {
        $errors++;
        echo "  ❌ #{$p['id']} - {$p['title']}: {$e->getMessage()}\n";
    }
}

echo "\n========================================\n";
echo "✅ $inserted produits insérés/mis à jour\n";
if ($errors > 0) {
    echo "❌ $errors erreurs\n";
}
echo "========================================\n";
?>
