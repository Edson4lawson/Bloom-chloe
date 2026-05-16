<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier si la requête est de type DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

// Récupérer l'ID du produit depuis les paramètres de requête ou du corps de la requête
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

// Si l'ID n'est pas dans les paramètres de requête, vérifier le corps de la requête
if (!$productId) {
    $data = getJsonData();
    $productId = isset($data['product_id']) ? (int)$data['product_id'] : null;
}

// Valider l'ID du produit
if (!$productId) {
    sendJsonResponse(['error' => 'ID du produit requis'], 400);
}

try {
    // Supprimer des favoris
    $stmt = $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$user['id'], $productId]);
    
    if ($stmt->rowCount() === 0) {
        sendJsonResponse(['message' => 'Produit non trouvé dans vos favoris'], 404);
    }
    
    sendJsonResponse(['message' => 'Produit retiré des favoris']);
    
} catch (PDOException $e) {
    error_log('Erreur lors de la suppression des favoris: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la suppression des favoris'], 500);
}
?>
