<?php
$host = 'localhost';
$db   = 'bloom_chloe';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->query("SELECT email, token, token_expires_at, NOW() as current_time FROM users WHERE email = 'admin@bloom-chloe.com'");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($admins, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
