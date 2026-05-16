<?php
/**
 * Test de l'API produits pour diagnostiquer l'erreur 500
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 TEST API PRODUITS\n";
echo "==================\n";

try {
    require_once __DIR__ . '/config/headers.php';
    require_once __DIR__ . '/config/db.php';
    
    echo "✅ Fichiers de configuration chargés\n";
    
    // Test connexion BDD
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    echo "✅ Connexion BDD réussie\n";
    
    // Test simple requête
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $result = $stmt->fetch();
    echo "📊 Total produits: " . $result['total'] . "\n";
    
    // Test la requête de l'API
    $query = "
        SELECT p.id, p.name, p.slug, p.description, p.price, p.stock_quantity,
               p.image_url, p.source, p.created_at, p.updated_at,
               c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1
        ORDER BY p.created_at DESC 
        LIMIT 5
    ";
    
    echo "🔍 Exécution de la requête API...\n";
    $stmt = $pdo->query($query);
    $products = $stmt->fetchAll();
    
    echo "✅ Requête réussie - " . count($products) . " produits trouvés\n";
    
    foreach ($products as $product) {
        echo "📦 ID: {$product['id']} | {$product['name']} | {$product['price']} FCFA | Image: {$product['image_url']}\n";
    }
    
    // Test formatage réponse JSON
    $response = [
        'data' => $products,
        'pagination' => [
            'total' => $result['total'],
            'per_page' => 5,
            'current_page' => 1,
            'last_page' => ceil($result['total'] / 5),
            'from' => 1,
            'to' => min(5, $result['total'])
        ]
    ];
    
    echo "\n✅ Formatage JSON réussi\n";
    echo "📄 Réponse JSON:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

?>
