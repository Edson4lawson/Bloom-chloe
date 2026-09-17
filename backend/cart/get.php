<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier si la requête est de type GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

try {
    // Récupérer le contenu du panier avec les détails des produits (compatible PostgreSQL et MySQL)
    $query = "
        SELECT 
            c.product_id,
            p.name,
            p.slug,
            p.price,
            p.compare_price,
            p.image_url,
            COALESCE(p.stock_quantity, p.stock, 100) as available_quantity,
            c.quantity,
            (p.price * c.quantity) as total_price,
            CASE 
                WHEN COALESCE(p.stock_quantity, p.stock, 100) = 0 THEN 'out_of_stock'
                WHEN COALESCE(p.stock_quantity, p.stock, 100) < c.quantity THEN 'low_stock'
                ELSE 'in_stock'
            END as stock_status
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ? AND p.status = 'published'
        ORDER BY c.updated_at DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user['id']]);
    $cartItems = $stmt->fetchAll();
    
    // Calculer les totaux
    $subtotal = 0;
    $shippingFee = 0;
    $taxRate = 0.20;
    
    foreach ($cartItems as $item) {
        $subtotal += (float)$item['total_price'];
    }
    
    $taxAmount = $subtotal * $taxRate;
    $total = $subtotal + $shippingFee + $taxAmount;
    
    // Formater la réponse
    $response = [
        'items' => $cartItems,
        'summary' => [
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'shipping_fee' => number_format($shippingFee, 2, '.', ''),
            'tax_amount' => number_format($taxAmount, 2, '.', ''),
            'total' => number_format($total, 2, '.', '')
        ],
        'item_count' => count($cartItems)
    ];
    
    sendJsonResponse($response);
    
} catch (PDOException $e) {
    error_log('Erreur lors de la récupération du panier: ' . $e->getMessage());
    sendJsonResponse(['items' => [], 'summary' => ['subtotal' => '0.00', 'total' => '0.00'], 'item_count' => 0]);
} catch (Exception $e) {
    error_log('Erreur générale panier: ' . $e->getMessage());
    sendJsonResponse(['items' => [], 'summary' => ['subtotal' => '0.00', 'total' => '0.00'], 'item_count' => 0]);
}
