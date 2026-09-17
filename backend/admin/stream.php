<?php
// backend/admin/stream.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// SSE headers FIRST
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Credentials: true');

// Authentifier sans utiliser sendJsonResponse
$token = $_GET['token'] ?? $_GET['access_token'] ?? '';
if (empty($token)) {
    echo "event: auth_error\ndata: {\"error\":\"Token manquant\"}\n\n";
    flush();
    exit();
}

// Vérifier le token
$stmt = $pdo->prepare('
    SELECT u.id, u.email, u.first_name, u.last_name, u.role, r.name as role_name 
    FROM users u 
    LEFT JOIN roles r ON u.role_id = r.id 
    WHERE u.token = ? AND u.token_expires_at > NOW()
');
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "event: auth_error\ndata: {\"error\":\"Token invalide ou expiré\"}\n\n";
    flush();
    exit();
}

$effectiveRole = !empty($user['role_name']) ? $user['role_name'] : $user['role'];
if ($effectiveRole !== 'admin') {
    echo "event: auth_error\ndata: {\"error\":\"Accès refusé. Administrateur uniquement.\"}\n\n";
    flush();
    exit();
}

// Initialiser les repères d'état
$lastOrderId = 0;
$lastOrderUpdate = '';
$lastProductUpdate = '';

try {
    $stmt = $pdo->query("SELECT MAX(id) as max_id, MAX(updated_at) as max_updated FROM orders");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $lastOrderId = (int)($row['max_id'] ?? 0);
    $lastOrderUpdate = $row['max_updated'] ?? '';

    $stmt = $pdo->query("SELECT MAX(updated_at) as max_prod_updated FROM products");
    $lastProductUpdate = $stmt->fetchColumn() ?: '';
} catch (Exception $e) {
    // ignore
}

$counter = 0;
$startTime = time();
$maxExecutionTime = 25; // Reconnexion fluide toutes les 25s

while (time() - $startTime < $maxExecutionTime) {
    $events = [];

    // 1. Vérifier les nouvelles commandes
    try {
        if ($lastOrderId > 0) {
            $stmt = $pdo->prepare("
                SELECT o.id, o.total_amount, o.status,
                       COALESCE(NULLIF(TRIM(CONCAT(u.first_name, ' ', u.last_name)), ''), u.email, 'Client') as user_name 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id > ? 
                ORDER BY o.id ASC
            ");
            $stmt->execute([$lastOrderId]);
            $newOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($newOrders as $order) {
                $events[] = [
                    'type' => 'new_order',
                    'order_id' => $order['id'],
                    'amount' => $order['total_amount'],
                    'message' => "Nouvelle commande #{$order['id']} de {$order['user_name']} ({$order['total_amount']} FCFA)"
                ];
                if ((int)$order['id'] > $lastOrderId) {
                    $lastOrderId = (int)$order['id'];
                }
            }
        }

        // 2. Vérifier les modifications de commandes (statut / paiement)
        $stmt = $pdo->query("SELECT MAX(updated_at) as max_updated FROM orders");
        $currentOrderUpdate = $stmt->fetchColumn() ?: '';
        if ($lastOrderUpdate !== '' && $currentOrderUpdate > $lastOrderUpdate) {
            $events[] = [
                'type' => 'stats_update',
                'reason' => 'order_modified',
                'message' => 'Une commande a été mise à jour.'
            ];
            $lastOrderUpdate = $currentOrderUpdate;
        }

        // 3. Vérifier les modifications de produits / stock
        $stmt = $pdo->query("SELECT MAX(updated_at) as max_prod_updated FROM products");
        $currentProductUpdate = $stmt->fetchColumn() ?: '';
        if ($lastProductUpdate !== '' && $currentProductUpdate > $lastProductUpdate) {
            $events[] = [
                'type' => 'stats_update',
                'reason' => 'stock_or_product_modified',
                'message' => 'Mise à jour de produit ou de stock détectée.'
            ];
            $lastProductUpdate = $currentProductUpdate;
        }

    } catch (Exception $e) {
        // ignore
    }

    // Ping heartbeat régulier
    if ($counter % 4 == 0) {
        $events[] = [
            'type' => 'ping',
            'timestamp' => time()
        ];
    }

    // Envoyer les événements
    foreach ($events as $event) {
        echo "data: " . json_encode($event) . "\n\n";
    }

    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();

    // Attendre 3 secondes avant la vérification suivante
    sleep(3);
    $counter++;
}
?>
