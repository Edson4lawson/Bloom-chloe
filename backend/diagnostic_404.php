<?php
/**
 * Diagnostic précis des erreurs 404
 */

echo "🔍 DIAGNOSTIC PRÉCIS DES ERREURS 404\n";
echo "====================================\n\n";

$projectRoot = __DIR__ . '/..';

// 1. Vérifier la structure frontend
echo "1️⃣ STRUCTURE FRONTEND COMPLÈTE\n";
echo "==============================\n";

$frontendDir = $projectRoot . '/frontend';
$requiredFiles = [
    'index.html' => 'Page principale',
    'src/main.js' => 'Point d\'entrée JS',
    'src/App.vue' => 'Composant principal',
    'src/router/index.js' => 'Router Vue',
    'src/services/api.js' => 'Service API',
    'src/stores/products.js' => 'Store produits'
];

foreach ($requiredFiles as $file => $description) {
    $path = $frontendDir . '/' . $file;
    $exists = file_exists($path);
    $size = $exists ? filesize($path) : 0;
    echo sprintf("   %s %s (%d bytes) - %s\n", 
        $exists ? '✅' : '❌', 
        $file, 
        $size, 
        $description
    );
}

echo "\n";

// 2. Vérifier le contenu de main.js
echo "2️⃣ CONTENU DE main.js\n";
echo "=====================\n";

$mainJsPath = $frontendDir . '/src/main.js';
if (file_exists($mainJsPath)) {
    $content = file_get_contents($mainJsPath);
    echo "✅ main.js trouvé\n";
    echo "📄 Contenu:\n";
    echo $content . "\n";
    
    // Vérifier les imports
    if (strpos($content, 'import') !== false) {
        echo "✅ Imports détectés\n";
        
        // Extraire les imports
        preg_match_all("/import.*from\s+['\"](.*)['\"]/", $content, $matches);
        if (!empty($matches[1])) {
            echo "📦 Imports trouvés:\n";
            foreach ($matches[1] as $import) {
                echo "   - $import\n";
            }
        }
    }
} else {
    echo "❌ main.js manquant\n";
}

echo "\n";

// 3. Vérifier le contenu de index.html
echo "3️⃣ CONTENU DE index.html\n";
echo "========================\n";

$indexPath = $frontendDir . '/index.html';
if (file_exists($indexPath)) {
    $content = file_get_contents($indexPath);
    echo "✅ index.html trouvé\n";
    echo "📄 Contenu:\n";
    echo $content . "\n";
    
    // Vérifier le script src
    if (strpos($content, '/src/main.js') !== false) {
        echo "✅ Script src correct: /src/main.js\n";
    } else {
        echo "❌ Script src incorrect ou manquant\n";
    }
} else {
    echo "❌ index.html manquant\n";
}

echo "\n";

// 4. Vérifier les imports dans main.js
echo "4️⃣ VÉRIFICATION DES IMPORTS\n";
echo "==========================\n";

$mainJsPath = $frontendDir . '/src/main.js';
if (file_exists($mainJsPath)) {
    $content = file_get_contents($mainJsPath);
    
    // Vérifier chaque import
    $imports = [
        './router' => $frontendDir . '/src/router',
        './App.vue' => $frontendDir . '/src/App.vue',
        './style.css' => $frontendDir . '/src/style.css'
    ];
    
    foreach ($imports as $import => $fullPath) {
        if (strpos($content, $import) !== false) {
            $exists = file_exists($fullPath) || is_dir($fullPath);
            echo sprintf("   %s %s\n", 
                $exists ? '✅' : '❌', 
                $import
            );
        }
    }
}

echo "\n";

// 5. Vérifier le router
echo "5️⃣ VÉRIFICATION DU ROUTER\n";
echo "========================\n";

$routerDir = $frontendDir . '/src/router';
if (is_dir($routerDir)) {
    $routerFiles = glob($routerDir . '/*.js');
    echo "✅ Dossier router trouvé\n";
    
    foreach ($routerFiles as $file) {
        $filename = basename($file);
        echo "   📄 $filename\n";
    }
} else {
    echo "❌ Dossier router manquant\n";
}

echo "\n";

// 6. Test d'accès direct
echo "6️⃣ TEST D'ACCÈS DIRECT\n";
echo "======================\n";

$filesToTest = [
    'index.html' => 'http://localhost:5173/',
    'src/main.js' => 'http://localhost:5173/src/main.js'
];

foreach ($filesToTest as $file => $url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'method' => 'GET'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response) {
        echo "✅ $file: Accessible\n";
    } else {
        echo "❌ $file: Erreur 404 ou inaccessible\n";
    }
}

echo "\n";

// 7. Recommandations
echo "7️⃣ RECOMMANDATIONS\n";
echo "==================\n";

$problems = [];

if (!file_exists($frontendDir . '/index.html')) {
    $problems[] = "Créer index.html avec le bon script src";
}

if (!file_exists($frontendDir . '/src/main.js')) {
    $problems[] = "Créer main.js avec les bons imports";
}

if (!is_dir($frontendDir . '/src/router')) {
    $problems[] = "Créer le dossier router et index.js";
}

if (!file_exists($frontendDir . '/src/App.vue')) {
    $problems[] = "Créer App.vue";
}

if (empty($problems)) {
    echo "✅ Tous les fichiers nécessaires sont présents\n";
    echo "💡 Le problème vient peut-être des chemins relatifs\n";
} else {
    echo "❌ Problèmes à corriger:\n";
    foreach ($problems as $problem) {
        echo "   - $problem\n";
    }
}

echo "\n✅ DIAGNOSTIC TERMINÉ\n";

?>
