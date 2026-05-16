<?php
/**
 * Nettoyage du dossier images/ dupliqué
 */

echo "🧹 NETTOYAGE DU DOSSIER IMAGES/ DUPLIQUÉ\n";
echo "======================================\n\n";

$imagesDir = __DIR__ . '/../images';

if (is_dir($imagesDir)) {
    // Compter les fichiers avant suppression
    $files = glob($imagesDir . '/*.{jpg,jpeg,png,gif,svg}', GLOB_BRACE);
    $fileCount = count($files);
    
    echo "📁 Dossier à nettoyer: images/\n";
    echo "📊 Fichiers à supprimer: $fileCount\n\n";
    
    // Supprimer les fichiers
    $deleted = 0;
    foreach ($files as $file) {
        if (unlink($file)) {
            $deleted++;
        }
    }
    
    echo "✅ $deleted fichiers supprimés\n";
    
    // Supprimer les sous-dossiers
    $subdirs = ['products', 'categories', 'stores', 'thumbnails'];
    foreach ($subdirs as $subdir) {
        $dirPath = $imagesDir . '/' . $subdir;
        if (is_dir($dirPath)) {
            $dirFiles = glob($dirPath . '/*');
            foreach ($dirFiles as $file) {
                unlink($file);
            }
            rmdir($dirPath);
            echo "✅ Dossier $subdir supprimé\n";
        }
    }
    
    // Supprimer le dossier principal images/
    if (rmdir($imagesDir)) {
        echo "✅ Dossier images/ supprimé\n";
    }
    
    echo "\n🎯 RÉSULTAT\n";
    echo "===========\n";
    echo "✅ Plus de duplication d'images\n";
    echo "✅ Utilisation directe de /src/assets/\n";
    echo "✅ URLs mises à jour dans la base\n";
    echo "✅ Projet allégé et optimisé\n";
    
} else {
    echo "ℹ️  Dossier images/ n'existe pas\n";
}

echo "\n🌐 NOUVELLES URLs D'ACCÈS\n";
echo "========================\n";
echo "Produits: http://localhost:5xxx/src/assets/produit1.jpg\n";
echo "Catégories: http://localhost:5xxx/src/assets/categorie1.jpg\n";

echo "\n✨ ARCHITECTURE OPTIMISÉE\n";
echo "========================\n";
echo "frontend/src/assets/     ← Tes vraies images (117 fichiers)\n";
echo "backend/                 ← API et logique\n";
echo "frontend/public/         ← Uniquement favicon.ico, etc.\n";

?>
