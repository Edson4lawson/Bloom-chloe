<?php
require_once 'config/db.php';
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name LIKE ? OR name LIKE ?");
$stmt->execute(['%Dior%', '%Nibosi%']);
echo "Found: " . $stmt->fetchColumn() . "\n";
?>
