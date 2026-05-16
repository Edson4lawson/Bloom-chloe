<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT id, name, stock_quantity FROM products LIMIT 10");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>
