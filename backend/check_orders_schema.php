<?php
require_once 'config/db.php';
echo "ORDERS:\n";
$stmt = $pdo->query("DESCRIBE orders");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
echo "\nPAYMENTS:\n";
$stmt = $pdo->query("DESCRIBE payments");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>
