<?php
/**
 * Déconnexion sécurisée - Révoque les tokens
 * 
 * @endpoint POST /api/auth/logout.php
 * @headers Authorization: Bearer <token>
 * @body { "refresh_token": "string" } (optionnel pour révoquer aussi le refresh)
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

try {
    $pdo->beginTransaction();
    
    // Invalider l'access token actuel
    $stmt = $pdo->prepare('UPDATE users SET token = NULL, token_expires_at = NULL WHERE id = ?');
    $stmt->execute([$user['id']]);
    
    // Récupérer les données pour éventuellement révoquer le refresh token
    $data = getJsonData();
    $refreshToken = $data['refresh_token'] ?? null;
    
    if ($refreshToken) {
        // Révoquer le refresh token spécifique
        $stmt = $pdo->prepare('
            UPDATE refresh_tokens 
            SET revoked = 1, revoked_at = NOW() 
            WHERE token = ? AND user_id = ?
        ');
        $stmt->execute([$refreshToken, $user['id']]);
    }
    
    // Option: Révoquer TOUS les refresh tokens de l'utilisateur (déconnexion de tous les appareils)
    if (isset($data['logout_all']) && $data['logout_all'] === true) {
        $stmt = $pdo->prepare('
            UPDATE refresh_tokens 
            SET revoked = 1, revoked_at = NOW() 
            WHERE user_id = ? AND revoked = 0
        ');
        $stmt->execute([$user['id']]);
    }
    
    $pdo->commit();
    
    // Logger la déconnexion
    error_log("User {$user['id']} ({$user['email']}) logged out from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    sendJsonResponse([
        'message' => 'Déconnexion réussie',
        'logged_out_all' => isset($data['logout_all']) && $data['logout_all']
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur logout: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la déconnexion'], 500);
}
?>
