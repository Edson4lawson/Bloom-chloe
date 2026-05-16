<?php
/**
 * Script de création de la base de données bloom_chloe
 */

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connexion au serveur MySQL sans sélectionner de base
    echo "🔌 Connexion au serveur MySQL...\n";
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la base de données
    echo "🗄️ Création de la base bloom_chloe...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS bloom_chloe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Base bloom_chloe créée avec succès!\n";
    
    // Sélectionner la base
    $pdo->exec("USE bloom_chloe");
    
    // Importer le schéma
    $schemaFile = __DIR__ . '/../database/sql/schema.sql';
    if (file_exists($schemaFile)) {
        echo "📄 Import du schéma...\n";
        $sql = file_get_contents($schemaFile);
        
        // Exécuter les requêtes une par une
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^\s*--/', $query)) {
                try {
                    $pdo->exec($query);
                } catch (PDOException $e) {
                    // Ignorer les erreurs de tables existantes
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "⚠️ Erreur: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        echo "✅ Schéma importé!\n";
    }
    
    // Importer l'admin
    $adminFile = __DIR__ . '/../database/sql/create_admin.sql';
    if (file_exists($adminFile)) {
        echo "👤 Création admin...\n";
        $sql = file_get_contents($adminFile);
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^\s*--/', $query)) {
                try {
                    $pdo->exec($query);
                } catch (PDOException $e) {
                    // Ignorer les erreurs de doublons
                    if (strpos($e->getMessage(), 'Duplicate') === false) {
                        echo "⚠️ Admin: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        echo "✅ Admin créé!\n";
    }
    
    // Importer les données de test
    $testFile = __DIR__ . '/../database/sql/test_data.sql';
    if (file_exists($testFile)) {
        echo "🌱 Insertion données de test...\n";
        $sql = file_get_contents($testFile);
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^\s*--/', $query)) {
                try {
                    $pdo->exec($query);
                } catch (PDOException $e) {
                    // Ignorer les erreurs de doublons
                    if (strpos($e->getMessage(), 'Duplicate') === false) {
                        echo "⚠️ Test data: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        echo "✅ Données de test insérées!\n";
    }
    
    // Vérifier les produits
    echo "\n📊 Vérification des produits:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "   - Produits trouvés: " . $result['count'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch();
    echo "   - Catégories trouvées: " . $result['count'] . "\n";
    
    echo "\n🎉 Base de données prête !\n";
    echo "   - Admin: admin@bloom-chloe.com / admin123\n";
    echo "   - URL: http://localhost:5177\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
?>
