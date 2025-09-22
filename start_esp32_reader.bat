@echo off
echo ===============================================
echo ESP32 RFID Reader Launcher
echo ===============================================
echo.

REM Check if Laravel is running
echo Checking Laravel server...
curl -s http://localhost:8000/debug > nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Laravel server is not running!
    echo    Please start it with: php artisan serve
    echo.
    pause
    exit /b 1
)

echo ✅ Laravel server is running

REM Start the ESP32 Reader
echo.
echo Starting ESP32 RFID Reader...
echo Make sure your ESP32 is connected to COM7
echo.
echo Press Ctrl+C to stop the reader
echo ===============================================
echo.

php ESP32Reader.php --port=COM7 --url=http://localhost:8000

echo.
echo ESP32 Reader stopped.
pause
