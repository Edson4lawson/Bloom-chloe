<?php
require_once __DIR__ . '/../../config/headers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Vérifier si la requête est de type GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'administrateur
$user = authenticate();
if ($user['role'] !== 'admin') {
    sendJsonResponse(['error' => 'Accès refusé. Droits administrateur requis.'], 403);
}

// Récupérer les paramètres de requête
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : null);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
$status = !empty($_GET['status']) ? trim($_GET['status']) : null;
$userId = !empty($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$search = !empty($_GET['search']) ? trim($_GET['search']) : null;
$date = !empty($_GET['date']) ? trim($_GET['date']) : null;

// Valider les paramètres
$page = max(1, $page);
$perPage = max(1, min(100, $perPage));
$offset = ($page - 1) * $perPage;

try {
    // Si un ID de commande est fourni, récupérer les détails d'une commande spécifique
    if ($orderId) {
        $stmt = $pdo->prepare('
            SELECT 
                o.id,
                o.user_id,
                o.total_amount,
                o.status,
                o.shipping_address,
                o.shipping_fee,
                o.tax_amount,
                o.notes,
                o.created_at,
                o.updated_at,
                COALESCE(NULLIF(TRIM(CONCAT(u.first_name, " ", u.last_name)), ""), u.email, "Client") as user_name,
                u.email as user_email,
                u.phone as user_phone,
                p.status as payment_status,
                p.transaction_id,
                p.provider as payment_provider,
                p.metadata as payment_metadata
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN payments p ON o.id = p.order_id
            WHERE o.id = ?
        ');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            sendJsonResponse(['error' => 'Commande non trouvée'], 404);
        }
        
        // Récupérer les articles de la commande
        $stmt = $pdo->prepare('
            SELECT 
                oi.id,
                oi.order_id,
                oi.product_id,
                oi.product_name,
                oi.quantity,
                oi.price_at_purchase as price,
                p.slug as product_slug,
                p.image_url
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ');
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $order['items'] = $orderItems;
        $order['canal'] = 'site';
        
        if (!empty($order['payment_metadata'])) {
            $order['payment_details'] = json_decode($order['payment_metadata'], true);
        }
        
        sendJsonResponse([
            'success' => true,
            'order' => $order
        ]);
    } 
    // Liste de toutes les commandes avec filtres et pagination
    else {
        $whereConditions = [];
        $params = [];

        if ($userId) {
            $whereConditions[] = 'o.user_id = ?';
            $params[] = $userId;
        }

        if ($status) {
            $whereConditions[] = 'o.status = ?';
            $params[] = $status;
        }

        if ($date) {
            $whereConditions[] = 'DATE(o.created_at) = ?';
            $params[] = $date;
        }

        if ($search) {
            $whereConditions[] = '(o.id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR o.shipping_address LIKE ?)';
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Compter le total
        $countQuery = "SELECT COUNT(*) as total FROM orders o LEFT JOIN users u ON o.user_id = u.id $whereClause";
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Récupérer les commandes
        $query = "
            SELECT 
                o.id,
                o.user_id,
                o.total_amount,
                o.status,
                o.shipping_address,
                o.shipping_fee,
                o.notes,
                o.created_at,
                o.updated_at,
                COALESCE(NULLIF(TRIM(CONCAT(u.first_name, ' ', u.last_name)), ''), u.email, 'Client') as user_name,
                u.email as user_email,
                u.phone as user_phone,
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count,
                (SELECT p.status FROM payments p WHERE p.order_id = o.id ORDER BY p.id DESC LIMIT 1) as payment_status
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            $whereClause
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $pdo->prepare($query);
        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex++, $param);
        }
        $stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ajouter canal par défaut
        foreach ($orders as &$ord) {
            $ord['canal'] = 'site';
        }
        
        sendJsonResponse([
            'success' => true,
            'orders' => $orders,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / max(1, $perPage)),
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $total)
            ]
        ]);
    }
    
} catch (PDOException $e) {
    error_log('Erreur lors de la récupération des commandes admin: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la récupération des commandes: ' . $e->getMessage()], 500);
}
?>
