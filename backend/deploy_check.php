<?php
require_once 'config/db.php';
try {
    $stmt = $pdo->query("SELECT id, email, role FROM users WHERE role = 'admin'");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Admins found: " . count($admins) . "\n";
    foreach ($admins as $admin) {
        echo "- {$admin['email']} (ID: {$admin['id']})\n";
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    echo "Total products: " . $stmt->fetch()['total'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
    echo "Total categories: " . $stmt->fetch()['total'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    echo "Total orders: " . $stmt->fetch()['total'] . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
