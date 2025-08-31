@echo off
echo =====================================
echo   HOUSYNC RFID - Railway Deployment
echo =====================================
echo.

echo Checking prerequisites...

REM Check if Railway CLI is installed
railway --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Railway CLI not found!
    echo Please install it first: npm install -g @railway/cli
    echo Then run this script again.
    pause
    exit /b 1
)

echo ✅ Railway CLI found

REM Check if git is initialized
if not exist .git (
    echo ❌ Git repository not found!
    echo Please initialize git and push to GitHub first:
    echo   git init
    echo   git add .
    echo   git commit -m "Initial commit"
    echo   git branch -M main
    echo   git remote add origin https://github.com/yourusername/your-repo.git
    echo   git push -u origin main
    pause
    exit /b 1
)

echo ✅ Git repository found

echo.
echo === Step 1: Railway Login ===
railway login

echo.
echo === Step 2: Initialize Railway Project ===
railway init

echo.
echo === Step 3: Deploy Application ===
echo This will deploy your application to Railway...
railway up

echo.
echo === Step 4: Add MySQL Database ===
echo Please add MySQL service manually in Railway dashboard:
echo 1. Go to your Railway project dashboard
echo 2. Click "New Service"
echo 3. Select "Database" → "MySQL"
echo 4. Railway will create the MySQL instance
echo.
echo Press any key when you've added the MySQL service...
pause

echo.
echo === Step 5: Set Environment Variables ===
echo Please set the environment variables in Railway dashboard:
echo 1. Go to your project → Variables tab
echo 2. Copy variables from railway-env-variables.txt
echo 3. Generate APP_KEY with: php artisan key:generate --show
echo.
echo Press any key when you've set the environment variables...
pause

echo.
echo === Step 6: Run Database Migrations ===
echo Running database migrations...
railway run php artisan migrate --force

echo.
echo === Step 7: Seed Database (Optional) ===
set /p seed="Do you want to seed the database with sample data? (y/n): "
if /i "%seed%"=="y" (
    railway run php artisan db:seed --force
    echo ✅ Database seeded
) else (
    echo ⏭️  Database seeding skipped
)

echo.
echo === Step 8: Clear Caches ===
echo Clearing application caches...
railway run php artisan config:clear
railway run php artisan route:clear
railway run php artisan view:clear

echo.
echo === Step 9: Test Deployment ===
echo Opening your application...
railway open

echo.
echo =====================================
echo   🎉 DEPLOYMENT COMPLETE! 🎉
echo =====================================
echo.
echo Your RFID Security System is now live!
echo.
echo Next Steps:
echo 1. ✅ Test the web interface
echo 2. ✅ Update ESP32 code with your Railway URL
echo 3. ✅ Configure WiFi credentials in ESP32
echo 4. ✅ Test RFID card scanning
echo.
echo Important URLs:
railway open --print
echo Health Check: [Your URL]/health
echo RFID API: [Your URL]/api/rfid/verify
echo Admin Login: [Your URL]/login
echo.
echo ESP32 Configuration:
echo - Update serverURL in esp32_wifi_rfid_code.ino
echo - Set your WiFi credentials
echo - Upload code to ESP32
echo.
echo For support, check:
echo - Railway logs: railway logs
echo - Laravel logs: railway run php artisan log:show
echo.
echo =====================================
pause
