<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Authentifier l'utilisateur
$user = authenticate();

// Récupérer les données de la requête
$data = getJsonData();

// Valider les données d'entrée
if (empty($data['product_id'])) {
    sendJsonResponse(['error' => 'ID du produit requis'], 400);
}

$productId = (int)$data['product_id'];
$userId = $user['id'];

try {
    // Vérifier si le produit existe
    $stmt = $pdo->prepare('SELECT id FROM products WHERE id = ? AND status = "published"');
    $stmt->execute([$productId]);
    
    if (!$stmt->fetch()) {
        sendJsonResponse(['error' => 'Produit non trouvé'], 404);
    }
    
    // Vérifier si le produit est déjà dans les favoris
    $stmt = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$userId, $productId]);
    
    if ($stmt->fetch()) {
        sendJsonResponse(['message' => 'Ce produit est déjà dans vos favoris']);
    }
    
    // Ajouter aux favoris
    $stmt = $pdo->prepare('INSERT INTO favorites (user_id, product_id) VALUES (?, ?)');
    $stmt->execute([$userId, $productId]);
    
    sendJsonResponse(['message' => 'Produit ajouté aux favoris'], 201);
    
} catch (PDOException $e) {
    error_log('Erreur lors de l\'ajout aux favoris: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de l\'ajout aux favoris'], 500);
}
?>
