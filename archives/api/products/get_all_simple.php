<?php
/**
 * API Produits - Version simplifiée et corrigée
 */

// Désactiver l'affichage des erreurs pour éviter les headers conflicts
error_reporting(0);
ini_set('display_errors', 0);

// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// Gérer OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

try {
    // Connexion BDD
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    // Paramètres
    $page = (int)($_GET['page'] ?? 1);
    $perPage = (int)($_GET['per_page'] ?? 10);
    $perPage = max(1, min(100, $perPage));
    $offset = ($page - 1) * $perPage;
    
    // Requête simple
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.description, p.price, p.image_url, p.source,
               c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([$perPage, $offset]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $total = $totalStmt->fetch()['total'];
    
    // Réponse
    echo json_encode([
        'data' => $products,
        'pagination' => [
            'total' => (int)$total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
?>
