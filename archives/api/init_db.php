<?php
/**
 * Script d'initialisation de la base de données Bloom-Chloe
 * 
 * Usage: php backend/init_db.php
 * 
 * Ce script :
 * 1. Crée la base de données et les tables
 * 2. Insère les catégories
 * 3. Insère les produits (seed)
 */

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connexion au serveur MySQL sans sélectionner de base
    echo "🔌 Connexion au serveur MySQL...\n";
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Lecture du fichier schema.sql
    $schemaFile = __DIR__ . '/../database/sql/schema.sql';
    if (!file_exists($schemaFile)) {
        die("❌ Erreur: Le fichier schema.sql est introuvable ($schemaFile)\n");
    }
    
    echo "📄 Lecture du schéma SQL...\n";
    $sql = file_get_contents($schemaFile);
    
    echo "🔧 Exécution des requêtes de création...\n\n";
    
    // Splitter par ';' pour exécuter commande par commande
    $queries = explode(';', $sql);
    $success = 0;
    $skipped = 0;
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query) && !preg_match('/^\s*--/', $query)) {
            try {
                $pdo->exec($query);
                $success++;
            } catch (PDOException $e) {
                // Ignorer les erreurs pour les tables/données qui existent déjà
                if (strpos($e->getMessage(), 'already exists') !== false || 
                    strpos($e->getMessage(), 'Duplicate') !== false) {
                    $skipped++;
                } else {
                    echo "  ⚠️  " . substr($query, 0, 60) . "...\n";
                    echo "     " . $e->getMessage() . "\n\n";
                }
            }
        }
    }
    
    echo "✅ Schéma: $success requêtes exécutées, $skipped ignorées (déjà existantes)\n\n";
    
    // Lancer le script de seed des produits
    echo "🌱 Insertion des produits...\n\n";
    include __DIR__ . '/seed_products.php';
    
    echo "\n🎉 Base de données 'bloom_chloe' initialisée avec succès !\n";
    echo "\n📋 Résumé:\n";
    echo "   - Tables créées: users, refresh_tokens, email_verifications, login_logs,\n";
    echo "     categories, products, cart, favorites, orders, order_items, payments, rate_limits\n";
    echo "   - Admin par défaut: admin@bloom-chloe.com / Admin123!\n";
    echo "   - Produits: ~89 articles insérés\n";
    
} catch (PDOException $e) {
    if ($e->getCode() == 1045) {
         echo "❌ Erreur d'accès: Impossible de se connecter avec user='$user' et password='$pass'.\n";
         echo "Veuillez vérifier vos identifiants MySQL locaux.\n";
    } elseif ($e->getCode() == 2002) {
         echo "❌ Impossible de se connecter au serveur MySQL sur $host.\n";
         echo "Vérifiez que votre serveur Laragon est bien lancé.\n";
    } else {
         echo "❌ Erreur inattendue: " . $e->getMessage() . "\n";
    }
    exit(1);
}
?>
