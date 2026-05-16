<?php
/**
 * API pour récupérer toutes les catégories
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

try {
    // La connexion $pdo est déjà initialisée par config/db.php

    
    $stmt = $pdo->query("
        SELECT id, name, description, image_url 
        FROM categories 
        ORDER BY name
    ");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendJsonResponse([
        "data" => $categories,
        "total" => count($categories)
    ]);
    
} catch (Exception $e) {
    sendJsonResponse(["error" => "Erreur serveur"], 500);
}

?>