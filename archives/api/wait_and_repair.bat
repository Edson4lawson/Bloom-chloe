@echo off
echo ATTENTE DE REDÉMARRAGE MYSQL...
echo.

:wait
echo Vérification de MySQL...
netstat -ano | findstr :3306 >nul
if %errorlevel% equ 0 (
    echo ✅ MySQL est démarré !
    echo.
    echo Lancement de la réparation...
    cd /d "C:\laragon\www\Bloom-chloe\backend"
    php repair_recovery.php
    goto end
) else (
    echo ⏳ MySQL en cours de démarrage...
    timeout /t 3 /nobreak >nul
    goto wait
)

:end
pause
