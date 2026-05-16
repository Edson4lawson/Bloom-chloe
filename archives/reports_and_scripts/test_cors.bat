@echo off
echo TEST CORS ET API
echo ================

echo 1. Test de l'API avec CORS...
powershell -Command "$response = Invoke-WebRequest -Uri 'http://localhost:8080/products/get_all.php?per_page=3' -UseBasicParsing; Write-Host 'StatusCode:' $response.StatusCode; if ($response.Headers['Access-Control-Allow-Origin']) { Write-Host 'CORS Origin:' $response.Headers['Access-Control-Allow-Origin'] } else { Write-Host 'CORS: Non trouvé' }"

echo.
echo 2. Test avec headers complets...
powershell -Command "$response = Invoke-WebRequest -Uri 'http://localhost:8080/products/get_all.php?per_page=3' -Headers @{'Origin'='http://localhost:5174'} -UseBasicParsing; Write-Host 'StatusCode:' $response.StatusCode; $response.Headers | Where-Object { $_ -like '*Access-Control*' }"

echo.
echo 3. Vérification des ports...
netstat -ano | findstr :8080
netstat -ano | findstr :5174

echo.
echo 4. Configuration actuelle :
echo   - Backend: http://localhost:8080
echo   - Frontend: http://localhost:5174
echo   - CORS: http://localhost:5174

pause
