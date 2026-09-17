<?php
/**
 * Analyse des performances du site
 */

echo "🚀 ANALYSE DES PERFORMANCES DU SITE\n";
echo "====================================\n\n";

$projectRoot = __DIR__ . '/..';

// 1. Analyse des images
echo "1️⃣ ANALYSE DES IMAGES\n";
echo "====================\n";

$assetsDir = $projectRoot . '/frontend/src/assets';
if (is_dir($assetsDir)) {
    $images = glob($assetsDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
    $totalSize = 0;
    $largeImages = [];
    
    foreach ($images as $image) {
        $size = filesize($image);
        $totalSize += $size;
        
        if ($size > 500000) { // > 500KB
            $largeImages[] = [
                'file' => basename($image),
                'size' => round($size / 1024 / 1024, 2) . ' MB'
            ];
        }
    }
    
    echo "📊 Images totales: " . count($images) . "\n";
    echo "📦 Taille totale: " . round($totalSize / 1024 / 1024, 2) . " MB\n";
    echo "⚠️  Images > 500KB: " . count($largeImages) . "\n";
    
    if (!empty($largeImages)) {
        echo "\n🔴 Images à compresser:\n";
        foreach (array_slice($largeImages, 0, 5) as $img) {
            echo "   - {$img['file']} ({$img['size']})\n";
        }
    }
}

echo "\n";

// 2. Analyse des composants Vue
echo "2️⃣ ANALYSE DES COMPOSANTS VUE\n";
echo "============================\n";

$componentsDir = $projectRoot . '/frontend/src/components';
if (is_dir($componentsDir)) {
    $components = glob($componentsDir . '/*.vue');
    $totalSize = 0;
    
    foreach ($components as $component) {
        $totalSize += filesize($component);
    }
    
    echo "📊 Composants: " . count($components) . "\n";
    echo "📦 Taille totale: " . round($totalSize / 1024, 2) . " KB\n";
    echo "📊 Moyenne: " . round($totalSize / count($components), 2) . " KB/composant\n";
}

echo "\n";

// 3. Analyse du bundle
echo "3️⃣ ANALYSE DU BUNDLE\n";
echo "====================\n";

$packageJson = $projectRoot . '/package.json';
if (file_exists($packageJson)) {
    $content = file_get_contents($packageJson);
    $data = json_decode($content, true);
    
    if (isset($data['dependencies'])) {
        echo "📦 Dépendances: " . count($data['dependencies']) . "\n";
        
        $heavyDeps = [];
        foreach ($data['dependencies'] as $dep => $version) {
            $heavyDeps[] = $dep;
        }
        
        echo "🔍 Dépendances principales:\n";
        foreach (array_slice($heavyDeps, 0, 10) as $dep) {
            echo "   - $dep\n";
        }
    }
}

echo "\n";

// 4. Analyse de la configuration Vite
echo "4️⃣ CONFIGURATION VITE\n";
echo "====================\n";

$viteConfig = $projectRoot . '/frontend/vite.config.js';
if (file_exists($viteConfig)) {
    $content = file_get_contents($viteConfig);
    
    echo "✅ vite.config.js trouvé\n";
    
    if (strpos($content, 'build') !== false) {
        echo "✅ Configuration build détectée\n";
    } else {
        echo "⚠️  Pas de configuration build optimisée\n";
    }
    
    if (strpos($content, 'splitChunks') !== false) {
        echo "✅ Code splitting configuré\n";
    } else {
        echo "⚠️  Code splitting non configuré\n";
    }
}

echo "\n";

// 5. Recommandations
echo "5️⃣ RECOMMANDATIONS D'OPTIMISATION\n";
echo "==================================\n";

$recommendations = [];

// Images
if (!empty($largeImages)) {
    $recommendations[] = "🖼️  Compresser les images > 500KB (WebP format)";
    $recommendations[] = "🖼️  Implémenter lazy loading pour les images";
}

// Code splitting
if (!file_exists($viteConfig) || strpos(file_get_contents($viteConfig), 'splitChunks') === false) {
    $recommendations[] = "📦 Activer le code splitting dans vite.config.js";
}

// Vue warnings
$recommendations[] = "⚠️  Corriger les warnings Vue (emits dans Header.vue)";

// API
$recommendations[] = "🚀 Optimiser le caching des requêtes API";
$recommendations[] = "📊 Implémenter la pagination côté serveur";

// Bundle
$recommendations[] = "🌳 Activer le tree shaking pour réduire le bundle";

foreach ($recommendations as $rec) {
    echo "   $rec\n";
}

echo "\n✅ ANALYSE TERMINÉE\n";

?>
