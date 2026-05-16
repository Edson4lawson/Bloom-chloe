<?php
/**
 * Script de récupération directe des fichiers .ibd
 * Méthode avancée pour récupérer les données originales
 */

echo "🔧 RÉCUPÉRATION DIRECTE DES DONNÉES ORIGINALES\n";
echo "=============================================\n\n";

// Configuration
$mysqlDataDir = 'C:/laragon/data/mysql-8';
$originalDb = 'bloom_chloe';
$recoveryDb = 'bloom_chloe_recovery';

echo "1️⃣ Préparation de l'environnement...\n";

// Créer un script batch pour la récupération
$batchScript = '@echo off
echo ARRÊT DE MYSQL POUR RÉCUPÉRATION...
echo.

echo 1. Arrêt de MySQL...
taskkill /f /im mysqld.exe 2>nul
timeout /t 3 /nobreak >nul

echo 2. Création du dossier de récupération...
mkdir "' . $mysqlDataDir . '/' . $recoveryDb . '" 2>nul

echo 3. Copie des fichiers .ibd...
copy "' . $mysqlDataDir . '/' . $originalDb . '\*.ibd" "' . $mysqlDataDir . '/' . $recoveryDb . '\" /Y

echo 4. Copie du fichier .cfg...
copy "' . $mysqlDataDir . '/' . $originalDb . '\db.opt" "' . $mysqlDataDir . '/' . $recoveryDb . '\" /Y 2>nul

echo 5. Redémarrage de MySQL...
echo   - Veuillez redémarrer Laragon manuellement
echo   - Puis exécuter: php repair_recovery.php

pause
';

file_put_contents(__DIR__ . '/recover_data.bat', $batchScript);
echo "   ✅ Script de récupération créé: recover_data.bat\n";

// Créer le script de réparation
$repairScript = '<?php
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
?>';

file_put_contents(__DIR__ . '/repair_recovery.php', $repairScript);
echo "   ✅ Script de réparation créé: repair_recovery.php\n";

echo "\n2️⃣ Instructions de récupération:\n";
echo "   1. Exécuter: recover_data.bat\n";
echo "   2. Redémarrer Laragon/MySQL\n";
echo "   3. Exécuter: php repair_recovery.php\n";
echo "   4. Vérifier les données récupérées\n";

echo "\n3️⃣ Recherche GitHub (optionnel):\n";
echo "   Si tu as un nom d\'utilisateur GitHub, je peux chercher des sauvegardes.\n";
echo "   Donne-moi ton pseudo GitHub pour lancer la recherche.\n";

echo "\n🚀 Prêt à lancer la récupération !\n";
?>
