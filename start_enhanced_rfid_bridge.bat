@echo off
echo Starting Enhanced ESP32 RFID Bridge...
echo This bridge supports both access control and on-demand card scanning
echo.

REM Default values
set COM_PORT=COM7
set BAUD_RATE=115200
set SERVER_URL=http://localhost:8000

REM Check if arguments were provided
if not "%1"=="" set COM_PORT=%1
if not "%2"=="" set BAUD_RATE=%2
if not "%3"=="" set SERVER_URL=%3

echo Configuration:
echo - Serial Port: %COM_PORT%
echo - Baud Rate: %BAUD_RATE%
echo - Server URL: %SERVER_URL%
echo.
echo Make sure your ESP32 is connected and the web server is running!
echo Press Ctrl+C to stop the bridge
echo.

REM Start the enhanced bridge using Laravel Artisan command
php artisan rfid:bridge %COM_PORT% %BAUD_RATE% --server-url=%SERVER_URL%

echo.
echo Bridge stopped.
pause
