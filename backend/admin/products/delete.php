<?php
/**
 * API pour supprimer un produit
 */

require_once __DIR__ . '/../../config/headers.php';
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

$data = getJsonData();
$id = $data['id'] ?? null;

if (!$id) {
    sendJsonResponse(['error' => 'ID du produit manquant'], 400);
}

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    sendJsonResponse([
        'success' => true,
        'message' => 'Produit supprimé avec succès'
    ]);

} catch (Exception $e) {
    sendJsonResponse(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
}
?>
