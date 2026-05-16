<?php
$dirs = [
    'admin/analytics', 
    'admin/products', 
    'admin/categories', 
    'admin/orders', 
    'admin/users', 
    'admin/settings', 
    'orders'
];

foreach($dirs as $d) { 
    if(!is_dir($d)) mkdir($d, 0777, true); 
}

$files = [
    'admin/analytics/detailed.php' => 'json_encode([])', 
    'admin/users/get_all.php' => 'json_encode(["users" => $pdo->query("SELECT * FROM users")->fetchAll()])', 
    'admin/settings/get.php' => 'json_encode(["store_name" => "Bloom by Chloé", "currency" => "FCFA"])', 
    'admin/settings/update.php' => 'json_encode(["success" => true, "message" => "Paramètres mis à jour"])',
    'orders/get.php' => 'json_encode(["orders" => $pdo->query("SELECT * FROM orders")->fetchAll()])'
];

foreach($files as $f => $content) { 
    // Handle specific nested paths for relative requires
    $depth = substr_count($f, '/');
    $rel = str_repeat('../', $depth);
    $phpContent = "<?php
require_once __DIR__ . '/{$rel}config/headers.php';
require_once __DIR__ . '/{$rel}config/db.php';
echo $content;
?>";
    file_put_contents($f, $phpContent); 
}
echo "Endpoints created successfully!";
?>
