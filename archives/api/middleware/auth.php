<?php
require_once __DIR__ . '/../config/db.php';

// V├⌐rifier si le token est pr├⌐sent dans les en-t├¬tes
function authenticate() {
    global $pdo;
    
    // R├⌐cup├⌐rer le token depuis les en-t├¬tes
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    // V├⌐rifier le format du header d'autorisation
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        sendJsonResponse(['error' => 'Token d\'authentification manquant ou invalide'], 401);
    }
    
    $token = $matches[1];
    
    // V├⌐rifier le token dans la base de donn├⌐es
    $stmt = $pdo->prepare('SELECT id, email, first_name, last_name, role FROM users WHERE token = ? AND token_expires_at > NOW()');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        sendJsonResponse(['error' => 'Token invalide ou expir├⌐'], 401);
    }
    
    // Retourner l'utilisateur authentifi├⌐
    return $user;
}

// V├⌐rifier si l'utilisateur est administrateur
function requireAdmin($user) {
    if ($user['role'] !== 'admin') {
        sendJsonResponse(['error' => 'Acc├¿s non autoris├⌐. Droits administrateur requis.'], 403);
    }
}
?>
