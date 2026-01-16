<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';

// Vérifier si la requête est de type GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

try {
    // Récupérer les paramètres de requête
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
    $sortBy = $_GET['sort_by'] ?? 'created_at';
    $sortOrder = isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Valider les paramètres
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage)); // Limiter à 100 articles par page
    
    $offset = ($page - 1) * $perPage;
    
    // Construire la requête avec les filtres
    $whereClause = 'WHERE p.status = "published"';
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
    
    // Récupérer les produits avec pagination
    $query = "
        SELECT p.*, c.name as category_name, c.slug as category_slug 
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
    
    sendJsonResponse($response);
    
} catch (PDOException $e) {
    error_log('Erreur lors de la récupération des produits: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la récupération des produits'], 500);
}
?>
