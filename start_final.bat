@echo off
echo DÉMARRAGE DES SERVEURS BLOOM CHLOÉ
echo ====================================

echo 1. Démarrage du Backend PHP sur port 8080...
cd /d "%~dp0backend"
start "Backend PHP" cmd /k "php -S 0.0.0.0:8080"

echo 2. Attente démarrage backend...
timeout /t 3 /nobreak >nul

echo 3. Test de l'API...
powershell -Command "try { Invoke-WebRequest -Uri 'http://127.0.0.1:8080/products/get_all.php?per_page=3' -UseBasicParsing | Select-Object StatusCode } catch { $_.Exception.Message }"

echo 4. Démarrage du Frontend...
cd /d "%~dp0"
start "Frontend Vite" cmd /k "npm run dev"

echo.
echo ✅ SERVEURS DÉMARRÉS !
echo.
echo 🔗 Backend: http://localhost:8080
echo 🌐 Frontend: http://localhost:5173
echo 🎯 Dashboard: http://localhost:5173/admin
echo.
echo 👤 Connexion admin: admin@bloomchloe.com / Admin123!
echo.
pause
