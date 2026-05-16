<?php
require_once 'config/db.php';
$stmt = $pdo->query('SELECT id, name FROM products WHERE id <= 0');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
