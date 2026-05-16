<?php
/**
 * API Produits - Version corrigée
 */

// Headers CORS (sans affichage d'erreurs)
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
    // Connexion BDD directe
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    // Paramètres sécurisés
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = max(1, min(100, (int)($_GET['per_page'] ?? 10)));
    $offset = ($page - 1) * $perPage;
    
    // Requête principale
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
    
    // Compter le total
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $total = (int)$totalStmt->fetch()['total'];
    
    // Nettoyer les URLs d'images
    foreach ($products as &$product) {
        $product['image_url'] = $product['image_url'] ?? '';
    }
    
    // Réponse JSON
    echo json_encode([
        'data' => $products,
        'pagination' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur', 'details' => $e->getMessage()]);
}
?>
