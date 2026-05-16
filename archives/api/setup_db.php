<?php
/**
 * Script corrigé pour créer la base de données bloom_chloe
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
    
    // Créer les tables manuellement
    echo "📋 Création des tables...\n";
    
    // Table users
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) DEFAULT NULL,
            last_name VARCHAR(100) DEFAULT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            address TEXT DEFAULT NULL,
            role ENUM('customer', 'admin') DEFAULT 'customer',
            token VARCHAR(255) DEFAULT NULL,
            token_expires_at DATETIME DEFAULT NULL,
            failed_login_attempts INT DEFAULT 0,
            locked_until DATETIME DEFAULT NULL,
            last_login_at DATETIME DEFAULT NULL,
            last_login_ip VARCHAR(45) DEFAULT NULL,
            email_verified_at DATETIME DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_token (token)
        ) ENGINE=InnoDB
    ");
    echo "✅ Table users créée\n";
    
    // Table categories
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            image_url VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_name (name)
        ) ENGINE=InnoDB
    ");
    echo "✅ Table categories créée\n";
    
    // Table products
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            price DECIMAL(10,2) NOT NULL,
            category_id INT DEFAULT NULL,
            stock_quantity INT DEFAULT 0,
            image_url VARCHAR(255) DEFAULT NULL,
            thumbnail VARCHAR(255) DEFAULT NULL,
            slug VARCHAR(255) DEFAULT NULL,
            source ENUM('store', 'external') DEFAULT 'store',
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            INDEX idx_name (name),
            INDEX idx_category (category_id),
            INDEX idx_source (source),
            INDEX idx_slug (slug)
        ) ENGINE=InnoDB
    ");
    echo "✅ Table products créée\n";
    
    // Table orders
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
            shipping_address TEXT DEFAULT NULL,
            payment_method VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_user (user_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB
    ");
    echo "✅ Table orders créée\n";
    
    // Table order_items
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            INDEX idx_order (order_id),
            INDEX idx_product (product_id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Table order_items créée\n";
    
    // Table cart
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cart (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            session_id VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            INDEX idx_user (user_id),
            INDEX idx_session (session_id),
            INDEX idx_product (product_id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Table cart créée\n";
    
    // Créer admin
    echo "👤 Création admin...\n";
    $adminEmail = 'admin@bloom-chloe.com';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $pdo->exec("
        INSERT IGNORE INTO users (email, password, first_name, last_name, role, email_verified_at) 
        VALUES (?, ?, 'Admin', 'Bloom Chloé', 'admin', NOW())
    ");
    echo "✅ Admin créé!\n";
    
    // Insérer catégories
    echo "🌱 Insertion catégories...\n";
    $categories = [
        ['Robes', 'Magnifiques robes pour toutes occasions'],
        ['Chaussures', 'Chaussures élégantes et confortables'],
        ['Accessoires', 'Accessoires de mode tendance'],
        ['Sacs', 'Sacs à main et sacs à dos fashion'],
        ['Bijoux', 'Bijoux fins et bijoux de fantaisie']
    ];
    
    foreach ($categories as $cat) {
        $pdo->exec("
            INSERT IGNORE INTO categories (name, description) 
            VALUES (?, ?)
        ");
    }
    echo "✅ Catégories créées!\n";
    
    // Insérer produits
    echo "🛍️ Insertion produits...\n";
    $products = [
        ['Robe Soirée Élégante', 'Robe longue en soie parfaite pour les occasions spéciales', 45000, 1, 'robe-soiree-elegante'],
        ['Robe d\'Été Florale', 'Robe légère avec motif floral pour l\'été', 25000, 1, 'robe-ete-florale'],
        ['Talons Hauts Noirs', 'Talons aiguilles élégants de 12cm', 35000, 2, 'talons-hauts-noirs'],
        ['Bottines en Cuir', 'Bottines confortables en cuir véritable', 55000, 2, 'bottines-cuir'],
        ['Sac à Main Luxe', 'Sac à main en cuir avec fermoir doré', 75000, 4, 'sac-main-luxe'],
        ['Sacoche Tendance', 'Sacoche moderne pour un look casual', 28000, 4, 'sacoche-tendance'],
        ['Collier Perles', 'Collier élégant en perles de culture', 18000, 5, 'collier-perles'],
        ['Bracelet Or', 'Bracelet fin en plaqué or', 22000, 5, 'bracelet-or']
    ];
    
    foreach ($products as $product) {
        $pdo->exec("
            INSERT IGNORE INTO products (name, description, price, category_id, slug, source) 
            VALUES (?, ?, ?, ?, ?, 'store')
        ");
    }
    echo "✅ Produits créés!\n";
    
    // Vérification
    echo "\n📊 Vérification finale:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "   - Utilisateurs: " . $result['count'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "   - Produits: " . $result['count'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch();
    echo "   - Catégories: " . $result['count'] . "\n";
    
    echo "\n🎉 Base de données prête !\n";
    echo "   - Admin: admin@bloom-chloe.com / admin123\n";
    echo "   - URL: http://localhost:5177\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
?>
