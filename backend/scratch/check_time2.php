<?php
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT NOW() as mysql_now");
    $mysql = $stmt->fetch();
    
    echo json_encode([
        'php_now' => date('Y-m-d H:i:s'),
        'php_expires_calc' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
        'mysql_now' => $mysql['mysql_now'],
        'php_timezone' => date_default_timezone_get()
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
