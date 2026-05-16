<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    sendJsonResponse(['error' => 'ID manquant'], 400);
}

try {
    // 1. Infos commande + utilisateur
    $stmt = $pdo->prepare("
        SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email, u.phone
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        sendJsonResponse(['error' => 'Commande non trouvée'], 404);
    }

    // 2. Articles de la commande
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.image_url
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$id]);
    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendJsonResponse($order);

} catch (Exception $e) {
    sendJsonResponse(['error' => 'Erreur serveur', 'message' => $e->getMessage()], 500);
}
?>
