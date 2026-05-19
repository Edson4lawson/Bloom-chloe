<?php
require_once __DIR__ . '/../config/db.php';

// Helper pour obtenir les headers si getallheaders() n'existe pas
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

// Vérifier si le token est présent dans les en-têtes
function authenticate($lenient = false) {
    global $pdo;
    
    // Récupérer le token depuis les en-têtes ou les paramètres d'URL
    $headers = getallheaders();
    
    // Chercher l'en-tête Authorization (casse-insensible)
    $authHeader = '';
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization') {
            $authHeader = $value;
            break;
        }
    }
    
    if (empty($authHeader)) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    }

    $token = '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    } elseif (isset($_GET['token'])) {
        $token = $_GET['token'];
    } elseif (isset($_GET['access_token'])) {
        $token = $_GET['access_token'];
    }
    
    // Vérifier le format du header d'autorisation
    if (empty($token)) {
        sendJsonResponse(['error' => 'Token d\'authentification manquant ou invalide'], 401);
    }
    
    // Vérifier le token dans la base de données
    $query = 'SELECT id, email, first_name, last_name, phone, address, role FROM users WHERE token = ?';
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
