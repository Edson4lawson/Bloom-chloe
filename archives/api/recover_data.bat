@echo off
echo ARRÊT DE MYSQL POUR RÉCUPÉRATION...
echo.

echo 1. Arrêt de MySQL...
taskkill /f /im mysqld.exe 2>nul
timeout /t 3 /nobreak >nul

echo 2. Création du dossier de récupération...
mkdir "C:/laragon/data/mysql-8/bloom_chloe_recovery" 2>nul

echo 3. Copie des fichiers .ibd...
copy "C:/laragon/data/mysql-8/bloom_chloe\*.ibd" "C:/laragon/data/mysql-8/bloom_chloe_recovery\" /Y

echo 4. Copie du fichier .cfg...
copy "C:/laragon/data/mysql-8/bloom_chloe\db.opt" "C:/laragon/data/mysql-8/bloom_chloe_recovery\" /Y 2>nul

echo 5. Redémarrage de MySQL...
echo   - Veuillez redémarrer Laragon manuellement
echo   - Puis exécuter: php repair_recovery.php

pause
