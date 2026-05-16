<?php
/**
 * Création des endpoints backend manquants
 */

echo "🔧 CRÉATION DES ENDPOINTS BACKEND\n";
echo "===============================\n\n";

$backendDir = __DIR__;

// Créer le dossier products s'il n'existe pas
if (!is_dir($backendDir . '/products')) {
    mkdir($backendDir . '/products', 0755, true);
    echo "✅ Dossier products créé\n";
}

// 1. Créer products/get_all.php
$getAllContent = '<?php
/**
 * API pour récupérer tous les produits
 */

// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// Gérer OPTIONS
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Vérifier la méthode
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit();
}

try {
    // Connexion BDD
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    // Paramètres
    $page = max(1, (int)($_GET["page"] ?? 1));
    $perPage = max(1, min(200, (int)($_GET["per_page"] ?? 10)));
    $offset = ($page - 1) * $perPage;
    
    // Requête
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.slug, p.description, p.price, p.stock_quantity,
               p.image_url, p.source, p.created_at, p.updated_at,
               c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([$perPage, $offset]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $total = (int)$totalStmt->fetch()["total"];
    
    echo json_encode([
        "data" => $products,
        "pagination" => [
            "total" => $total,
            "per_page" => $perPage,
            "current_page" => $page,
            "last_page" => ceil($total / $perPage),
            "from" => $total > 0 ? $offset + 1 : 0,
            "to" => min($offset + $perPage, $total)
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Erreur serveur",
        "message" => $e->getMessage()
    ]);
}
?>';

file_put_contents($backendDir . '/products/get_all.php', $getAllContent);
echo "✅ products/get_all.php créé\n";

// 2. Créer auth/login.php
$authDir = $backendDir . '/auth';
if (!is_dir($authDir)) {
    mkdir($authDir, 0755, true);
    echo "✅ Dossier auth créé\n";
}

$loginContent = '<?php
/**
 * API pour l\'authentification
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit();
}

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data["email"] ?? "";
    $password = $data["password"] ?? "";
    
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(["error" => "Email et mot de passe requis"]);
        exit();
    }
    
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    $stmt = $pdo->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user["password"])) {
        http_response_code(401);
        echo json_encode(["error" => "Identifiants incorrects"]);
        exit();
    }
    
    // Générer tokens
    $accessToken = bin2hex(random_bytes(32));
    $refreshToken = bin2hex(random_bytes(32));
    
    // Mettre à jour les tokens (simulation)
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $user["id"],
            "email" => $user["email"],
            "role" => $user["role"]
        ],
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur"]);
}
?>';

file_put_contents($authDir . '/login.php', $loginContent);
echo "✅ auth/login.php créé\n";

// 3. Créer categories/get_all.php
$categoriesDir = $backendDir . '/categories';
if (!is_dir($categoriesDir)) {
    mkdir($categoriesDir, 0755, true);
    echo "✅ Dossier categories créé\n";
}

$categoriesContent = '<?php
/**
 * API pour récupérer toutes les catégories
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE bloom_chloe");
    
    $stmt = $pdo->query("
        SELECT id, name, description, image_url 
        FROM categories 
        ORDER BY name
    ");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "data" => $categories,
        "total" => count($categories)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur"]);
}
?>';

file_put_contents($categoriesDir . '/get_all.php', $categoriesContent);
echo "✅ categories/get_all.php créé\n";

echo "\n🎯 ENDPOINTS CRÉÉS AVEC SUCCÈS\n";
echo "   - products/get_all.php ✅\n";
echo "   - auth/login.php ✅\n";
echo "   - categories/get_all.php ✅\n";

echo "\n📋 STRUCTURE CRÉÉE:\n";
echo "backend/\n";
echo "├── products/\n";
echo "│   └── get_all.php\n";
echo "├── auth/\n";
echo "│   └── login.php\n";
echo "└── categories/\n";
echo "    └── get_all.php\n";

?>
