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
    sendJsonResponse(['error' => 'Accès refusé'], 403);
}

try {
    $stats = [];

    // Total Products
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = (int)$stmt->fetch()['total'];

    // Total Orders
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $stats['orders_count'] = (int)$stmt->fetch()['total'];

    // Total Clients
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
    $stats['customers_count'] = (int)$stmt->fetch()['total'];

    // Total Revenue
    $stmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
    $stats['revenue_total'] = (float)($stmt->fetch()['total'] ?? 0);

    // Order Status Breakdown (ALL orders)
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    $stats['order_status_counts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent Orders (last 10)
    $stmt = $pdo->query("
        SELECT o.id, o.total_amount, o.status, o.created_at, CONCAT(u.first_name, ' ', u.last_name) as user_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC LIMIT 10
    ");
    $stats['recent_orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Real Top Selling Products
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.price, p.image_url, SUM(oi.quantity) as total_sold, SUM(oi.price_at_purchase * oi.quantity) as total_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY p.id, p.name, p.price, p.image_url
        ORDER BY total_sold DESC LIMIT 8
    ");
    $stats['top_products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent Products (for the dashboard preview)
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.slug, p.price, p.image_url, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC LIMIT 6
    ");
    $stats['recent_products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monthly Sales (for chart - last 6 months)
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%b') as month, SUM(total_amount) as total
        FROM orders
        WHERE status != 'cancelled' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b')
        ORDER BY YEAR(created_at), MONTH(created_at)
    ");
    $monthlySales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['monthly_sales'] = array_map(function($row) {
        return ['month' => $row['month'], 'revenue' => (float)$row['total']];
    }, $monthlySales);

    sendJsonResponse($stats);

} catch (PDOException $e) {
    error_log('Erreur Admin Dashboard: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur serveur'], 500);
}
?>
