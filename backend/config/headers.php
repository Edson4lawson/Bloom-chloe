<?php
/**
 * Configuration CORS et Headers de Sécurité Unifiés - Bloom-Chloe
 * Ce fichier centralise CORS, sécurité et fonctions utilitaires
 * 
 * @version 2.1.0 - Production Ready
 */

// =============================================================================
// CHARGEMENT VARIABLES D'ENVIRONNEMENT
// =============================================================================

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// =============================================================================
// CONFIGURATION CORS SÉCURISÉE & DYNAMIQUE
// =============================================================================

$rawAllowed = getenv('ALLOWED_ORIGINS') ?: ($_ENV['ALLOWED_ORIGINS'] ?? $_SERVER['ALLOWED_ORIGINS'] ?? '');
$allowedOrigins = [];
if (!empty($rawAllowed)) {
    $allowedOrigins = array_map('trim', explode(',', $rawAllowed));
}

// Domaines de confiance toujours autorisés par défaut
$trustedOrigins = [
    'https://bloom-chloe.vercel.app',
    'https://www.bloom-chloe.com',
    'https://bloom-chloe.com',
    'http://localhost:5173',
    'http://localhost:3000',
    'http://localhost:8080',
    'http://127.0.0.1:5173',
    'http://127.0.0.1:3000',
    'http://127.0.0.1:8080'
];

$allAllowed = array_unique(array_merge($allowedOrigins, $trustedOrigins));

// Récupérer l'origine de la requête entrante
$origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
if (!empty($origin)) {
    // Si c'est un referer complet (ex: https://site.vercel.app/shop), extraire le protocole + host
    $parsed = parse_url($origin);
    if (!empty($parsed['scheme']) && !empty($parsed['host'])) {
        $originClean = $parsed['scheme'] . '://' . $parsed['host'] . (!empty($parsed['port']) ? ':' . $parsed['port'] : '');
    } else {
        $originClean = $origin;
    }
} else {
    $originClean = '*';
}

$originAllowed = false;
if (in_array($originClean, $allAllowed)) {
    $originAllowed = true;
} elseif (preg_match('/^https:\/\/([a-z0-9-]+\.)?vercel\.app$/i', $originClean)) {
    // Autoriser automatiquement tous les déploiements preview/production Vercel
    $originAllowed = true;
} elseif (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/i', $originClean)) {
    // Autoriser localhost avec n'importe quel port
    $originAllowed = true;
}

// Appliquer les en-têtes CORS
if ($originAllowed && $originClean !== '*') {
    header("Access-Control-Allow-Origin: $originClean");
    header('Access-Control-Allow-Credentials: true');
} else {
    header("Access-Control-Allow-Origin: *");
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token, X-2FA-Token, Cache-Control, Pragma');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Expose-Headers: X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset');

// Gérer la requête preflight OPTIONS immédiatement
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

// =============================================================================
// HEADERS DE SÉCURITÉ
// =============================================================================

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Type: application/json; charset=utf-8');

// =============================================================================
// CACHE CONTROL
// =============================================================================

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// =============================================================================
// FONCTIONS UTILITAIRES
// =============================================================================

/**
 * Envoie une réponse JSON sécurisée
 */
function sendJsonResponse(mixed $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Récupère et valide les données JSON de la requête
 */
function getJsonData(): array {
    $json = file_get_contents('php://input');
    
    if (empty($json)) {
        return [];
    }

    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }
    
    return is_array($data) ? $data : [];
}
?>
