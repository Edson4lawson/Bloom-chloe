<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

// Vérifier si la requête est de type GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Vérifier si l'ID du produit est fourni
$productId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$productSlug = $_GET['slug'] ?? null;

if (!$productId && !$productSlug) {
    sendJsonResponse(['error' => 'ID ou slug du produit requis'], 400);
}

try {
    // Construire la requête en fonction de l'ID ou du slug
    $query = "
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.status = 'published' AND ";
    
    $params = [];
    
    if ($productId) {
        $query .= 'p.id = ?';
        $params[] = $productId;
    } else {
        $query .= 'p.slug = ?';
        $params[] = $productSlug;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $product = $stmt->fetch();
    
    if (!$product) {
        sendJsonResponse(['error' => 'Produit non trouvé'], 404);
    }
    
    // Récupérer les produits similaires (même catégorie)
    $similarProductsQuery = "
        SELECT id, name, slug, price, image_url 
        FROM products 
        WHERE category_id = ? AND id != ? AND status = 'published' 
        ORDER BY RAND() 
        LIMIT 4
    ";
    
    $similarStmt = $pdo->prepare($similarProductsQuery);
    $similarStmt->execute([$product['category_id'], $product['id']]);
    $similarProducts = $similarStmt->fetchAll();
    
    // Ajouter les produits similaires à la réponse
    $product['similar_products'] = $similarProducts;
    
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
