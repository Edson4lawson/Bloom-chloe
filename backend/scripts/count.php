<?php
require_once __DIR__ . '/../config/db.php';

$tables = ['categories', 'products', 'roles', 'permissions', 'role_permissions', 'users', 'two_factor_auth', 'fraud_flags'];
echo "=== STATISTIQUES BASE DE DONNÉES BLOOM-CHLOE ===\n\n";
foreach ($tables as $table) {
    $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    echo str_pad($table, 20) . " : " . $count . " enregistrements\n";
}
echo "\n";

