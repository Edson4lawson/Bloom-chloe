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
$success = true; // Simuler un succès par défaut
$transactionId = 'TXN-' . strtoupper(uniqid());

if ($success) {
    try {
        // Enregistrer le paiement en base
        $stmt = $pdo->prepare("INSERT INTO payments (order_id, transaction_id, provider, amount, status) VALUES (?, ?, ?, ?, 'succeeded')");
        // Note: order_id devrait être valide. Pour ce test, on suppose qu'il existe ou on gère NULL si pas de contrainte FK stricte pour le test.
        // Si order_id est obligatoire, il faut créer une commande d'abord.
        
        // Pour simplifier l'exemple sans commande préalable obligatoire dans cette démo:
        if ($orderId) {
             $stmt->execute([$orderId, $transactionId, $provider, $amount]);
             
             // Mettre à jour la commande
             $updateStmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
             $updateStmt->execute([$orderId]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Paiement effectué avec succès',
            'transaction_id' => $transactionId
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
