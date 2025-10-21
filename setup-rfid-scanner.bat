@echo off
echo ========================================
echo RFID Scanner Setup for Railway Project
echo ========================================
echo.

echo Checking available COM ports...
powershell -Command "[System.IO.Ports.SerialPort]::GetPortNames()" 2>nul
if %errorlevel% neq 0 (
    echo Available ports: COM1, COM3, COM7, COM8, COM9, COM10, COM11
) else (
    echo Above are your available COM ports
)
echo.

echo Testing connection to Railway app...
curl -s https://housync.up.railway.app/api/system-info
echo.

echo ========================================
echo RFID Scanner Usage Instructions:
echo ========================================
echo.
echo 1. Connect your ESP32 RFID reader to a USB port
echo 2. Identify the COM port (usually COM3, COM7, etc.)
echo 3. Run the scanner with:
echo.
echo    php ESP32Reader.php --port=COM3 --url=https://housync.up.railway.app
echo.
echo 4. Replace COM3 with your actual COM port
echo.
echo Available options:
echo   --port=COMx    Serial port (default: COM3)
echo   --url=URL      Laravel base URL (default: https://housync.up.railway.app)
echo   --help, -h     Show help message
echo.
echo ========================================
echo Example Commands:
echo ========================================
echo.
echo For COM3: php ESP32Reader.php --port=COM3
echo For COM7: php ESP32Reader.php --port=COM7
echo For COM8: php ESP32Reader.php --port=COM8
echo.
echo The scanner will:
echo - Connect to your ESP32 RFID reader
echo - Send card data to your Railway app
echo - Log access attempts in your database
echo - Support web-triggered card scanning
echo.
echo Press any key to continue...
pause >nul
