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
    
    // Calculer les frais de livraison et les taxes
    $shippingFee = $this->calculateShippingFee($subtotal, $data['shipping_address']);
    $taxRate = 0.20; // 20% de TVA
    $taxAmount = $subtotal * $taxRate;
    $totalAmount = $subtotal + $shippingFee + $taxAmount;
    
    // 3. Créer la commande
    $orderNumber = 'ORD-' . strtoupper(uniqid());
    
    $stmt = $pdo->prepare('INSERT INTO orders (
        user_id, order_number, status, payment_status, payment_method,
        subtotal, shipping_fee, tax_amount, total_amount, shipping_address, billing_address, customer_note
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');    
    
    $stmt->execute([
        $user['id'],
        $orderNumber,
        'pending',
        'pending',
        $data['payment_method'],
        $subtotal,
        $shippingFee,
        $taxAmount,
        $totalAmount,
        $data['shipping_address'],
        $data['billing_address'] ?? $data['shipping_address'],
        $data['customer_note'] ?? null
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // 4. Ajouter les articles de la commande
    $orderItemsQuery = 'INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, total_price) VALUES ';
    $orderItemsParams = [];
    $placeholders = [];
    
    foreach ($orderItems as $item) {
        $placeholders[] = '(?, ?, ?, ?, ?, ?)';
        $orderItemsParams = array_merge($orderItemsParams, [
            $orderId,
            $item['product_id'],
            $item['product_name'],
            $item['price'],
            $item['quantity'],
            $item['total']
        ]);
        
        // Mettre à jour le stock
        $updateStockStmt = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?');
        $updateStockStmt->execute([$item['quantity'], $item['product_id']]);
    }
    
    $orderItemsQuery .= implode(', ', $placeholders);
    $stmt = $pdo->prepare($orderItemsQuery);
    $stmt->execute($orderItemsParams);
    
    // 5. Vider le panier
    $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    
    // 6. Créer un enregistrement de paiement
    $paymentStmt = $pdo->prepare('INSERT INTO payments (
        order_id, amount, payment_method, status, payment_details
    ) VALUES (?, ?, ?, ?, ?)');
    
    $paymentDetails = json_encode([
        'payment_method' => $data['payment_method'],
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    $paymentStmt->execute([
        $orderId,
        $totalAmount,
        $data['payment_method'],
        'pending',
        $paymentDetails
    ]);
    
    $pdo->commit();
    
    // 7. Préparer la réponse avec l'URL de paiement
    $paymentUrl = $this->generatePaymentUrl($orderId, $orderNumber, $totalAmount, $data['payment_method']);
    
    sendJsonResponse([
        'message' => 'Commande créée avec succès',
        'order_id' => $orderId,
        'order_number' => $orderNumber,
        'status' => 'pending',
        'payment_url' => $paymentUrl,
        'amount' => number_format($totalAmount, 2, '.', '')
    ], 201);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur lors de la création de la commande: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la création de la commande: ' . $e->getMessage()], 500);
}

/**
 * Calcule les frais de livraison en fonction du montant et de l'adresse
 */
function calculateShippingFee($subtotal, $shippingAddress) {
    // Logique de calcul des frais de livraison
    // Ici, on utilise une logique simplifiée
    
    // Livraison gratuite pour les commandes de plus de 100€
    if ($subtotal >= 100) {
        return 0;
    }
    
    // Frais de livraison fixes de 5.99€
    return 5.99;
}

/**
 * Génère une URL de paiement en fonction de la méthode choisie
 */
function generatePaymentUrl($orderId, $orderNumber, $amount, $paymentMethod) {
    // En production, vous utiliseriez les API de paiement réelles
    // Ici, on retourne une URL factice pour la démo
    
    $baseUrl = 'http://localhost:8000/payment';
    
    switch ($paymentMethod) {
        case 'credit_card':
            return "$baseUrl/credit-card?order_id=$orderId&amount=$amount&order_number=$orderNumber";
        case 'paypal':
            return "$baseUrl/paypal/create-payment?order_id=$orderId&amount=$amount";
        case 'mobile_money':
            return "$baseUrl/mobile-money/initiate?order_id=$orderId&amount=$amount";
        default:
            return "$baseUrl/checkout?order_id=$orderId";
    }
}
?>
