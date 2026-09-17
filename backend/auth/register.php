<?php
/**
 * Inscription utilisateur (ADMIN SEULEMENT)
 * Permet uniquement aux admins de créer des comptes staff
 * 
 * @endpoint POST /api/auth/register.php
 * @header Authorization: Bearer {admin_token}
 * @body { "email": "string", "password": "string", "first_name": "string", "last_name": "string", "role_id": "int" }
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/rate_limit.php';

// ⚠️ PROTECTION: Limite à 10 inscriptions par 5 minutes par IP
rateLimit('public_register', 10, 300);

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
        sendJsonResponse(['error' => 'Tous les champs obligatoires doivent être renseignés'], 400);
    }
}

// Nettoyer l'email
$email = strtolower(trim($data['email']));

// Valider l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(['error' => 'Format d\'email invalide'], 400);
}

// Valider la force du mot de passe
function validatePasswordStrength($password) {
    if (strlen($password) < 8) {
        return 'Le mot de passe doit contenir au moins 8 caractères';
    }
    if (strlen($password) > 128) {
        return 'Le mot de passe ne peut pas dépasser 128 caractères';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Le mot de passe doit contenir au moins une lettre majuscule';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Le mot de passe doit contenir au moins une lettre minuscule';
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
    sendJsonResponse(['error' => 'Cette adresse email est déjà associée à un compte. Veuillez vous connecter ou utiliser une autre adresse email.'], 409);
}

// Déterminer le rôle
// Par défaut: customer (role_id = 1)
$roleId = 1;
$roleName = 'customer';

// Si un admin authentifié souhaite créer un rôle spécifique
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!empty($authHeader) && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    $adminToken = $matches[1];
    $adminStmt = $pdo->prepare('SELECT u.id, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.token = ? AND u.token_expires_at > NOW()');
    $adminStmt->execute([$adminToken]);
    $adminUser = $adminStmt->fetch();
    if ($adminUser && ($adminUser['role_name'] === 'admin' || $adminUser['role_name'] === 'super_admin')) {
        if (!empty($data['role_id'])) {
            $roleId = (int)$data['role_id'];
            $roleStmt = $pdo->prepare('SELECT name FROM roles WHERE id = ?');
            $roleStmt->execute([$roleId]);
            $roleRow = $roleStmt->fetch();
            if ($roleRow) {
                $roleName = $roleRow['name'];
            }
        }
    }
}

// Hacher le mot de passe
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

try {
    $pdo->beginTransaction();

    // Insérer le nouvel utilisateur avec le rôle spécifié
    $stmt = $pdo->prepare('INSERT INTO users (email, password, first_name, last_name, address, phone, role, role_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $email,
        $hashedPassword,
        trim($data['first_name']),
        trim($data['last_name']),
        $data['address'] ?? null,
        $data['phone'] ?? null,
        $roleName === 'admin' ? 'admin' : 'customer',
        $roleId
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
        'phone' => trim($data['phone'] ?? ''),
        'address' => trim($data['address'] ?? ''),
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
    sendJsonResponse(['error' => 'Une erreur technique est survenue lors de la création de votre compte. Veuillez réessayer dans quelques instants. Si le problème persiste, contactez notre support.'], 500);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur générale lors de l\'inscription: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Une erreur inattendue est survenue lors de l\'inscription. Veuillez réessayer.'], 500);
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

