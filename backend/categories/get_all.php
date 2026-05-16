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
        SELECT c.id, c.name, c.description, c.image_url, COUNT(p.id) as product_count 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        GROUP BY c.id, c.name, c.description, c.image_url
        ORDER BY c.name
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