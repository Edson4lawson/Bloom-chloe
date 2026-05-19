<?php
/**
 * Mise à jour du profil utilisateur
 * 
 * @endpoint POST /api/auth/update_profile.php
 * @body { "first_name": "string", "last_name": "string", "phone": "string", "address": "string" }
 * @returns { "message": "string", "user": object }
 */

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

// Valider les champs requis
if (empty($data['first_name']) || empty($data['last_name'])) {
    sendJsonResponse(['error' => 'Le prénom et le nom sont obligatoires'], 400);
}

$firstName = trim($data['first_name']);
$lastName = trim($data['last_name']);
$phone = isset($data['phone']) ? trim($data['phone']) : null;
$address = isset($data['address']) ? trim($data['address']) : null;

try {
    // Mettre à jour l'utilisateur dans la base de données
    $stmt = $pdo->prepare('
        UPDATE users 
        SET first_name = ?, last_name = ?, phone = ?, address = ?, updated_at = NOW() 
        WHERE id = ?
    ');
    $stmt->execute([
        $firstName,
        $lastName,
        $phone,
        $address,
        $user['id']
    ]);

    // Préparer les données utilisateur mises à jour
    $updatedUser = [
        'id' => $user['id'],
        'email' => $user['email'],
        'first_name' => $firstName,
        'last_name' => $lastName,
        'phone' => $phone,
        'address' => $address,
        'role' => $user['role']
    ];

    sendJsonResponse([
        'message' => 'Profil mis à jour avec succès',
        'user' => $updatedUser
    ]);

} catch (PDOException $e) {
    error_log('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Erreur lors de la mise à jour du profil'], 500);
}
?>
