<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
echo json_encode(["orders" => $pdo->query("SELECT * FROM orders")->fetchAll()]);
?>