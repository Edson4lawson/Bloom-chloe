<?php
require 'backend/config/db.php';
try {
    $stats = [];
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $stats['orders_count'] = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
    $stats['customers_count'] = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
    $stats['revenue_total'] = (float)$stmt->fetch()['total'];

    $stmt = $pdo->query("
        SELECT o.id, o.total_amount, o.status, o.created_at, CONCAT(u.first_name, ' ', u.last_name) as user_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC LIMIT 5
    ");
    $stats['recent_orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%b') as month, SUM(total_amount) as total
        FROM orders
        WHERE status != 'cancelled' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY YEAR(created_at), MONTH(created_at)
        ORDER BY YEAR(created_at), MONTH(created_at)
    ");
    $stats['monthly_sales'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    print_r($stats);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
