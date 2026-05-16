<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

// Récupérer les données de la requête
$data = getJsonData();

// Valider les données d'entrée
if (empty($data['product_id'])) {
    sendJsonResponse(['error' => 'ID du produit requis'], 400);
}

$productId = (int)$data['product_id'];
$quantity = isset($data['quantity']) ? max(1, (int)$data['quantity']) : 1;
$userId = $user['id'];

try {
    // Vérifier si le produit existe et est en stock
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare('SELECT id, stock_quantity, price FROM products WHERE id = ? AND status = "published" FOR UPDATE');
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
    
    // Vérifier si le produit est déjà dans le panier
    $stmt = $pdo->prepare('SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$userId, $productId]);
    $existingItem = $stmt->fetch();
    
    if ($existingItem) {
        // Mettre à jour la quantité si le produit est déjà dans le panier
        $newQuantity = $existingItem['quantity'] + $quantity;
        
        // Vérifier à nouveau le stock avec la nouvelle quantité
        if ($product['stock_quantity'] < $newQuantity) {
            $pdo->rollBack();
            sendJsonResponse([
                'error' => 'Quantité demandée non disponible en stock',
                'available_quantity' => $product['stock_quantity'] - $existingItem['quantity']
            ], 400);
        }
        
        $stmt = $pdo->prepare('UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$newQuantity, $existingItem['id']]);
    } else {
        // Ajouter un nouvel article au panier
        $stmt = $pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $productId, $quantity]);
    }
    
    $pdo->commit();
    
    // Récupérer le contenu mis à jour du panier
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM cart WHERE user_id = ?');
    $stmt->execute([$userId]);
    $cartCount = $stmt->fetch()['count'];
    
    sendJsonResponse([
        'message' => 'Produit ajouté au panier',
        'cart_item_count' => (int)$cartCount
    ], 201);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur lors de l\'ajout au panier: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de l\'ajout au panier'], 500);
}
?>
