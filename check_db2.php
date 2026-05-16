<?php
require 'backend/config/db.php';
try {
    $stmt = $pdo->query("DESCRIBE products");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
