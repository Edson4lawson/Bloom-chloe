<?php
/**
 * Authentification utilisateur avec Access Token + Refresh Token
 * 
 * @endpoint POST /api/auth/login.php
 * @body { "email": "string", "password": "string" }
 * @returns { "access_token": "string", "refresh_token": "string", "expires_in": int, "user": object }
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/rate_limit.php';

// ΓÜá∩╕Å PROTECTION BRUTE-FORCE: Limite ├á 5 tentatives par 5 minutes
loginRateLimit();

// V├⌐rifier si la requ├¬te est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'M├⌐thode non autoris├⌐e'], 405);
}

// R├⌐cup├⌐rer les donn├⌐es de la requ├¬te
$data = getJsonData();

// Valider les donn├⌐es d'entr├⌐e
if (empty($data['email']) || empty($data['password'])) {
    sendJsonResponse(['error' => 'Email et mot de passe requis'], 400);
}

$email = strtolower(trim($data['email']));
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

try {
    // R├⌐cup├⌐rer l'utilisateur par email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // V├⌐rifier si le compte est verrouill├⌐
    if ($user && isset($user['locked_until']) && $user['locked_until'] > date('Y-m-d H:i:s')) {
        logLoginAttempt($pdo, $user['id'] ?? null, $email, $ipAddress, $userAgent, 'blocked', 'Account locked');
        sendJsonResponse(['error' => 'Compte temporairement verrouill├⌐. R├⌐essayez plus tard.'], 423);
    }
    
    // V├⌐rifier si l'utilisateur existe et si le mot de passe est correct
    if (!$user || !password_verify($data['password'], $user['password'])) {
        // Incr├⌐menter les tentatives ├⌐chou├⌐es
        if ($user) {
            $attempts = ($user['failed_login_attempts'] ?? 0) + 1;
            $lockUntil = $attempts >= 5 ? date('Y-m-d H:i:s', strtotime('+15 minutes')) : null;
            
            $stmt = $pdo->prepare('UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?');
            $stmt->execute([$attempts, $lockUntil, $user['id']]);
        }
        
        logLoginAttempt($pdo, $user['id'] ?? null, $email, $ipAddress, $userAgent, 'failed', 'Invalid credentials');
        sendJsonResponse(['error' => 'Email ou mot de passe incorrect'], 401);
    }
    
    $pdo->beginTransaction();
    
    // R├⌐initialiser les tentatives ├⌐chou├⌐es
    $stmt = $pdo->prepare('UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW(), last_login_ip = ? WHERE id = ?');
    $stmt->execute([$ipAddress, $user['id']]);
    
    // G├⌐n├⌐rer l'Access Token (courte dur├⌐e: 15 minutes)
    $accessToken = bin2hex(random_bytes(32));
    $accessExpiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    $stmt = $pdo->prepare('UPDATE users SET token = ?, token_expires_at = ? WHERE id = ?');
    $stmt->execute([$accessToken, $accessExpiresAt, $user['id']]);
    
    // G├⌐n├⌐rer le Refresh Token (longue dur├⌐e: 30 jours)
    $refreshToken = bin2hex(random_bytes(64));
    $refreshExpiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $stmt = $pdo->prepare('
        INSERT INTO refresh_tokens (user_id, token, expires_at, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ');
    $stmt->execute([$user['id'], $refreshToken, $refreshExpiresAt, $ipAddress, $userAgent]);
    
    $pdo->commit();
    
    // Logger la connexion r├⌐ussie
    logLoginAttempt($pdo, $user['id'], $email, $ipAddress, $userAgent, 'success', null);
    
    // Pr├⌐parer les donn├⌐es utilisateur (sans informations sensibles)
    $userData = [
        'id' => $user['id'],
        'email' => $user['email'],
        'first_name' => $user['first_name'] ?? null,
        'last_name' => $user['last_name'] ?? null,
        'role' => $user['role'] ?? 'customer'
    ];
    
    // Retourner les tokens
    sendJsonResponse([
        'message' => 'Connexion r├⌐ussie',
        'access_token' => $accessToken,
        'refresh_token' => $refreshToken,
        'token_type' => 'Bearer',
        'expires_in' => 900, // 15 minutes en secondes
        'user' => $userData
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur lors de la connexion: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la connexion'], 500);
}

/**
 * Log une tentative de connexion
 */
function logLoginAttempt($pdo, $userId, $email, $ipAddress, $userAgent, $status, $reason) {
    try {
        $stmt = $pdo->prepare('
            INSERT INTO login_logs (user_id, email, ip_address, user_agent, status, failure_reason, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$userId, $email, $ipAddress, $userAgent, $status, $reason]);
    } catch (PDOException $e) {
        // Ne pas bloquer le login si le log ├⌐choue (table peut ne pas exister)
        error_log('Failed to log login attempt: ' . $e->getMessage());
    }
}
?>

