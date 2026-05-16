@echo off
echo =============================================================================
echo BLOOM-CHLOE - RESTAURATION COMPLÈTE DE LA BASE DE DONNÉES
echo =============================================================================
echo.

echo 1. Arret des serveurs PHP existants...
taskkill /f /im php.exe 2>nul

echo 2. Demarrage du serveur PHP sur le port 8000...
cd /d "C:\laragon\www\Bloom-chloe\backend"
start /B php -S localhost:8000

echo 3. Attente du demarrage du serveur...
timeout /t 3 /nobreak >nul

echo 4. Test de connexion au serveur...
curl -s http://localhost:8000 >nul 2>&1
if %errorlevel% neq 0 (
    echo ERREUR: Le serveur PHP ne repond pas
    pause
    exit /b 1
)

echo.
echo 5. Le serveur est pret! URL: http://localhost:8000
echo.
echo 6. Pour acceder au dashboard admin:
echo    - Allez sur http://localhost:5174
echo    - Connectez-vous avec: admin@bloom-chloe.com / admin123
echo    - Ou accedez directement a: http://localhost:5174/admin
echo.
echo 7. Si vous avez perdu la base de donnees:
echo    - Ouvrez phpMyAdmin (via Laragon)
echo    - Creer la base "bloom_chloe"
echo    - Importez: database/sql/schema.sql
echo    - Importez: database/sql/create_admin.sql
echo    - Importez: database/sql/test_data.sql
echo.

pause
