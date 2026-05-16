<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

// Vérifier si la requête est de type GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Vérifier si l'identifiant est fourni
$identifier = $_GET['identifier'] ?? $_GET['id'] ?? $_GET['slug'] ?? null;

if (!$identifier) {
    sendJsonResponse(['error' => 'Identifiant du produit requis'], 400);
}

try {
    // Construire la requête
    $query = "
        SELECT p.id, p.name, p.slug, p.description, p.price, p.compare_price,
               p.stock, p.stock_quantity, p.image_url, p.gallery_urls, p.rating, p.source, p.status,
               c.id as category_id, c.name as category_name, c.slug as category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE (p.id = ? OR p.slug = ?) AND p.status = 'published'
        LIMIT 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$identifier, $identifier]);
    $product = $stmt->fetch();
    
    if (!$product) {
        sendJsonResponse(['error' => 'Produit non trouvé'], 404);
    }
    
    // Récupérer les produits similaires (même catégorie) - Fallback robuste
    $similarProducts = [];
    $categoryId = $product['category_id'] ?? 0;
    
    if ($categoryId > 0) {
        $similarStmt = $pdo->prepare("
            SELECT id, name, slug, price, image_url 
            FROM products 
            WHERE category_id = ? AND id != ? AND status = 'published' 
            ORDER BY RAND() 
            LIMIT 4
        ");
        $similarStmt->execute([$categoryId, $product['id']]);
        $similarProducts = $similarStmt->fetchAll();
    }
    
    // Si pas assez de produits similaires, compléter avec du hasard (évite les doublons)
    if (count($similarProducts) < 4) {
        $neededCount = 4 - count($similarProducts);
        $excludeIds = [$product['id']];
        foreach ($similarProducts as $p) {
            $excludeIds[] = $p['id'];
        }
        
        $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
        $randomQuery = "
            SELECT id, name, slug, price, image_url 
            FROM products 
            WHERE id NOT IN ($placeholders) AND status = 'published' 
            ORDER BY RAND() 
            LIMIT $neededCount
        ";
        
        $randomStmt = $pdo->prepare($randomQuery);
        $randomStmt->execute($excludeIds);
        $randomProducts = $randomStmt->fetchAll();
        
        $similarProducts = array_merge($similarProducts, $randomProducts);
    }
    
    // Ajouter les produits similaires à la réponse
    $product['similar_products'] = array_slice($similarProducts, 0, 4);
    
    // Si des images supplémentaires sont stockées sous forme de chaîne séparée par des virgules
    if (!empty($product['gallery_urls'])) {
        $product['gallery'] = array_filter(explode(',', $product['gallery_urls']));
    } else {
        $product['gallery'] = [];
    }
    
    // Supprimer le champ gallery_urls qui n'est plus nécessaire
    unset($product['gallery_urls']);
    
    sendJsonResponse($product);
    
} catch (PDOException $e) {
    error_log('Erreur lors de la récupération du produit: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la récupération du produit'], 500);
}
?>
