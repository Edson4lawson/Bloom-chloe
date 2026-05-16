<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE products MODIFY COLUMN source ENUM('produit', 'store', 'tendance') DEFAULT 'produit'");
    echo "Enum mis à jour avec succès\n";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
