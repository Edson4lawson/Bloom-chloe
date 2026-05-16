<?php
// backend/admin/stream.php
require_once __DIR__ . '/../config/headers.php'; // CORS
require_once __DIR__ . '/../config/db.php';

// Disable timeout for SSE
set_time_limit(0);

// Forcer le Content-Type pour SSE (doit être après headers.php)
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Important pour Nginx/Apache proxy

// We need a way to check for "real orders". For simplicity, we check if the max order id changed.
$lastOrderId = 0;
try {
    $stmt = $pdo->query("SELECT MAX(id) as max_id FROM orders");
    $lastOrderId = (int) $stmt->fetchColumn();
} catch (Exception $e) {
    // ignore
}

// Some fake activity messages for the dashboard
$fakeActivities = [
    "Une cliente ajoute 'Mini Valise de Maquillage' au panier.",
    "Un visiteur consulte la catégorie 'Accessoire de beauté'.",
    "Une cliente est sur la page de paiement...",
    "Nouveau visiteur depuis Abidjan.",
    "Un avis 5 étoiles vient d'être soumis !",
    "La 'Trousse de Toilette Bloom' est très demandée aujourd'hui."
];

$counter = 0;

while (true) {
    $events = [];

    // 1. Check for real new orders
    try {
        $stmt = $pdo->query("SELECT id, total_amount, CONCAT(u.first_name, ' ', u.last_name) as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id > $lastOrderId ORDER BY o.id ASC");
        $newOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($newOrders as $order) {
            $events[] = [
                'type' => 'new_order',
                'message' => "Nouvelle commande #" . $order['id'] . " de " . $order['user_name'] . " (" . $order['total_amount'] . " FCFA)!"
            ];
            $lastOrderId = $order['id'];
        }
    } catch (Exception $e) {
        // ignore
    }

    // 2. Random fake activity (every ~10-15 seconds)
    if ($counter % 5 == 0) {
        // Just a random simulated live event
        $events[] = [
            'type' => 'activity',
            'message' => $fakeActivities[array_rand($fakeActivities)],
            'visitors' => rand(8, 25) // Fake active visitors count
        ];
    }

    // Send events
    foreach ($events as $event) {
        echo "data: " . json_encode($event) . "\n\n";
    }

    // Output buffer flush
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();

    // Wait 3 seconds before next check
    sleep(3);
    $counter++;
}
?>
