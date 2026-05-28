<?php
/**
 * Headers de sécurité pour l'API Bloom-Chloe
 * Ce fichier gère les CORS et les fonctions utilitaires
 */

// =============================================================================
// CONFIGURATION CORS DYNAMIQUE (DÉVELOPPEMENT)
// =============================================================================

$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
if ($origin !== '*') {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
} else {
    header("Access-Control-Allow-Origin: *");
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json; charset=utf-8');

// Gestion des requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

// Fonction pour envoyer une réponse JSON
function sendJsonResponse(mixed $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    exit();
}

// Fonction pour obtenir les données JSON de la requête
function getJsonData() {
    $json = file_get_contents('php://input');
    
    if (empty($json)) {
        return [];
    }

    $data = json_decode($json, true);
    
    // Si le décodage échoue, on retourne un tableau vide pour éviter le 400
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }
    
    return is_array($data) ? $data : [];
}
