<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
    $categories = $stmt->fetchAll();
    
    // Si aucune catégorie n'existe, on peut renvoyer les catégories par défaut pour le frontend
    if (empty($categories)) {
        // Optionnel : insérer des données par défaut si vide, ou juste renvoyer un tableau vide
    }

    echo json_encode(['data' => $categories]);
} catch (PDOException $e) {
    error_log('Erreur récupération catégories : ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
?>
