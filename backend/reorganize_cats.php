<?php
require_once 'config/db.php';

$new_categories = [
    'Cuisine & Art de la Table',
    'Beauté & Soins Personnels',
    'Maison & Confort',
    'Mode & Prestige',
    'High-Tech & Gadgets',
    'Santé & Bien-être',
    'Entretien & Bricolage'
];

$cat_ids = [];

try {
    $pdo->beginTransaction();

    // 1. Créer les nouvelles catégories
    foreach ($new_categories as $name) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, created_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
        $stmt->execute([$name, $slug, "Catégorie regroupée pour $name"]);
        $cat_ids[$name] = $pdo->lastInsertId();
    }

    // 2. Récupérer tous les produits
    $products = $pdo->query("SELECT id, name, description FROM products")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $p) {
        $name = strtolower($p['name']);
        $desc = strtolower($p['description']);
        $target_cat = null;

        // Logique de mapping par mots-clés
        if (strpos($name, 'moulinex') !== false || strpos($name, 'cuisine') !== false || strpos($name, 'carafe') !== false || strpos($name, 'verre') !== false || strpos($name, 'vaisselle') !== false || strpos($name, 'légumes') !== false || strpos($name, 'bouilloire') !== false || strpos($name, 'couteaux') !== false || strpos($name, 'louche') !== false || strpos($name, 'batteuse') !== false || strpos($name, 'plaque chauffante') !== false) {
            $target_cat = $cat_ids['Cuisine & Art de la Table'];
        } 
        elseif (strpos($name, 'maquillage') !== false || strpos($name, 'pinceau') !== false || strpos($name, 'miroir') !== false || strpos($name, 'lisser') !== false || strpos($name, 'séchoir') !== false || strpos($name, 'beauté') !== false || strpos($name, 'ongles') !== false || strpos($name, 'esthétique') !== false || strpos($name, 'pédicure') !== false || strpos($name, 'manucure') !== false || strpos($name, 'peigne') !== false || strpos($name, 'gel de douche') !== false || strpos($name, 'tondeuse') !== false || strpos($name, 'épilateur') !== false || strpos($name, 'brosse') !== false) {
            $target_cat = $cat_ids['Beauté & Soins Personnels'];
        }
        elseif (strpos($name, 'montre') !== false || strpos($name, 'bague') !== false || strpos($name, 'parfum') !== false || strpos($name, 'collier') !== false || strpos($name, 'bracelet') !== false || strpos($name, 'boucle') !== false || strpos($name, 'bijou') !== false || strpos($name, 'chevalière') !== false || strpos($name, 'sac à main') !== false || strpos($name, 'valise') !== false) {
            $target_cat = $cat_ids['Mode & Prestige'];
        }
        elseif (strpos($name, 'massage') !== false || strpos($name, 'masseur') !== false || strpos($name, 'vibro') !== false || strpos($name, 'santé') !== false || strpos($name, 'bien-être') !== false || strpos($name, 'menstruelle') !== false || strpos($name, 'spa') !== false || strpos($name, 'relaxation') !== false || strpos($name, 'langue lècheuse') !== false || strpos($name, 'genouillère') !== false) {
            $target_cat = $cat_ids['Santé & Bien-être'];
        }
        elseif (strpos($name, 'tech') !== false || strpos($name, 'power bank') !== false || strpos($name, 'écran') !== false || strpos($name, 'téléphone') !== false || strpos($name, 'vidéo') !== false || strpos($name, 'tournage') !== false || strpos($name, 'trépied') !== false || strpos($name, 'projecteur') !== false || strpos($name, 'support') !== false) {
            $target_cat = $cat_ids['High-Tech & Gadgets'];
        }
        elseif (strpos($name, 'perceuse') !== false || strpos($name, 'bricolage') !== false || strpos($name, 'jardin') !== false || strpos($name, 'insecte') !== false || strpos($name, 'raclette') !== false || strpos($name, 'lavage') !== false || strpos($name, 'tuyau') !== false || strpos($name, 'aspirateur') !== false) {
            $target_cat = $cat_ids['Entretien & Bricolage'];
        }
        else {
            // Par défaut dans Maison & Confort pour tout ce qui reste (lit, douche, meubles, etc.)
            $target_cat = $cat_ids['Maison & Confort'];
        }

        if ($target_cat) {
            $update = $pdo->prepare("UPDATE products SET category_id = ? WHERE id = ?");
            $update->execute([$target_cat, $p['id']]);
        }
    }

    // 3. Supprimer les anciennes catégories vides
    // On ne garde que celles qui sont dans notre nouvelle liste d'IDs
    $new_ids_list = implode(',', $cat_ids);
    $pdo->exec("DELETE FROM categories WHERE id NOT IN ($new_ids_list)");

    $pdo->commit();
    echo "Réorganisation terminée avec succès !";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erreur lors de la réorganisation : " . $e->getMessage();
}
?>
