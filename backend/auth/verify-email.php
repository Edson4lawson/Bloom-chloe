<?php
/**
 * Endpoint de vérification email
 * 
 * @endpoint GET /api/auth/verify-email.php?token=xxx
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Récupérer le token
$token = $_GET['token'] ?? '';

if (empty($token) || strlen($token) !== 64) {
    sendJsonResponse(['error' => 'Token de vérification invalide'], 400);
}

try {
    // Vérifier le token
    $stmt = $pdo->prepare('
        SELECT ev.*, u.email, u.first_name 
        FROM email_verifications ev
        INNER JOIN users u ON ev.user_id = u.id
        WHERE ev.token = ? 
        AND ev.expires_at > NOW()
        AND ev.verified_at IS NULL
    ');
    $stmt->execute([$token]);
    $verification = $stmt->fetch();
    
    if (!$verification) {
        sendJsonResponse(['error' => 'Token invalide ou expiré'], 400);
    }
    
    $pdo->beginTransaction();
    
    // Marquer l'email comme vérifié
    $stmt = $pdo->prepare('UPDATE users SET email_verified_at = NOW() WHERE id = ?');
    $stmt->execute([$verification['user_id']]);
    
    // Marquer le token comme utilisé
    $stmt = $pdo->prepare('UPDATE email_verifications SET verified_at = NOW() WHERE id = ?');
    $stmt->execute([$verification['id']]);
    
    $pdo->commit();
    
    sendJsonResponse([
        'message' => 'Email vérifié avec succès',
        'email' => $verification['email']
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur vérification email: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la vérification'], 500);
}
?>
