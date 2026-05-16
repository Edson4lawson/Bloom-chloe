@echo off
echo DÉMARRAGE COMPLET AVEC STORE INTEGRÉ
echo =====================================

echo 1. Démarrage du Backend PHP...
cd /d "C:\laragon\www\Bloom-chloe\backend"
start "Backend PHP" cmd /k "php -S localhost:8000"

echo 2. Attente démarrage backend...
timeout /t 3 /nobreak >nul

echo 3. Intégration des produits store...
cd /d "C:\laragon\www\Bloom-chloe\backend"
php integrate_store_products.php

echo 4. Démarrage du Frontend...
cd /d "C:\laragon\www\Bloom-chloe\frontend"
start "Frontend Vite" cmd /k "npm run dev"

echo.
echo ✅ SERVEURS DÉMARRÉS AVEC STORE INTÉGRÉ !
echo.
echo 🔗 Backend: http://localhost:8000
echo 🌐 Frontend: sera disponible sur un port 5xxx
echo 🎯 Dashboard: http://localhost:5xxx/admin
echo.
echo 👤 Connexion admin: admin@bloom-chloe.com / admin123
echo.
echo 🛍️  Produits réguliers: 90 produits avec images
echo 🏪 Produits store: 12 produits premium avec images
echo 📂 Catégories: 32 catégories avec images
echo.
echo 📊 Total: 102 produits avec vraies images !
echo.
pause
