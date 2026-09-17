<?php
require_once __DIR__ . '/../../config/headers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Authentifier l'administrateur
$user = authenticate();
if ($user['role'] !== 'admin') {
    sendJsonResponse(['error' => 'Accès refusé. Droits administrateur requis.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

$data = getJsonData();
$order_id = isset($data['order_id']) ? (int)$data['order_id'] : null;
$status = isset($data['status']) ? trim($data['status']) : null;

if (!$order_id || !$status) {
    sendJsonResponse(['error' => 'Données manquantes (order_id et status requis)'], 400);
}

// Normaliser le statut (delivered -> completed pour correspondre à l'enum)
if ($status === 'delivered') {
    $status = 'completed';
}

// Vérifier si le statut est valide
$valid_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    sendJsonResponse(['error' => 'Statut invalide. Statuts acceptés: ' . implode(', ', $valid_statuses)], 400);
}

try {
    $pdo->beginTransaction();

    // 1. Récupérer l'état actuel de la commande
    $stmt = $pdo->prepare("SELECT id, status FROM orders WHERE id = ? FOR UPDATE");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $pdo->rollBack();
        sendJsonResponse(['error' => 'Commande introuvable'], 404);
    }

    $oldStatus = $order['status'];

    // 2. Gestion automatique du stock selon le changement de statut
    if ($oldStatus !== $status) {
        // Récupérer les articles de la commande
        $itemsStmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$order_id]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        // A. Si la commande est annulée -> On réinjecte le stock
        if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
            $restoreStmt = $pdo->prepare("
                UPDATE products 
                SET stock_quantity = stock_quantity + ?,
                    stock = stock + ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            foreach ($items as $item) {
                if (!empty($item['product_id']) && (int)$item['quantity'] > 0) {
                    $restoreStmt->execute([(int)$item['quantity'], (int)$item['quantity'], (int)$item['product_id']]);
                }
            }
        }
        // B. Si une commande annulée est réactivée -> On re-déduit le stock
        elseif ($oldStatus === 'cancelled' && $status !== 'cancelled') {
            $deductStmt = $pdo->prepare("
                UPDATE products 
                SET stock_quantity = GREATEST(0, stock_quantity - ?),
                    stock = GREATEST(0, stock - ?),
                    updated_at = NOW()
                WHERE id = ?
            ");
            foreach ($items as $item) {
                if (!empty($item['product_id']) && (int)$item['quantity'] > 0) {
                    $deductStmt->execute([(int)$item['quantity'], (int)$item['quantity'], (int)$item['product_id']]);
                }
            }
        }
    }

    // 3. Mettre à jour le statut de la commande
    $updateStmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
    $updateStmt->execute([$status, $order_id]);

    $pdo->commit();

    sendJsonResponse([
        'success' => true,
        'message' => 'Statut de la commande mis à jour avec succès',
        'order_id' => $order_id,
        'old_status' => $oldStatus,
        'new_status' => $status
    ]);

} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur update_status: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
}
?>
