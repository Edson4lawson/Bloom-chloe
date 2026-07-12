<?php
/**
 * Vérification 2FA (Two-Factor Authentication) - Bloom Chloé
 * Vérifie le code TOTP et active le 2FA
 * 
 * @endpoint POST /api/auth/2fa/verify
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

// Récupérer les données
$data = getJsonData();

if (empty($data['code'])) {
    sendJsonResponse(['error' => 'Code 2FA requis'], 400);
}

try {
    // Récupérer la configuration 2FA de l'utilisateur
    $stmt = $pdo->prepare('SELECT * FROM two_factor_auth WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    $twoFactor = $stmt->fetch();
    
    if (!$twoFactor) {
        sendJsonResponse(['error' => 'Configuration 2FA non trouvée'], 404);
    }
    
    $code = preg_replace('/[^0-9]/', '', $data['code']);
    
    // Vérifier si c'est un code de secours
    if (strlen($code) === 8) {
        $backupCodes = json_decode($twoFactor['backup_codes'], true) ?: [];
        
        if (in_array(strtoupper($code), $backupCodes)) {
            // Retirer le code utilisé
            $backupCodes = array_diff($backupCodes, [strtoupper($code)]);
            
            $stmt = $pdo->prepare('UPDATE two_factor_auth SET backup_codes = ? WHERE user_id = ?');
            $stmt->execute([json_encode(array_values($backupCodes)), $user['id']]);
            
            // Activer le 2FA
            $stmt = $pdo->prepare('UPDATE two_factor_auth SET enabled = 1, last_used_at = NOW() WHERE user_id = ?');
            $stmt->execute([$user['id']]);
            
            sendJsonResponse([
                'message' => '2FA activé avec succès (code de secours utilisé)',
                'remaining_codes' => count($backupCodes)
            ]);
        }
        
        sendJsonResponse(['error' => 'C de secours inve'], 400
    }
    
    // Vérifier le code TOTP
    if (!verifyTOTP($twoFactor['secret'], $code)) {
        sendJsonResponse(['error' => 'Le code de double authentification est incorrect. Veuillez vérifier votre application d\'authentification et réessayer.'], 400);
    }
    
    // Activer le 2FA
    $stmt = $pdo->prepare('UPDATE two_factor_auth SET enabled = 1, last_used_at = NOW() WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    
    sendJsonResponse([
        'message' => '2FA activé avec succès',
        'enabled' => true
    ]);
    
} catch (PDOException $e) {
    error_log('Erreur 2FA verify: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Une erreur technique est survenue lors de la vérification du code 2FA. Veuillez réessayer dans quelques instants.'], 500);
} catch (Exception $e) {
    error_log('Erreur générale 2FA verify: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Une erreur inattendue est survenue lors de la vérification du code 2FA. Veuillez réessayer.'], 500);
}

/**
 * Vérifie un code TOTP
 * Implémentation simplifiée - utiliser robthree/twofactorauth en production
 */
function verifyTOTP($secret, $code) {
    // Décoder le secret Base32
    $secret = base32Decode($secret);
    
    // Obtenir le compteur de temps actuel (période de 30 secondes)
    $time = floor(time() / 30);
    
    // Vérifier le code actuel et les codes adjacents (±1 pour tolérance d'horloge)
    for ($i = -1; $i <= 1; $i++) {
        $counter = $time + $i;
        $expectedCode = generateTOTPCode($secret, $counter);
        
        if (hash_equals($expectedCode, $code)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Génère un code TOTP pour un compteur donné
 */
function generateTOTPCode($secret, $counter) {
    // Convertir le compteur en bytes (big-endian)
    $counterBytes = pack('N*', 0) . pack('N*', $counter);
    
    // HMAC-SHA1
    $hash = hash_hmac('sha1', $counterBytes, $secret, true);
    
    // Dynamic truncation
    $offset = ord($hash[19]) & 0x0F;
    $code = (
        ((ord($hash[$offset]) & 0x7F) << 24) |
        ((ord($hash[$offset + 1]) & 0xFF) << 16) |
        ((ord($hash[$offset + 2]) & 0xFF) << 8) |
        (ord($hash[$offset + 3]) & 0xFF)
    ) % 1000000;
    
    return str_pad($code, 6, '0', STR_PAD_LEFT);
}

/**
 * Décode un secret Base32
 */
function base32Decode($secret) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = strtoupper($secret);
    
    $bits = '';
    for ($i = 0; $i < strlen($secret); $i++) {
        if ($secret[$i] === '=') continue;
        $val = strpos($chars, $secret[$i]);
        if ($val === false) continue;
        $bits .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
    }
    
    $bytes = '';
    for ($i = 0; $i + 8 <= strlen($bits); $i += 8) {
        $bytes .= chr(bindec(substr($bits, $i, 8)));
    }
    
    return $bytes;
}
