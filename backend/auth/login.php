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
    sendJsonResponse(['error' => 'Email et mot de passe requis.'], 400);
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
        WHERE LOWER(TRIM(u.email)) = ?
    ');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Vérifier si le compte est verrouillé
    if ($user && isset($user['locked_until']) && !empty($user['locked_until']) && $user['locked_until'] > date('Y-m-d H:i:s')) {
        sendJsonResponse(['error' => 'Compte temporairement verrouillé. Réessayez plus tard.'], 423);
    }
    
    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if (!$user || !password_verify($data['password'], $user['password'])) {
        // Incrémenter les tentatives échouées de manière sécurisée
        if ($user) {
            try {
                $attempts = (int)($user['failed_login_attempts'] ?? 0) + 1;
                $lockUntil = $attempts >= 5 ? date('Y-m-d H:i:s', strtotime('+15 minutes')) : null;
                $updateStmt = $pdo->prepare('UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?');
                $updateStmt->execute([$attempts, $lockUntil, $user['id']]);
            } catch (Exception $e) {}
        }
        
        sendJsonResponse([
            'error' => 'Email ou mot de passe incorrect.'
        ], 401);
    }
    
    // Vérifier si le 2FA est configuré et actif
    $requiresTwoFactor = false;
    try {
        $stmt2fa = $pdo->prepare('SELECT enabled FROM two_factor_auth WHERE user_id = ?');
        $stmt2fa->execute([$user['id']]);
        $twoFactor = $stmt2fa->fetch();
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
            sendJsonResponse(['error' => 'Le code de double authentification est incorrect.'], 401);
        }
    }
    
    // Générer l'Access Token (30 jours de validité pour éviter les déconnexions intempestives)
    $accessToken = bin2hex(random_bytes(32));
    $accessExpiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $updateStmt = $pdo->prepare('UPDATE users SET token = ?, token_expires_at = ? WHERE id = ?');
    $updateStmt->execute([$accessToken, $accessExpiresAt, $user['id']]);
    
    // Générer le Refresh Token (60 jours)
    $refreshToken = bin2hex(random_bytes(64));
    $refreshExpiresAt = date('Y-m-d H:i:s', strtotime('+60 days'));
    
    try {
        $stmt = $pdo->prepare('
            INSERT INTO refresh_tokens (user_id, token, expires_at, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$user['id'], $refreshToken, $refreshExpiresAt, $ipAddress, $userAgent]);
    } catch (Exception $e) {
        error_log('Notice refresh token insert: ' . $e->getMessage());
    }

    // Réinitialiser les tentatives échouées
    try {
        $resetStmt = $pdo->prepare('UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW(), last_login_ip = ? WHERE id = ?');
        $resetStmt->execute([$ipAddress, $user['id']]);
    } catch (Exception $e) {}

    // Logger la tentative réussie
    try {
        $logStmt = $pdo->prepare('INSERT INTO login_logs (user_id, email, ip_address, user_agent, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $logStmt->execute([$user['id'], $email, $ipAddress, $userAgent, 'success']);
    } catch (Exception $e) {}
    
    // Préparer les données utilisateur
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
    
    // Retourner les tokens et l'utilisateur
    sendJsonResponse([
        'message' => 'Connexion réussie',
        'access_token' => $accessToken,
        'refresh_token' => $refreshToken,
        'token_type' => 'Bearer',
        'expires_in' => 2592000, // 30 jours
        'user' => $userData
    ]);

} catch (PDOException $e) {
    error_log('BLOOM ERROR [Login]: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    sendJsonResponse(['error' => 'Une erreur est survenue lors de l\'authentification : ' . $e->getMessage()], 500);
} catch (Exception $e) {
    error_log('BLOOM ERROR [Login General]: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Une erreur inattendue est survenue.'], 500);
}
