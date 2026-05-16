<?php
require_once __DIR__ . '/../config/db.php';
try {
    $stmt = $pdo->query("SHOW INDEX FROM payments");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
