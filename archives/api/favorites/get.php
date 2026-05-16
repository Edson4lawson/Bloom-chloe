<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier si la requête est de type GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

// Récupérer les paramètres de pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

// Valider les paramètres
$page = max(1, $page);
$perPage = max(1, min(50, $perPage)); // Limiter à 50 articles par page
$offset = ($page - 1) * $perPage;

try {
    // Compter le nombre total de favoris
    $countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM favorites f JOIN products p ON f.product_id = p.id WHERE f.user_id = ? AND p.status = "published"');
    $countStmt->execute([$user['id']]);
    $total = $countStmt->fetch()['total'];
    
    // Récupérer les produits favoris avec pagination
    $query = "
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM favorites f 
        JOIN products p ON f.product_id = p.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE f.user_id = ? AND p.status = 'published' 
        ORDER BY f.created_at DESC 
        LIMIT ? OFFSET ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user['id'], $perPage, $offset]);
    $favorites = $stmt->fetchAll();
    
    // Formater la réponse
    $response = [
        'data' => $favorites,
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
    error_log('Erreur lors de la récupération des favoris: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la récupération des favoris'], 500);
}
?>
