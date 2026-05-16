<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query("
    SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");

sendJsonResponse(["orders" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
?>