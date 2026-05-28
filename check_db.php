<?php
require_once __DIR__ . '/backend/config/db.php';

try {
    echo "DB Connection: OK\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in 'users' table:\n";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    echo "Total users: " . $stmt->fetchColumn() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
