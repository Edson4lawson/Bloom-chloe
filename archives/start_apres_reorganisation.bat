@echo off
echo 🚀 DÉMARRAGE COMPLET APRÈS RÉORGANISATION
echo ==========================================

echo 1. Vérification de la structure...
if exist "frontend" (
    echo    ✅ Dossier frontend trouvé
) else (
    echo    ❌ Dossier frontend manquant
    goto :end
)

if exist "backend" (
    echo    ✅ Dossier backend trouvé
) else (
    echo    ❌ Dossier backend manquant
    goto :end
)

echo.
echo 2. Démarrage du Backend PHP sur port 8080...
cd /d "C:\laragon\www\Bloom-chloe\backend"
start "Backend PHP" cmd /k "php -S localhost:8080 -t backend"

echo 3. Attente démarrage backend...
timeout /t 3 /nobreak >nul

echo 4. Test de l'API produits...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:8080/products/get_all.php?per_page=3' -UseBasicParsing; Write-Host '✅ API Status:' $response.StatusCode } catch { Write-Host '❌ Erreur API:' $_.Exception.Message }"

echo 5. Démarrage du Frontend Vite...
cd /d "C:\laragon\www\Bloom-chloe\frontend"
start "Frontend Vite" cmd /k "npm run dev"

echo.
echo ✅ SERVEURS DÉMARRÉS APRÈS RÉORGANISATION !
echo.
echo 🔗 Backend: http://localhost:8080
echo 🌐 Frontend: sera disponible sur un port 5xxx
echo 🎯 Dashboard: http://localhost:5xxx/admin
echo.
echo 👤 Connexion admin: admin@bloom-chloe.com / admin123
echo.
echo 📊 Structure actuelle:
echo    - Frontend: frontend/ (Vue 3 + Vite)
echo    - Backend: backend/ (API PHP)
echo    - Images: frontend/src/assets/ (133 fichiers)
echo    - API: products/get_all.php, auth/login.php, categories/get_all.php
echo.
echo 🛍️  Produits attendus: 122 avec vraies images
echo 🏪  Store: 12 produits premium
echo 📂  Catégories: 12 catégories avec images
echo.
echo 🌐 Accès directs:
echo    - API: http://localhost:8080/products/get_all.php
echo    - Frontend: http://localhost:5xxx
echo    - Admin: http://localhost:5xxx/admin
echo.
echo ⚠️  Important: MySQL/Laragon doit être démarré !
echo.

:end
pause
