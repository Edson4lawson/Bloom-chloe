<?php
/**
 * Headers de sécurité pour l'API Bloom-Chloe
 * Ce fichier gère les CORS et les fonctions utilitaires
 */

// =============================================================================
// CONFIGURATION CORS SÉCURISÉE
// =============================================================================

// Liste blanche des origines autorisées
$allowedOrigins = [
    'http://localhost:5173',       // Vite dev server
    'http://localhost:3000',       // Alternative dev
    'http://127.0.0.1:5173',      // Localhost alternatif
    'https://bloom-chloe.com',    // Production (à configurer)
    'https://www.bloom-chloe.com' // Production WWW
];

// Récupérer l'origine de la requête
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Vérifier si l'origine est autorisée
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
} elseif (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/', $origin)) {
    // En développement, autoriser localhost avec n'importe quel port
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
}

// Headers CORS
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

// Headers de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Type: application/json; charset=utf-8');

// Gestion des requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

// Fonction pour envoyer une réponse JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    exit();
}

// Fonction pour obtenir les données JSON de la requête
function getJsonData() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(['error' => 'Données JSON invalides'], 400);
    }
    
    return $data;
}
?>

