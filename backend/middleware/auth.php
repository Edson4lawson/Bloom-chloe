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

/**
 * Authentifie l'utilisateur via Token Bearer, GET param, ou JSON body
 */
function authenticate($lenient = false) {
    global $pdo;
    
    // 1. Récupérer le token depuis les en-têtes
    $headers = getallheaders();
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

    if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
        $token = trim($matches[1]);
    } elseif (isset($_GET['token']) && !empty($_GET['token'])) {
        $token = trim($_GET['token']);
    } elseif (isset($_GET['access_token']) && !empty($_GET['access_token'])) {
        $token = trim($_GET['access_token']);
    } elseif (isset($_POST['token']) && !empty($_POST['token'])) {
        $token = trim($_POST['token']);
    }

    // Fallback JSON body
    if (empty($token)) {
        $data = getJsonData();
        if (!empty($data['token'])) {
            $token = trim($data['token']);
        }
    }
    
    if (empty($token)) {
        sendJsonResponse(['error' => 'Token d\'authentification manquant ou invalide. Veuillez vous connecter.'], 401);
    }
    
    try {
        // Vérifier le token dans la base de données
        $query = '
            SELECT u.id, u.email, u.first_name, u.last_name, u.phone, u.address, u.role, u.role_id, r.name as role_name 
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.token = ?
        ';

        $stmt = $pdo->prepare($query);
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if (!$user) {
            sendJsonResponse(['error' => 'Session invalide ou expirée. Veuillez vous reconnecter.'], 401);
        }

        // Utiliser role_name si disponible, sinon role
        if (!empty($user['role_name'])) {
            $user['role'] = $user['role_name'];
        }

        return $user;

    } catch (Exception $e) {
        error_log('Auth error: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Erreur lors de la vérification de l\'authentification.'], 500);
    }
}

// Vérifier si l'utilisateur est administrateur
function requireAdmin($user) {
    $role = $user['role'] ?? '';
    if ($role !== 'admin' && $role !== 'super_admin') {
        sendJsonResponse(['error' => 'Accès non autorisé. Droits administrateur requis.'], 403);
    }
}
