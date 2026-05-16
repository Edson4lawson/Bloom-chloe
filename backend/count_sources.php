<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT source, COUNT(*) as count FROM products GROUP BY source");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>
