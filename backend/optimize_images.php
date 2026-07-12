<?php
/**
 * Script d'optimisation des images en WebP
 */

echo "🖼️  OPTIMISATION DES IMAGES EN WEBP\n";
echo "===================================\n\n";

$assetsDir = __DIR__ . '/../frontend/src/assets';
$webpDir = $assetsDir . '/webp';

// Créer le dossier webp s'il n'existe pas
if (!is_dir($webpDir)) {
    mkdir($webpDir, 0755, true);
    echo "✅ Dossier webp créé\n";
}

// Scanner les images JPG et PNG
$images = glob($assetsDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
$totalImages = count($images);
$converted = 0;
$skipped = 0;

echo "📊 Images trouvées: $totalImages\n\n";

foreach ($images as $image) {
    $filename = basename($image, '.jpg');
    $filename = basename($filename, '.jpeg');
    $filename = basename($filename, '.png');
    $webpPath = $webpDir . '/' . $filename . '.webp';
    
    // Vérifier si le WebP existe déjà
    if (file_exists($webpPath)) {
        $skipped++;
        continue;
    }
    
    // Convertir en WebP
    $info = getimagesize($image);
    
    if ($info === false) {
        echo "❌ Erreur lecture: $filename\n";
        continue;
    }
    
    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            $img = imagecreatefromjpeg($image);
            break;
        case IMAGETYPE_PNG:
            $img = imagecreatefrompng($image);
            break;
        default:
            echo "⏭️  Format non supporté: $filename\n";
            continue;
    }
    
    if ($img === false) {
        echo "❌ Erreur chargement: $filename\n";
        continue;
    }
    
    // Sauvegarder en WebP avec qualité 80
    if (imagewebp($img, $webpPath, 80)) {
        $originalSize = filesize($image);
        $webpSize = filesize($webpPath);
        $reduction = round((1 - $webpSize / $originalSize) * 100, 1);
        
        echo "✅ $filename: " . round($originalSize / 1024, 1) . " KB → " . round($webpSize / 1024, 1) . " KB (-$reduction%)\n";
        $converted++;
    } else {
        echo "❌ Erreur conversion: $filename\n";
    }
    
    imagedestroy($img);
}

echo "\n📊 RÉSUMÉ:\n";
echo "   Total images: $totalImages\n";
echo "   Converties: $converted\n";
echo "   Déjà converties: $skipped\n";

// Calculer l'économie totale
$originalTotal = 0;
$webpTotal = 0;

foreach ($images as $image) {
    $filename = basename($image, '.jpg');
    $filename = basename($filename, '.jpeg');
    $filename = basename($filename, '.png');
    $webpPath = $webpDir . '/' . $filename . '.webp';
    
    $originalTotal += filesize($image);
    if (file_exists($webpPath)) {
        $webpTotal += filesize($webpPath);
    }
}

if ($originalTotal > 0) {
    $totalReduction = round((1 - $webpTotal / $originalTotal) * 100, 1);
    $savedSpace = round(($originalTotal - $webpTotal) / 1024 / 1024, 2);
    
    echo "   Économie totale: -$totalReduction% ($savedSpace MB)\n";
}

echo "\n💡 UTILISATION:\n";
echo "   Les images WebP sont dans: frontend/src/assets/webp/\n";
echo "   Modifiez vos composants pour utiliser les WebP:\n";
echo "   <img :src=\"imagePath.replace('.jpg', '.webp')\" />\n";

echo "\n✅ OPTIMISATION TERMINÉE\n";

?>
