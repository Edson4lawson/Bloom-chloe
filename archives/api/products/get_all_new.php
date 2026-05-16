<?php
/**
 * API Produits - Version corrigée et fonctionnelle
 */

// Headers CORS sans erreurs
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
    // Connexion BDD directe et simple
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    // Paramètres sécurisés
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = max(1, min(200, (int)($_GET['per_page'] ?? 10))); // Augmenté à 200 comme demandé
    $offset = ($page - 1) * $perPage;
    
    // Filtres optionnels
    $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
    $sortBy = $_GET['sort_by'] ?? 'created_at';
    $sortOrder = isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Construction de la requête
    $whereClause = 'WHERE 1=1';
    $params = [];
    
    if ($categoryId) {
        $whereClause .= ' AND p.category_id = ?';
        $params[] = $categoryId;
    }
    
    if ($search) {
        $whereClause .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
        $params[] = $search;
        $params[] = $search;
    }
    
    // Colonnes autorisées pour le tri
    $allowedSortColumns = ['name', 'price', 'created_at', 'updated_at', 'id'];
    $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
    
    // Requête principale corrigée
    $query = "
        SELECT p.id, p.name, p.slug, p.description, p.price, p.stock_quantity,
               p.image_url, p.source, p.created_at, p.updated_at,
               c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $whereClause 
        ORDER BY p.$sortBy $sortOrder 
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $perPage;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Compter le total
    $countQuery = "SELECT COUNT(*) as total FROM products p $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute(array_slice($params, 0, -2)); // Exclure LIMIT et OFFSET
    $total = (int)$countStmt->fetch()['total'];
    
    // Nettoyer les données
    foreach ($products as &$product) {
        $product['image_url'] = $product['image_url'] ?? '';
        $product['category_name'] = $product['category_name'] ?? '';
        $product['description'] = $product['description'] ?? '';
        // Convertir les prix en nombre
        $product['price'] = (float)$product['price'];
    }
    
    // Réponse JSON complète
    echo json_encode([
        'data' => $products,
        'pagination' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $total)
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur serveur',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
}
?>
