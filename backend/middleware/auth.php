<?php
require_once __DIR__ . '/../config/db.php';

// Vérifier si le token est présent dans les en-têtes
function authenticate($lenient = false) {
    global $pdo;
    
    // Récupérer le token depuis les en-têtes
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    
    // Vérifier le format du header d'autorisation
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        sendJsonResponse(['error' => 'Token d\'authentification manquant ou invalide'], 401);
    }
    
    $token = $matches[1];
    
    // Vérifier le token dans la base de données
    $query = 'SELECT id, email, first_name, last_name, role FROM users WHERE token = ?';
    if (!$lenient) {
        $query .= ' AND token_expires_at > NOW()';
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        sendJsonResponse(['error' => $lenient ? 'Utilisateur non trouvé' : 'Token invalide ou expiré'], 401);
    }
    
    // Retourner l'utilisateur authentifié
    return $user;
}

// Vérifier si l'utilisateur est administrateur
function requireAdmin($user) {
    if ($user['role'] !== 'admin') {
        sendJsonResponse(['error' => 'Accès non autorisé. Droits administrateur requis.'], 403);
    }
}
?>
