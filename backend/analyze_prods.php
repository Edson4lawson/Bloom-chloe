<?php
require_once 'config/db.php';
$stmt = $pdo->query("
    SELECT p.id, p.name, p.description, c.name as cat_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>
