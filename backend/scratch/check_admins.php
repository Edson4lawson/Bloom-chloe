<?php
$host = 'localhost';
$db   = 'bloom_chloe';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->query("SELECT email, role FROM users WHERE role = 'admin'");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($admins);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
