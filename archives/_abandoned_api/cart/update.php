<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier si la requête est de type PUT ou POST
if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

// Récupérer les données de la requête
$data = getJsonData();

// Valider les données d'entrée
if (empty($data['product_id']) || !isset($data['quantity'])) {
    sendJsonResponse(['error' => 'ID du produit et quantité requis'], 400);
}

$productId = (int)$data['product_id'];
$quantity = (int)$data['quantity'];
$userId = $user['id'];

// Valider la quantité
if ($quantity < 1) {
    sendJsonResponse(['error' => 'La quantité doit être supérieure à 0'], 400);
}

try {
    $pdo->beginTransaction();
    
    // Vérifier si le produit est disponible en stock
    $stmt = $pdo->prepare('SELECT stock_quantity FROM products WHERE id = ? AND status = "published" FOR UPDATE');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        $pdo->rollBack();
        sendJsonResponse(['error' => 'Produit non trouvé ou indisponible'], 404);
    }
    
    // Vérifier le stock disponible
    if ($product['stock_quantity'] < $quantity) {
        $pdo->rollBack();
        sendJsonResponse([
            'error' => 'Stock insuffisant',
            'available_quantity' => $product['stock_quantity']
        ], 400);
    }
    
    // Mettre à jour la quantité dans le panier
    $stmt = $pdo->prepare('UPDATE cart SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$quantity, $userId, $productId]);
    
    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        sendJsonResponse(['error' => 'Article non trouvé dans votre panier'], 404);
    }
    
    $pdo->commit();
    
    // Récupérer le contenu mis à jour du panier
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM cart WHERE user_id = ?');
    $stmt->execute([$userId]);
    $cartCount = $stmt->fetch()['count'];
    
    sendJsonResponse([
        'message' => 'Quantité mise à jour',
        'cart_item_count' => (int)$cartCount
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur lors de la mise à jour du panier: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la mise à jour du panier'], 500);
}
?>
