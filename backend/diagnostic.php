<?php
require_once 'config/db.php';

echo "--- DIAGNOSTIC BLOOM-CHLOE ---\n";

// 1. Check DB Connection
try {
    $pdo->query("SELECT 1");
    echo "[OK] Connexion DB\n";
} catch (Exception $e) {
    echo "[ERROR] Connexion DB: " . $e->getMessage() . "\n";
    exit;
}

// 2. Check Tables count
$tables = ['users', 'products', 'orders', 'order_items', 'categories'];
foreach ($tables as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "[INFO] Table '$table': $count lignes\n";
    } catch (Exception $e) {
        echo "[ERROR] Table '$table' manquante ou erreur: " . $e->getMessage() . "\n";
    }
}

// 3. Find Admin User
$admin = $pdo->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    echo "[OK] Admin trouvé: " . $admin['email'] . "\n";
    $token = $admin['token'];
    if (!$token) {
        // Generate a temporary token if needed for testing
        $token = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE users SET token = ?, token_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?")->execute([$token, $admin['id']]);
        echo "[INFO] Nouveau token généré pour test: $token\n";
    } else {
        echo "[INFO] Token existant: $token\n";
    }
} else {
    echo "[ERROR] Aucun utilisateur admin trouvé !\n";
}

// 4. Test summary.php logic
echo "--- TEST summary.php logic ---\n";
try {
    $stats = [];
    $stats['total_products'] = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $stats['orders_count'] = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['customers_count'] = (int)$pdo->query("SELECT COUNT(DISTINCT user_id) FROM orders")->fetchColumn();
    $stats['revenue_total'] = (float)$pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
    
    echo "[OK] Stats calculées: " . json_encode($stats) . "\n";
} catch (Exception $e) {
    echo "[ERROR] Erreur calcul stats: " . $e->getMessage() . "\n";
}

// 5. Test summary.php via HTTP
if (isset($token)) {
    echo "--- TEST summary.php via HTTP ---\n";
    $url = "http://localhost:8080/admin/analytics/summary.php?token=" . $token;
    echo "Appel URL: $url\n";
    $resp = file_get_contents($url);
    echo "Réponse HTTP:\n" . $resp . "\n";
}
