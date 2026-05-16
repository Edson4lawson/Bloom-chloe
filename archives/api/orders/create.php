<?php
require_once __DIR__ . '/../../config/headers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

// Récupérer les données de la requête
$data = getJsonData();

// Valider les données d'entrée
$requiredFields = ['shipping_address', 'payment_method'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        sendJsonResponse(['error' => 'Tous les champs sont obligatoires'], 400);
    }
}

// Valider la méthode de paiement
$allowedPaymentMethods = ['credit_card', 'paypal', 'mobile_money'];
if (!in_array($data['payment_method'], $allowedPaymentMethods)) {
    sendJsonResponse(['error' => 'Méthode de paiement non valide'], 400);
}

try {
    $pdo->beginTransaction();
    
    // 1. Récupérer le panier de l'utilisateur avec les détails des produits
    $cartQuery = "
        SELECT 
            c.product_id,
            p.name as product_name,
            p.price,
            p.stock_quantity as available_quantity,
            c.quantity as requested_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ? AND p.status = 'published'
        FOR UPDATE
    ";
    
    $stmt = $pdo->prepare($cartQuery);
    $stmt->execute([$user['id']]);
    $cartItems = $stmt->fetchAll();
    
    if (empty($cartItems)) {
        $pdo->rollBack();
        sendJsonResponse(['error' => 'Votre panier est vide'], 400);
    }
    
    // 2. Vérifier la disponibilité des produits et calculer le total
    $subtotal = 0;
    $orderItems = [];
    
    foreach ($cartItems as $item) {
        if ($item['available_quantity'] < $item['requested_quantity']) {
            $pdo->rollBack();
            sendJsonResponse([
                'error' => 'Stock insuffisant pour le produit: ' . $item['product_name'],
                'product_id' => $item['product_id'],
                'available_quantity' => $item['available_quantity'],
                'requested_quantity' => $item['requested_quantity']
            ], 400);
        }
        
        $itemTotal = $item['price'] * $item['requested_quantity'];
        $subtotal += $itemTotal;
        
        $orderItems[] = [
            'product_id' => $item['product_id'],
            'product_name' => $item['product_name'],
            'price' => $item['price'],
            'quantity' => $item['requested_quantity'],
            'total' => $itemTotal
        ];
    }
    
    // Calculer les frais de livraison
    $shippingFee = calculateShippingFee($subtotal, $data['shipping_address']);
    $totalAmount = $subtotal + $shippingFee;
    
    // 3. Créer la commande
    $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -8));
    
    $stmt = $pdo->prepare('INSERT INTO orders (
        user_id, total_amount, status, shipping_address, shipping_fee, tax_amount, notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?)');    
    
    $stmt->execute([
        $user['id'],
        $totalAmount,
        'pending',
        $data['shipping_address'],
        $shippingFee,
        0, // Pas de taxe par défaut ou déjà incluse
        $data['customer_note'] ?? null
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // 4. Ajouter les articles de la commande
    foreach ($orderItems as $item) {
        $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, price_at_purchase) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $orderId,
            $item['product_id'],
            $item['product_name'],
            $item['quantity'],
            $item['price']
        ]);
        
        // Mettre à jour le stock
        $updateStockStmt = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ?, stock = stock - ? WHERE id = ?');
        $updateStockStmt->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
    }
    
    // 5. Vider le panier
    $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    
    // 6. Créer un enregistrement de paiement
    $paymentStmt = $pdo->prepare('INSERT INTO payments (
        order_id, amount, provider, status, metadata
    ) VALUES (?, ?, ?, ?, ?)');
    
    $paymentData = [
        'provider' => $data['payment_method'],
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $paymentStmt->execute([
        $orderId,
        $totalAmount,
        $data['payment_method'],
        'pending',
        json_encode($paymentData)
    ]);
    
    $pdo->commit();
    
    sendJsonResponse([
        'message' => 'Commande créée avec succès',
        'order_id' => $orderId,
        'order_number' => $orderNumber,
        'status' => 'pending',
        'amount' => $totalAmount
    ], 201);
    
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur lors de la création de la commande: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la création de la commande: ' . $e->getMessage()], 500);
}

/**
 * Calcule les frais de livraison en fonction du montant et de l'adresse
 */
function calculateShippingFee($subtotal, $shippingAddress) {
    // 2000 Fcfa par défaut au Bénin si moins de 50000 Fcfa
    if ($subtotal >= 50000) {
        return 0;
    }
    return 2000;
}
?>
?>
