<?php
/**
 * Changement de mot de passe utilisateur
 * 
 * Endpoint: POST /auth/change_password.php
 * Body: { "current_password": "string", "new_password": "string" }
 * Response: { "success": true, "message": "string" }
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier l'authentification
$user = authenticate();

$data = getJsonData();

// Valider les données
if (empty($data['current_password']) || empty($data['new_password'])) {
    sendJsonResponse(['error' => 'Le mot de passe actuel et le nouveau mot de passe sont requis'], 400);
}

$newPassword = $data['new_password'];

// Vérifier la force du mot de passe
if (strlen($newPassword) < 8) {
    sendJsonResponse(['error' => 'Le nouveau mot de passe doit contenir au moins 8 caractères'], 400);
}

if (!preg_match('/[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
    sendJsonResponse(['error' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre'], 400);
}

try {
    // Récupérer le hash actuel
    $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$user['id']]);
    $currentUser = $stmt->fetch();

    if (!$currentUser || !password_verify($data['current_password'], $currentUser['password'])) {
        sendJsonResponse(['error' => 'Le mot de passe actuel est incorrect'], 401);
    }

    // Vérifier que le nouveau mot de passe est différent
    if (password_verify($newPassword, $currentUser['password'])) {
        sendJsonResponse(['error' => 'Le nouveau mot de passe doit être différent de l\'actuel'], 400);
    }

    // Mettre à jour le mot de passe
    $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$newHash, $user['id']]);

    sendJsonResponse([
        'success' => true,
        'message' => 'Mot de passe modifié avec succès'
    ]);

} catch (PDOException $e) {
    error_log('Erreur changement mot de passe: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Une erreur est survenue'], 500);
}
?>
