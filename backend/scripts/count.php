<?php
require_once __DIR__ . '/api/config/db.php';
$stmt = $pdo->query('SELECT COUNT(*) as count FROM products');
$result = $stmt->fetch();
echo "TOTAL PRODUCTS: " . $result['count'] . "\n";
