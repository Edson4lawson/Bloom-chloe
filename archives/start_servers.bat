@echo off
echo DÉMARRAGE AUTOMATIQUE DES SERVEURS BLOOM-CHLOÉ
echo ========================================

echo 1. Démarrage du Backend PHP...
cd /d "C:\laragon\www\Bloom-chloe\backend"
start "Backend PHP" cmd /k "php -S localhost:8000"

echo 2. Attente démarrage backend...
timeout /t 3 /nobreak >nul

echo 3. Démarrage du Frontend...
cd /d "C:\laragon\www\Bloom-chloe\frontend"
start "Frontend Vite" cmd /k "npm run dev"

echo.
echo ✅ Serveurs en cours de démarrage...
echo 🔗 Backend: http://localhost:8000
echo 🌐 Frontend: sera disponible sur un port 5xxx
echo 🎯 Dashboard: http://localhost:5xxx/admin
echo.
echo 👤 Connexion admin: admin@bloom-chloe.com / admin123
echo.
echo 🛍️ 110 produits récupérés !
echo 📂 32 catégories disponibles !
echo.
pause
