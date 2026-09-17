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

try {
    $stats = [];

    // 1. Total Produits & Alertes de Stock
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE stock_quantity <= 5 OR stock <= 5");
    $stats['stock_alerts'] = (int)$stmt->fetch()['total'];

    // 2. Total Commandes, Commandes du Jour & En Attente
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $stats['orders_count'] = (int)$stmt->fetch()['total'];
    $stats['total_orders'] = $stats['orders_count'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()");
    $stats['today_orders'] = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = (int)$stmt->fetch()['total'];

    // 3. Total Clients
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
    $stats['customers_count'] = (int)$stmt->fetch()['total'];
    $stats['total_users'] = $stats['customers_count'];
    $stats['total_clients'] = $stats['customers_count'];

    // 4. Chiffre d'Affaires & Panier Moyen
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status != 'cancelled'");
    $stats['revenue_total'] = (float)$stmt->fetch()['total'];
    $stats['total_revenue'] = $stats['revenue_total'];

    $validOrdersCountStmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status != 'cancelled'");
    $validOrdersCount = (int)$validOrdersCountStmt->fetch()['total'];
    $stats['avg_cart'] = $validOrdersCount > 0 ? round($stats['total_revenue'] / $validOrdersCount, 0) : 0;

    // 5. Factures payées / en attente
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status IN ('processing', 'shipped', 'completed')");
    $stats['paid_invoices'] = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_invoices'] = (int)$stmt->fetch()['total'];

    // 6. Répartition par statut de commande
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    $stats['order_status_counts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 7. Commandes récentes (avec nom de client ou invité)
    $stmt = $pdo->query("
        SELECT o.id, o.total_amount, o.status, o.created_at, 
               COALESCE(NULLIF(TRIM(CONCAT(u.first_name, ' ', u.last_name)), ''), u.email, 'Client invité') as user_name,
               u.email as user_email
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC LIMIT 10
    ");
    $stats['recent_orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 8. Produits les plus vendus
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.price, p.image_url, COALESCE(p.stock_quantity, p.stock, 0) as stock, 
               SUM(oi.quantity) as total_sold, SUM(oi.price_at_purchase * oi.quantity) as total_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY p.id, p.name, p.price, p.image_url, p.stock_quantity, p.stock
        ORDER BY total_sold DESC LIMIT 8
    ");
    $stats['top_products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 9. Produits récents
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.slug, p.price, p.image_url, COALESCE(p.stock_quantity, p.stock, 0) as stock, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC LIMIT 6
    ");
    $stats['recent_products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 10. Graphique des ventes dynamique
    $period = $_GET['period'] ?? '6m';
    
    if ($period === '7d' || $period === '30d') {
        $interval = $period === '7d' ? '7 DAY' : '30 DAY';
        $stmt = $pdo->query("
            SELECT DATE_FORMAT(created_at, '%d %b') as month, SUM(total_amount) as total
            FROM orders
            WHERE status != 'cancelled' AND created_at >= DATE_SUB(CURDATE(), INTERVAL $interval)
            GROUP BY DATE(created_at), DATE_FORMAT(created_at, '%d %b')
            ORDER BY DATE(created_at)
        ");
    } else if ($period === '90d') {
        $stmt = $pdo->query("
            SELECT min(DATE_FORMAT(created_at, '%d %b')) as month, SUM(total_amount) as total
            FROM orders
            WHERE status != 'cancelled' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
            GROUP BY YEAR(created_at), WEEK(created_at)
            ORDER BY YEAR(created_at), WEEK(created_at)
        ");
    } else { // 1y or 6m
        $interval = $period === '1y' ? '1 YEAR' : '6 MONTH';
        $stmt = $pdo->query("
            SELECT DATE_FORMAT(created_at, '%b %Y') as month, SUM(total_amount) as total
            FROM orders
            WHERE status != 'cancelled' AND created_at >= DATE_SUB(CURDATE(), INTERVAL $interval)
            GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b %Y')
            ORDER BY YEAR(created_at), MONTH(created_at)
        ");
    }

    $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $salesChart = array_map(function($row) {
        return ['month' => $row['month'], 'revenue' => (float)$row['total'], 'total' => (float)$row['total']];
    }, $salesData);

    $stats['monthly_sales'] = $salesChart;
    $stats['sales_chart'] = $salesChart;

    sendJsonResponse($stats);

} catch (PDOException $e) {
    error_log('Erreur Admin Dashboard: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
}
?>
