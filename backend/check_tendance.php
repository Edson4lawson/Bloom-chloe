<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT id, name, source FROM products WHERE source = 'tendance'");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>
