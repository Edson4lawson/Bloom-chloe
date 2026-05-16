<?php
require_once 'config/db.php';

try {
    echo "Testing connection...\n";
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbName = $stmt->fetchColumn();
    echo "Successfully connected to database: " . $dbName . "\n";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "Table 'users' exists.\n";
    } else {
        echo "Table 'users' does NOT exist.\n";
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
