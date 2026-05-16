<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'M├⌐thode non autoris├⌐e']);
    exit();
}

// R├⌐cup├⌐rer les donn├⌐es POST (JSON)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Donn├⌐es invalides']);
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
// Dans un cas r├⌐el, ici on appellerait l'API Stripe / PayPal / etc.
$success = true; // Simuler un succ├¿s par d├⌐faut
$transactionId = 'TXN-' . strtoupper(uniqid());

if ($success) {
    try {
        // Enregistrer le paiement en base
        $stmt = $pdo->prepare("INSERT INTO payments (order_id, transaction_id, provider, amount, status) VALUES (?, ?, ?, ?, 'succeeded')");
        // Note: order_id devrait ├¬tre valide. Pour ce test, on suppose qu'il existe ou on g├¿re NULL si pas de contrainte FK stricte pour le test.
        // Si order_id est obligatoire, il faut cr├⌐er une commande d'abord.
        
        // Pour simplifier l'exemple sans commande pr├⌐alable obligatoire dans cette d├⌐mo:
        if ($orderId) {
             $stmt->execute([$orderId, $transactionId, $provider, $amount]);
             
             // Mettre ├á jour la commande
             $updateStmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
             $updateStmt->execute([$orderId]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Paiement effectu├⌐ avec succ├¿s',
            'transaction_id' => $transactionId
        ]);
    } catch (PDOException $e) {
        error_log("Erreur paiement DB: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de l\'enregistrement du paiement']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Paiement refus├⌐']);
}
?>
