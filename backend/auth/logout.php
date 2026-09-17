<?php
/**
 * Déconnexion sécurisée - Révoque les tokens
 * 
 * @endpoint POST /auth/logout.php
 * @headers Authorization: Bearer <token>
 * @body { "refresh_token": "string" } (optionnel)
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur (mode clément pour permettre la déconnexion même si le token est expiré)
$user = authenticate(true);

try {
    $pdo->beginTransaction();
    
    // Invalider l'access token actuel
    $stmt = $pdo->prepare('UPDATE users SET token = NULL, token_expires_at = NULL WHERE id = ?');
    $stmt->execute([$user['id']]);
    
    // Récupérer les données pour éventuellement révoquer le refresh token
    $data = getJsonData();
    $refreshToken = $data['refresh_token'] ?? null;
    
    if ($refreshToken) {
        $stmt = $pdo->prepare('
            UPDATE refresh_tokens 
            SET revoked = TRUE 
            WHERE token = ? AND user_id = ?
        ');
        $stmt->execute([$refreshToken, $user['id']]);
    }
    
    // Option: Révoquer TOUS les refresh tokens de l'utilisateur
    if (isset($data['logout_all']) && $data['logout_all'] === true) {
        $stmt = $pdo->prepare('
            UPDATE refresh_tokens 
            SET revoked = TRUE 
            WHERE user_id = ? AND (revoked = FALSE OR revoked IS NULL)
        ');
        $stmt->execute([$user['id']]);
    }
    
    $pdo->commit();
    
    sendJsonResponse([
        'message' => 'Déconnexion réussie',
        'logged_out_all' => isset($data['logout_all']) && $data['logout_all']
    ]);
    
} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur logout: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la déconnexion'], 500);
}
