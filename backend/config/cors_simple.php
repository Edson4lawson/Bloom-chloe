<?php
/**
 * Configuration CORS simplifiée pour développement
 * À utiliser uniquement en développement
 */

// En développement, autoriser toutes les origines localhost
if (getenv('APP_ENV') !== 'production') {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    
    // Autoriser localhost avec n'importe quel port
    if (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/', $origin)) {
        header("Access-Control-Allow-Origin: $origin");
    } else {
        // Autoriser les origines de développement par défaut
        $allowedOrigins = [
            'http://localhost:5173',
            'http://localhost:3000',
            'http://localhost:8080',
            'http://127.0.0.1:5173',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:8080'
        ];
        
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        }
    }
    
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token, X-2FA-Token');
    header('Access-Control-Max-Age: 86400');
}

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}
