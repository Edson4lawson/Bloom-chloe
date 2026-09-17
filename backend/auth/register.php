<?php
/**
 * Inscription utilisateur - Bloom Chloé
 * Support multi-environnements (MySQL local + PostgreSQL Neon en production)
 * 
 * @endpoint POST /auth/register.php
 * @body { "email": "string", "password": "string", "first_name": "string", "last_name": "string", "phone"?: "string", "address"?: "string" }
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/rate_limit.php';

// ⚠️ PROTECTION: Limite d'inscriptions
rateLimit('public_register', 15, 300);

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
        sendJsonResponse(['error' => 'Tous les champs obligatoires (prénom, nom, email, mot de passe) doivent être renseignés.'], 400);
    }
}

// Nettoyer l'email
$email = strtolower(trim($data['email']));

// Valider l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(['error' => 'Format d\'adresse email invalide.'], 400);
}

// Valider le mot de passe (min 6 caractères)
if (strlen($data['password']) < 6) {
    sendJsonResponse(['error' => 'Le mot de passe doit contenir au moins 6 caractères.'], 400);
}
if (strlen($data['password']) > 128) {
    sendJsonResponse(['error' => 'Le mot de passe ne peut pas dépasser 128 caractères.'], 400);
}

try {
    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendJsonResponse(['error' => 'Cette adresse email est déjà associée à un compte. Veuillez vous connecter ou réinitialiser votre mot de passe.'], 409);
    }

    // Déterminer le rôle
    $roleName = 'customer';
    $roleId = null;

    try {
        $roleStmt = $pdo->prepare("SELECT id, name FROM roles WHERE name = 'customer' LIMIT 1");
        $roleStmt->execute();
        $roleRow = $roleStmt->fetch();
        if ($roleRow) {
            $roleId = (int)$roleRow['id'];
        }
    } catch (Exception $e) {
        // En cas d'absence de la table roles
        $roleId = null;
    }

    // Si un admin authentifié souhaite créer un compte staff / spécifique
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if (!empty($authHeader) && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        $adminToken = $matches[1];
        try {
            $adminStmt = $pdo->prepare('SELECT u.id, u.role, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.token = ? AND u.token_expires_at > NOW()');
            $adminStmt->execute([$adminToken]);
            $adminUser = $adminStmt->fetch();
            $effectiveRole = $adminUser['role_name'] ?? $adminUser['role'] ?? '';
            if (in_array($effectiveRole, ['admin', 'super_admin']) && !empty($data['role_id'])) {
                $customRoleId = (int)$data['role_id'];
                $customRoleStmt = $pdo->prepare('SELECT id, name FROM roles WHERE id = ?');
                $customRoleStmt->execute([$customRoleId]);
                $customRole = $customRoleStmt->fetch();
                if ($customRole) {
                    $roleId = (int)$customRole['id'];
                    $roleName = $customRole['name'];
                }
            }
        } catch (Exception $e) {
            // Silencieux
        }
    }

    // Hacher le mot de passe
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

    $pdo->beginTransaction();

    $isPgsql = ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql');

    if ($isPgsql) {
        $stmt = $pdo->prepare('
            INSERT INTO users (email, password, first_name, last_name, address, phone, role, role_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW()) 
            RETURNING id
        ');
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
        $userId = (int)$stmt->fetchColumn();
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO users (email, password, first_name, last_name, address, phone, role, role_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ');
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
        $userId = (int)$pdo->lastInsertId();
    }

    // Générer l'Access Token (15 minutes)
    $accessToken = bin2hex(random_bytes(32));
    $accessExpiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $stmt = $pdo->prepare('UPDATE users SET token = ?, token_expires_at = ? WHERE id = ?');
    $stmt->execute([$accessToken, $accessExpiresAt, $userId]);

    // Générer le Refresh Token (30 jours)
    $refreshToken = bin2hex(random_bytes(64));
    $refreshExpiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

    try {
        $stmt = $pdo->prepare('
            INSERT INTO refresh_tokens (user_id, token, expires_at, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$userId, $refreshToken, $refreshExpiresAt, $ipAddress, $userAgent]);
    } catch (Exception $e) {
        error_log('Warning refresh_tokens insert: ' . $e->getMessage());
    }

    // Générer un token de vérification email
    try {
        $emailVerifyToken = bin2hex(random_bytes(32));
        $emailVerifyExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $pdo->prepare('
            INSERT INTO email_verifications (user_id, token, expires_at, created_at) 
            VALUES (?, ?, ?, NOW())
        ');
        $stmt->execute([$userId, $emailVerifyToken, $emailVerifyExpires]);
    } catch (Exception $e) {
        error_log('Warning email_verifications insert: ' . $e->getMessage());
    }

    $pdo->commit();

    // Préparer les données utilisateur
    $userData = [
        'id' => $userId,
        'email' => $email,
        'first_name' => trim($data['first_name']),
        'last_name' => trim($data['last_name']),
        'phone' => trim($data['phone'] ?? ''),
        'address' => trim($data['address'] ?? ''),
        'role' => $roleName,
        'email_verified' => false
    ];

    // Retourner les tokens et les données utilisateur
    sendJsonResponse([
        'message' => 'Inscription réussie.',
        'access_token' => $accessToken,
        'refresh_token' => $refreshToken,
        'token_type' => 'Bearer',
        'expires_in' => 900,
        'user' => $userData
    ], 201);

} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('BLOOM ERROR [Register]: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    sendJsonResponse(['error' => 'Une erreur est survenue lors de la création de votre compte : ' . $e->getMessage()], 500);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('BLOOM ERROR [Register General]: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Une erreur inattendue est survenue.'], 500);
}
