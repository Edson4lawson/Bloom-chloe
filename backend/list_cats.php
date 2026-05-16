<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($categories, JSON_PRETTY_PRINT);
?>
