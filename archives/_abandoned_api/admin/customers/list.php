<?php
/**
 * Liste administrative des clients
 */

require_once __DIR__ . '/../../config/headers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Authentification + vérification admin
$user = authenticate();
requireAdmin($user);

try {
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, phone, last_login_at, created_at,
                         (SELECT COUNT(*) FROM orders WHERE user_id = users.id) as order_count,
                         (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = users.id) as total_spent
                         FROM users 
                         WHERE role = 'customer'
                         ORDER BY created_at DESC");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($customers as &$c) {
        $c['order_count'] = (int)$c['order_count'];
        $c['total_spent'] = (float)$c['total_spent'];
    }

    sendJsonResponse($customers);

} catch (Exception $e) {
    sendJsonResponse(['error' => $e->getMessage()], 500);
}
