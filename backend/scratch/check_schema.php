<?php
require_once __DIR__ . '/../config/db.php';
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(", ", $tables) . "\n";
    
    if (in_array('payments', $tables)) {
        $stmt = $pdo->query("DESCRIBE payments");
        echo "Payments schema: \n";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
