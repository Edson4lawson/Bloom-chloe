<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE source = 'store'");
echo "Store products: " . $stmt->fetch()['count'] . "\n";
$stmt = $pdo->query("SELECT id, name FROM products WHERE source = 'store' LIMIT 10");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>
