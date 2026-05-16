<?php
/**
 * Script de réparation des tables récupérées
 */

$host = "localhost";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 RÉPARATION DES TABLES RÉCUPÉRÉES\n";
    echo "===================================\n\n";
    
    $pdo->exec("USE bloom_chloe_recovery");
    
    // Réparer les tables
    $tables = ["users", "categories", "products", "orders", "order_items", "cart", "favorites"];
    
    foreach ($tables as $table) {
        echo "🔨 Réparation de la table: $table\n";
        try {
            $pdo->exec("REPAIR TABLE $table");
            echo "   ✅ Table $table réparée\n";
        } catch (Exception $e) {
            echo "   ⚠️  Erreur réparation $table: " . $e->getMessage() . "\n";
        }
    }
    
    // Vérifier les données
    echo "\n📊 Vérification des données récupérées:\n";
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            echo "   - $table: " . $result["count"] . " enregistrements\n";
        } catch (Exception $e) {
            echo "   - $table: Erreur de lecture\n";
        }
    }
    
    // Afficher quelques produits si disponibles
    try {
        $stmt = $pdo->query("SELECT id, name, price FROM products LIMIT 5");
        $products = $stmt->fetchAll();
        
        if (!empty($products)) {
            echo "\n🛍️  Exemples de produits récupérés:\n";
            foreach ($products as $product) {
                echo "   - ID: {$product["id"]}, Nom: {$product["name"]}, Prix: {$product["price"]}\n";
            }
        }
    } catch (Exception $e) {
        echo "\n❌ Impossible de lire les produits\n";
    }
    
    echo "\n🎉 Récupération terminée !\n";
    echo "   - Base: bloom_chloe_recovery\n";
    echo "   - Pour utiliser: Renommer bloom_chloe_recovery en bloom_chloe\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>