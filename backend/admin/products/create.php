<?php
/**
 * API pour créer un produit
 */

require_once __DIR__ . '/../../config/headers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Authentifier l'administrateur
$user = authenticate();
if ($user['role'] !== 'admin') {
    sendJsonResponse(['error' => 'Accès refusé. Droits administrateur requis.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

$data = getJsonData();

$name = $data['name'] ?? null;
$description = $data['description'] ?? '';
$price = $data['price'] ?? 0;
$category_id = $data['category_id'] ?? null;
$status = $data['status'] ?? 'published';
$image_url = $data['image_url'] ?? '';
$stock = isset($data['stock']) ? (int)$data['stock'] : (isset($data['stock_quantity']) ? (int)$data['stock_quantity'] : 0);

if (!$name || !$price || !$category_id) {
    sendJsonResponse(['error' => 'Champs obligatoires manquants (nom, prix, catégorie)'], 400);
}

// Générer un slug
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . substr(uniqid(), -4);

try {
    $stmt = $pdo->prepare("
        INSERT INTO products (name, slug, description, price, category_id, status, image_url, stock, stock_quantity, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([$name, $slug, $description, $price, $category_id, $status, $image_url, $stock, $stock_quantity = $stock]);
    $id = $pdo->lastInsertId();

    sendJsonResponse([
        'success' => true,
        'product_id' => $id,
        'message' => 'Produit créé avec succès'
    ]);

} catch (Exception $e) {
    sendJsonResponse(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
}
?>
