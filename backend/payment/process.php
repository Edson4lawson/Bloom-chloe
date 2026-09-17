<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données POST (JSON)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Données invalides']);
    exit();
}

// Validation basique
if (!isset($input['amount']) || !isset($input['provider'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Montant et fournisseur requis']);
    exit();
}

$amount = (float)$input['amount'];
$provider = sanitize($input['provider']);
$orderId = isset($input['order_id']) ? (int)$input['order_id'] : null;

// Simulation de traitement paiement
// Dans un cas réel, ici on appellerait l'API Stripe / PayPal / etc.
$success = true;
$transactionId = ($provider === 'cash_on_delivery') 
    ? 'COD-' . strtoupper(substr(uniqid(), -8)) 
    : (($provider === 'transfer') ? 'TRF-' . strtoupper(substr(uniqid(), -8)) : 'TXN-' . strtoupper(uniqid()));

$paymentStatus = 'succeeded';
$orderStatus = 'processing';

if ($provider === 'cash_on_delivery') {
    $paymentStatus = 'pending_delivery';
    $orderStatus = 'pending';
} elseif ($provider === 'transfer') {
    $paymentStatus = 'pending_verification';
    $orderStatus = 'pending';
}

if ($success) {
    try {
        if ($orderId) {
            // Mettre à jour ou insérer le paiement
            $checkStmt = $pdo->prepare("SELECT id FROM payments WHERE order_id = ? ORDER BY id DESC LIMIT 1");
            $checkStmt->execute([$orderId]);
            $existingPayment = $checkStmt->fetch();

            if ($existingPayment) {
                $updatePayStmt = $pdo->prepare("UPDATE payments SET transaction_id = ?, provider = ?, amount = ?, status = ? WHERE id = ?");
                $updatePayStmt->execute([$transactionId, $provider, $amount, $paymentStatus, $existingPayment['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO payments (order_id, transaction_id, provider, amount, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$orderId, $transactionId, $provider, $amount, $paymentStatus]);
            }
             
            // Mettre à jour le statut de la commande
            $updateStmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
            $updateStmt->execute([$orderStatus, $orderId]);
        }

        echo json_encode([
            'success' => true,
            'message' => ($provider === 'cash_on_delivery') ? 'Commande validée pour paiement à la livraison' : (($provider === 'transfer') ? 'Transfert en attente de vérification' : 'Paiement effectué avec succès'),
            'transaction_id' => $transactionId,
            'status' => $paymentStatus
        ]);
    } catch (PDOException $e) {
        error_log("Erreur paiement DB: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de l\'enregistrement du paiement']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Paiement refusé']);
}
?>
