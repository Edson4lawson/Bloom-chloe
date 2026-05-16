<?php
/**
 * Test direct de l'API sans headers pour trouver l'erreur
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connexion directe sans fichiers de config
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    // Simuler les paramètres GET
    $_GET['per_page'] = 5;
    $_GET['sort_by'] = 'created_at';
    $_GET['sort_order'] = 'ASC';
    
    // Récupérer les paramètres
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
    $sortBy = $_GET['sort_by'] ?? 'created_at';
    $sortOrder = isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Valider les paramètres
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $offset = ($page - 1) * $perPage;
    
    // Construire la requête
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
    
    // Ordre de tri sécurisé
    $allowedSortColumns = ['name', 'price', 'created_at', 'updated_at'];
    $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
    $sortOrder = $sortOrder === 'ASC' ? 'ASC' : 'DESC';
    
    // Compter le nombre total de produits
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM products p $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Récupérer les produits
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
    $products = $stmt->fetchAll();
    
    // Formater la réponse
    $response = [
        'data' => $products,
        'pagination' => [
            'total' => (int)$total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]
    ];
    
    // Envoyer la réponse JSON propre
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
