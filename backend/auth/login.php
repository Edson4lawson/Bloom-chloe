<?php
/**
 * Authentification utilisateur avec Access Token + Refresh Token
 * 
 * Endpoint: POST /auth/login.php
 * Body: { "email": "string", "password": "string", "two_factor_code"?: "string" }
 * Response: { "access_token": "string", "refresh_token": "string", "expires_in": int, "user": object }
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/rate_limit.php';
require_once __DIR__ . '/../middleware/two_factor.php';
require_once __DIR__ . '/../middleware/captcha.php';

// ⚠️ PROTECTION BRUTE-FORCE
loginRateLimit();

// Récupérer les données de la requête
$data = getJsonData();

// Valider les données d'entrée
if (empty($data['email']) || empty($data['password'])) {
    sendJsonResponse(['error' => 'Email et mot de passe requis'], 400);
}

$email = strtolower(trim($data['email']));
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

try {
    // Récupérer l'utilisateur par email
    $stmt = $pdo->prepare('
        SELECT u.*, r.name as role_name
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.email = ?
    ');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Vérifier si le compte est verrouillé
    if ($user && isset($user['locked_until']) && $user['locked_until'] > date('Y-m-d H:i:s')) {
        logLoginAttempt($pdo, $user['id'] ?? null, $email, $ipAddress, $userAgent, 'blocked', 'Account locked');
        sendJsonResponse(['error' => 'Compte temporairement verrouillé. Réessayez plus tard.'], 423);
    }
    
    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if (!$user || !password_verify($data['password'], $user['password'])) {
        // Incrémenter les tentatives échouées
        if ($user) {
            $attempts = ($user['failed_login_attempts'] ?? 0) + 1;
            $lockUntil = $attempts >= 5 ? date('Y-m-d H:i:s', strtotime('+15 minutes')) : null;
            
            try {
                $stmt = $pdo->prepare('UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?');
                $stmt->execute([$attempts, $lockUntil, $user['id']]);
            } catch (Exception $e) {}
        }
        
        logLoginAttempt($pdo, $user['id'] ?? null, $email, $ipAddress, $userAgent, 'failed', 'Invalid credentials');
        
        $errorMessage = 'Email ou mot de passe incorrect.';
        sendJsonResponse([
            'error' => $errorMessage
        ], 401);
    }
    
    // Vérifier si le 2FA est configuré et actif
    $requiresTwoFactor = false;
    try {
        $stmt = $pdo->prepare('SELECT enabled FROM two_factor_auth WHERE user_id = ?');
        $stmt->execute([$user['id']]);
        $twoFactor = $stmt->fetch();
        if ($twoFactor && !empty($twoFactor['enabled'])) {
            $requiresTwoFactor = true;
        }
    } catch (Exception $e) {
        $requiresTwoFactor = false;
    }
    
    // Si 2FA requis et code non fourni
    if ($requiresTwoFactor && empty($data['two_factor_code'])) {
        $tempSessionToken = createTwoFactorSession($user['id']);
        
        sendJsonResponse([
            'error' => 'Veuillez entrer le code de double authentification (2FA).',
            'require_2fa_verification' => true,
            'temp_session_token' => $tempSessionToken
        ], 403);
    }
    
    // Si 2FA requis et code fourni, le vérifier
    if ($requiresTwoFactor && !empty($data['two_factor_code'])) {
        if (!validateTwoFactorCode($user['id'], $data['two_factor_code'])) {
            logLoginAttempt($pdo, $user['id'], $email, $ipAddress, $userAgent, 'failed', 'Invalid 2FA code');
            sendJsonResponse(['error' => 'Le code de double authentification est incorrect.'], 401);
        }
    }
    
    $pdo->beginTransaction();
    
    // Réinitialiser les tentatives échouées
    try {
        $stmt = $pdo->prepare('UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW(), last_login_ip = ? WHERE id = ?');
        $stmt->execute([$ipAddress, $user['id']]);
    } catch (Exception $e) {}
    
    // Générer l'Access Token (15 minutes)
    $accessToken = bin2hex(random_bytes(32));
    $accessExpiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    $stmt = $pdo->prepare('UPDATE users SET token = ?, token_expires_at = ? WHERE id = ?');
    $stmt->execute([$accessToken, $accessExpiresAt, $user['id']]);
    
    // Générer le Refresh Token (30 jours)
    $refreshToken = bin2hex(random_bytes(64));
    $refreshExpiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    try {
        $stmt = $pdo->prepare('
            INSERT INTO refresh_tokens (user_id, token, expires_at, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$user['id'], $refreshToken, $refreshExpiresAt, $ipAddress, $userAgent]);
    } catch (Exception $e) {
        error_log('Warning refresh token insert: ' . $e->getMessage());
    }
    
    $pdo->commit();
    
    // Logger la connexion réussie
    logLoginAttempt($pdo, $user['id'], $email, $ipAddress, $userAgent, 'success', null);
    
    // Préparer les données utilisateur (rôle prioritaire role_name sinon role)
    $effectiveRole = $user['role_name'] ?? $user['role'] ?? 'customer';
    
    $userData = [
        'id' => (int)$user['id'],
        'email' => $user['email'],
        'first_name' => $user['first_name'] ?? null,
        'last_name' => $user['last_name'] ?? null,
        'phone' => $user['phone'] ?? null,
        'address' => $user['address'] ?? null,
        'role' => $effectiveRole
    ];
    
    // Retourner les tokens
    sendJsonResponse([
        'message' => 'Connexion réussie',
        'access_token' => $accessToken,
        'refresh_token' => $refreshToken,
        'token_type' => 'Bearer',
        'expires_in' => 900,
        'user' => $userData
    ]);

} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('BLOOM ERROR [Login]: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    sendJsonResponse(['error' => 'Une erreur est survenue lors de l\'authentification.'], 500);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('BLOOM ERROR [Login General]: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Une erreur inattendue est survenue lors de la connexion.'], 500);
}

/**
 * Log une tentative de connexion
 */
function logLoginAttempt(PDO $pdo, ?int $userId, string $email, string $ipAddress, string $userAgent, string $status, ?string $reason): void {
    try {
        $stmt = $pdo->prepare('
            INSERT INTO login_logs (user_id, email, ip_address, user_agent, status, failure_reason, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$userId, $email, $ipAddress, $userAgent, $status, $reason]);
    } catch (Exception $e) {
        // Silencieux si la table n'existe pas
    }
}
