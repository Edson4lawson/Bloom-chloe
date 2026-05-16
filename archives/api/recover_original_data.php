<?php
/**
 * Script de récupération des données depuis les fichiers .ibd existants
 * Tente de récupérer les vraies données de l'ancienne base bloom_chloe
 */

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    echo "🔍 DIAGNOSTIC DE RÉCUPÉRATION DES DONNÉES\n";
    echo "=====================================\n\n";
    
    // Connexion au serveur MySQL
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si la base bloom_chloe_original existe
    echo "1️⃣ Recherche de bases de données existantes...\n";
    $stmt = $pdo->query("SHOW DATABASES LIKE '%bloom%'");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($databases)) {
        echo "   📁 Bases trouvées : " . implode(', ', $databases) . "\n";
    } else {
        echo "   ❌ Aucune base bloom trouvée\n";
    }
    
    // Vérifier les fichiers .ibd
    echo "\n2️⃣ Analyse des fichiers .ibd existants...\n";
    $ibdDir = 'C:/laragon/data/mysql-8/bloom_chloe';
    if (is_dir($ibdDir)) {
        $files = scandir($ibdDir);
        $ibdFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'ibd';
        });
        
        echo "   📁 Fichiers .ibd trouvés : " . count($ibdFiles) . "\n";
        foreach ($ibdFiles as $file) {
            $size = filesize($ibdDir . '/' . $file);
            echo "      - $file (" . number_format($size) . " bytes)\n";
        }
    } else {
        echo "   ❌ Dossier .ibd non trouvé\n";
    }
    
    // Tenter de créer une base de récupération
    echo "\n3️⃣ Tentative de récupération...\n";
    
    // Arrêter MySQL pour manipuler les fichiers
    echo "   ⚠️  Note: Cette opération nécessite d'arrêter MySQL temporairement\n";
    echo "   ⚠️  Veuillez arrêter Laragon/MySQL manuellement\n";
    echo "   ⚠️  Puis relancer ce script\n";
    
    // Créer une base de récupération
    $pdo->exec("DROP DATABASE IF EXISTS bloom_chloe_recovery");
    $pdo->exec("CREATE DATABASE bloom_chloe_recovery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "   ✅ Base de récupération bloom_chloe_recovery créée\n";
    
    // Importer le schéma
    $schemaFile = __DIR__ . '/../database/sql/schema.sql';
    if (file_exists($schemaFile)) {
        echo "   📄 Import du schéma...\n";
        $sql = file_get_contents($schemaFile);
        
        // Remplacer bloom_chloe par bloom_chloe_recovery
        $sql = str_replace('USE bloom_chloe', 'USE bloom_chloe_recovery', $sql);
        
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^\s*--/', $query)) {
                try {
                    $pdo->exec($query);
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "      ⚠️  " . substr($e->getMessage(), 0, 100) . "...\n";
                    }
                }
            }
        }
        echo "   ✅ Schéma importé\n";
    }
    
    echo "\n4️⃣ Instructions pour récupération manuelle...\n";
    echo "   1. Arrêter complètement Laragon/MySQL\n";
    echo "   2. Copier les fichiers .ibd depuis C:/laragon/data/mysql-8/bloom_chloe/\n";
    echo "   3. Coller dans le nouveau dossier de la base bloom_chloe_recovery\n";
    echo "   4. Redémarrer MySQL\n";
    echo "   5. Exécuter: REPAIR TABLE products, categories, users, orders;\n";
    
    echo "\n🎯 Alternative: Recherche sur GitHub...\n";
    echo "   Souhaites-tu que je cherche des sauvegardes sur ton GitHub?\n";
    echo "   Si oui, donne-moi ton nom d'utilisateur GitHub.\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
