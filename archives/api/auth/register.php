<?php
/**
 * Inscription utilisateur avec Access Token + Refresh Token
 * Génère un email de vérification
 * 
 * @endpoint POST /api/auth/register.php
 * @body { "email": "string", "password": "string", "first_name": "string", "last_name": "string" }
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/rate_limit.php';

// ⚠️ PROTECTION: Limite à 3 inscriptions par heure par IP
registerRateLimit();

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Récupérer les données de la requête
$data = getJsonData();

// Valider les données d'entrée
$requiredFields = ['email', 'password', 'first_name', 'last_name'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        sendJsonResponse(['error' => 'Tous les champs sont obligatoires'], 400);
    }
}

// Nettoyer l'email
$email = strtolower(trim($data['email']));

// Valider l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(['error' => 'Format d\'email invalide'], 400);
}

// ⚠️ SÉCURITÉ: Valider la force du mot de passe
function validatePasswordStrength($password) {
    if (strlen($password) < 8) {
        return 'Le mot de passe doit contenir au moins 8 caractères';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Le mot de passe doit contenir au moins une majuscule';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Le mot de passe doit contenir au moins une minuscule';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Le mot de passe doit contenir au moins un chiffre';
    }
    return null;
}

$passwordError = validatePasswordStrength($data['password']);
if ($passwordError) {
    sendJsonResponse(['error' => $passwordError], 400);
}

// Vérifier si l'utilisateur existe déjà
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    sendJsonResponse(['error' => 'Cet email est déjà utilisé'], 409);
}

// Hacher le mot de passe avec un algorithme sécurisé
$hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

try {
    $pdo->beginTransaction();
    
    // Insérer le nouvel utilisateur
    $stmt = $pdo->prepare('INSERT INTO users (email, password, first_name, last_name, address, phone, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $email,
        $hashedPassword,
        trim($data['first_name']),
        trim($data['last_name']),
        $data['address'] ?? null,
        $data['phone'] ?? null
    ]);
    
    $userId = $pdo->lastInsertId();
    
    // Générer l'Access Token (15 minutes)
    $accessToken = bin2hex(random_bytes(32));
    $accessExpiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    $stmt = $pdo->prepare('UPDATE users SET token = ?, token_expires_at = ? WHERE id = ?');
    $stmt->execute([$accessToken, $accessExpiresAt, $userId]);
    
    // Générer le Refresh Token (30 jours)
    $refreshToken = bin2hex(random_bytes(64));
    $refreshExpiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $stmt = $pdo->prepare('
        INSERT INTO refresh_tokens (user_id, token, expires_at, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ');
    $stmt->execute([$userId, $refreshToken, $refreshExpiresAt, $ipAddress, $userAgent]);
    
    // Générer un token de vérification email
    $emailVerifyToken = bin2hex(random_bytes(32));
    $emailVerifyExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $stmt = $pdo->prepare('
        INSERT INTO email_verifications (user_id, token, expires_at, created_at) 
        VALUES (?, ?, ?, NOW())
    ');
    $stmt->execute([$userId, $emailVerifyToken, $emailVerifyExpires]);
    
    $pdo->commit();
    
    // En production, envoyer l'email de vérification
    // sendVerificationEmail($email, $data['first_name'], $emailVerifyToken);
    
    // Log pour développement
    error_log("Email verification token for $email: $emailVerifyToken");
    
    // Préparer les données utilisateur
    $userData = [
        'id' => $userId,
        'email' => $email,
        'first_name' => trim($data['first_name']),
        'last_name' => trim($data['last_name']),
        'role' => 'customer',
        'email_verified' => false
    ];
    
    // Retourner les tokens
    sendJsonResponse([
        'message' => 'Inscription réussie. Un email de vérification a été envoyé.',
        'access_token' => $accessToken,
        'refresh_token' => $refreshToken,
        'token_type' => 'Bearer',
        'expires_in' => 900,
        'user' => $userData,
        // En développement uniquement
        'dev_email_token' => (getenv('APP_ENV') !== 'production') ? $emailVerifyToken : null
    ], 201);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur lors de l\'inscription: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de l\'inscription'], 500);
}

/**
 * Envoi de l'email de vérification (à implémenter)
 */
function sendVerificationEmail($email, $firstName, $token) {
    $verifyUrl = getenv('FRONTEND_URL') . "/verify-email?token=$token";
    
    // Implémenter avec PHPMailer, SendGrid, etc.
    // $subject = "Vérifiez votre email - Bloom Chloé";
    // ...
}
?>

