<?php
require_once __DIR__ . '/../../config/headers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Vérifier si la requête est de type GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

// Récupérer les paramètres de requête
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

// Valider les paramètres
$page = max(1, $page);
$perPage = max(1, min(50, $perPage));
$offset = ($page - 1) * $perPage;

try {
    // Si un ID de commande est fourni, récupérer les détails d'une commande spécifique
    if ($orderId) {
        // Récupérer la commande
        $stmt = $pdo->prepare('
            SELECT 
                o.*,
                p.status as payment_status,
                p.transaction_id,
                p.payment_details
            FROM orders o
            LEFT JOIN payments p ON o.id = p.order_id
            WHERE o.id = ? AND o.user_id = ?
        ');
        $stmt->execute([$orderId, $user['id']]);
        $order = $stmt->fetch();
        
        if (!$order) {
            sendJsonResponse(['error' => 'Commande non trouvée'], 404);
        }
        
        // Récupérer les articles de la commande
        $stmt = $pdo->prepare('
            SELECT 
                oi.*,
                p.slug as product_slug,
                p.image_url
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ');
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll();
        
        // Formater la réponse
        $order['items'] = $orderItems;
        
        // Si les détails de paiement sont stockés sous forme de JSON, les décoder
        if (!empty($order['payment_details'])) {
            $order['payment_details'] = json_decode($order['payment_details'], true);
        }
        
        sendJsonResponse($order);
    } 
    // Sinon, récupérer la liste des commandes avec pagination
    else {
        // Compter le nombre total de commandes
        $countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM orders WHERE user_id = ?');
        $countStmt->execute([$user['id']]);
        $total = $countStmt->fetch()['total'];
        
        // Récupérer les commandes avec pagination
        $query = '
            SELECT 
                o.*,
                p.status as payment_status,
                COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN payments p ON o.id = p.order_id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user['id'], $perPage, $offset]);
        $orders = $stmt->fetchAll();
        
        // Formater la réponse avec la pagination
        $response = [
            'data' => $orders,
            'pagination' => [
                'total' => (int)$total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ];
        
        sendJsonResponse($response);
    }
    
} catch (PDOException $e) {
    error_log('Erreur lors de la récupération des commandes: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la récupération des commandes'], 500);
}
?>
