@echo off
echo RECUPERATION BASE DE DONNEES BLOOM-CHLOE
echo.

echo 1. Arret de MySQL...
taskkill /f /im mysqld.exe 2>nul
timeout /t 2 /nobreak >nul

echo 2. Redemarrage MySQL via Laragon...
echo   - Va dans Laragon et clique "Stop All" puis "Start All"
echo   - Ou redemarre Laragon completement
echo.

echo 3. Verification de la base...
echo   - Ouvre phpMyAdmin
echo   - La base "bloom_chloe" devrait etre la avec toutes tes donnees!
echo.

echo 4. Lancement du frontend...
cd /d "C:\laragon\www\Bloom-chloe\frontend"
start cmd /k "npm run dev"

echo.
echo 5. Acces au dashboard:
echo    - Frontend: http://localhost:5174
echo    - Dashboard: http://localhost:5174/admin
echo.

pause
