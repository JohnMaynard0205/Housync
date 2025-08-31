@echo off
echo =====================================
echo   ESP32 → Railway RFID Bridge
echo =====================================
echo.

REM Check if COM port is provided as argument
if "%1"=="" (
    set COM_PORT=COM7
    echo Using default COM port: COM7
) else (
    set COM_PORT=%1
    echo Using COM port: %1
)

REM Check if baud rate is provided as argument
if "%2"=="" (
    set BAUD_RATE=115200
    echo Using default baud rate: 115200
) else (
    set BAUD_RATE=%2
    echo Using baud rate: %2
)

REM Check if Railway URL is provided as argument
if "%3"=="" (
    echo.
    echo Please enter your Railway app URL:
    echo Example: https://your-app-name.up.railway.app
    set /p RAILWAY_URL="Railway URL: "
) else (
    set RAILWAY_URL=%3
    echo Using Railway URL: %3
)

echo.
echo Configuration:
echo - Serial Port: %COM_PORT%
echo - Baud Rate: %BAUD_RATE%
echo - Railway URL: %RAILWAY_URL%
echo.

echo Make sure your ESP32 is connected to %COM_PORT%
echo Press any key to start the bridge or Ctrl+C to cancel...
pause > nul

echo.
echo Starting ESP32 → Railway Bridge...
echo.
echo This bridge will:
echo 1. Read RFID data from ESP32 via USB
echo 2. Send verification requests to Railway API
echo 3. Log all access attempts to cloud database
echo 4. Show real-time access results
echo.
echo Press Ctrl+C to stop the bridge
echo.

php esp32_railway_bridge.php %COM_PORT% %BAUD_RATE% %RAILWAY_URL%

echo.
echo Bridge stopped.
pause
