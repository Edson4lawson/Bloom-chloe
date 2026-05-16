<?php
/**
 * Liste administrative des commandes
 */

require_once __DIR__ . '/../../config/headers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Authentification + vérification admin
$user = authenticate();
requireAdmin($user);

try {
    $stmt = $pdo->query("SELECT o.*, u.first_name, u.last_name, u.email as user_email 
                         FROM orders o 
                         LEFT JOIN users u ON o.user_id = u.id 
                         ORDER BY o.created_at DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as &$o) {
        $o['total_amount'] = (float)$o['total_amount'];
    }

    sendJsonResponse($orders);

} catch (Exception $e) {
    sendJsonResponse(['error' => $e->getMessage()], 500);
}
