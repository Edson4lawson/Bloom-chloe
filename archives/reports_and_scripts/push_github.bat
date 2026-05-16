@echo off
echo ==========================================
echo PUSH VERS GITHUB - BLOOM CHLOE
echo ==========================================
echo.
echo 1. Verification de la configuration...
git remote -v
echo.
echo 2. Envoi des modifications vers 'main'...
git branch -M main
git push -u origin main
echo.
echo Si une fenetre de connexion apparait, connectez-vous a votre compte GitHub.
echo Si le depot n'est pas trouve, verifiez qu'il est bien cree sur GitHub.
echo.
pause
