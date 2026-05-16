<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT p.name FROM products p JOIN categories c ON p.category_id = c.id WHERE c.name = 'Maison & Confort' LIMIT 30");
echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN), JSON_PRETTY_PRINT);
?>
