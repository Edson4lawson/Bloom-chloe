<?php
require_once 'config/db.php';
$stmt = $pdo->query("
    SELECT c.name, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id, c.name 
    ORDER BY c.name
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results, JSON_PRETTY_PRINT);
?>
