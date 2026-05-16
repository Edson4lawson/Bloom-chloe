<?php
/**
 * API pour récupérer tous les produits
 */

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

try {
    // La connexion $pdo est déjà initialisée par config/db.php

    
    // Paramètres
    $page = max(1, (int)($_GET["page"] ?? 1));
    $perPage = max(1, min(200, (int)($_GET["per_page"] ?? 10)));
    $offset = ($page - 1) * $perPage;
    
    // Requête
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.slug, p.description, p.price, p.stock_quantity,
               p.image_url, p.source, p.created_at, p.updated_at,
               p.is_featured, p.is_newest, p.is_bestseller, p.is_special_offer,
               c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    
    $stmt->bindValue(1, (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $total = (int)$totalStmt->fetch()["total"];
    
    sendJsonResponse([
        "data" => $products,
        "pagination" => [
            "total" => $total,
            "per_page" => $perPage,
            "current_page" => $page,
            "last_page" => ceil($total / $perPage),
            "from" => $total > 0 ? $offset + 1 : 0,
            "to" => min($offset + $perPage, $total)
        ]
    ]);
    
} catch (Exception $e) {
    sendJsonResponse([
        "error" => "Erreur serveur",
        "message" => $e->getMessage()
    ], 500);
}

?>