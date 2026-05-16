<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN is_featured TINYINT(1) DEFAULT 0");
    $pdo->exec("ALTER TABLE products ADD COLUMN is_newest TINYINT(1) DEFAULT 0");
    $pdo->exec("ALTER TABLE products ADD COLUMN is_bestseller TINYINT(1) DEFAULT 0");
    $pdo->exec("ALTER TABLE products ADD COLUMN is_special_offer TINYINT(1) DEFAULT 0");
    echo "Colonnes ajoutées avec succès\n";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
