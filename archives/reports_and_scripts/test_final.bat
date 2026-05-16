@echo off
echo TEST COMPLET FRONTEND-BACKEND
echo ============================

echo 1. Test exact comme le frontend (avec Origin)...
powershell -Command "$headers = @{'Origin'='http://localhost:5174'; 'Content-Type'='application/json'}; try { $response = Invoke-WebRequest -Uri 'http://localhost:8080/products/get_all.php?per_page=5&sort_by=created_at&sort_order=ASC' -Headers $headers -UseBasicParsing; Write-Host '✅ StatusCode:' $response.StatusCode; Write-Host '✅ CORS:' $response.Headers['Access-Control-Allow-Origin']; Write-Host '✅ Produits:' ($response.Content | ConvertFrom-Json).data.Count } catch { Write-Host '❌ Erreur:' $_.Exception.Message }"

echo.
echo 2. Configuration actuelle :
echo   - Backend: http://localhost:8080 ✅
echo   - Frontend: http://localhost:5174 ✅
echo   - API_URL: http://localhost:8080 ✅
echo   - withCredentials: false ✅
echo   - CORS: * ✅

echo.
echo 3. Ports actifs :
netstat -ano | findstr :8080
netstat -ano | findstr :5174

echo.
echo 4. Si ça ne fonctionne pas, rafraîchis le frontend (F5) !
echo    Les changements de api.js nécessitent un rechargement.

pause
