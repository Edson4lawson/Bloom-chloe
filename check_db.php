<?php
require_once __DIR__ . '/backend/config/db.php';

try {
    echo "DB Connection: OK\n";
    $users = $pdo->query("SELECT id, email, password, role, failed_login_attempts, locked_until, two_factor_required FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        echo "User ID: {$u['id']} | Email: {$u['email']} | Role: {$u['role']} | Failed attempts: {$u['failed_login_attempts']} | Locked: {$u['locked_until']}\n";
        echo "Password matches 'admin123': " . (password_verify('admin123', $u['password']) ? 'YES' : 'NO') . "\n";
        echo "Password matches 'Admin123!': " . (password_verify('Admin123!', $u['password']) ? 'YES' : 'NO') . "\n";
        echo "Password matches 'password123': " . (password_verify('password123', $u['password']) ? 'YES' : 'NO') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
