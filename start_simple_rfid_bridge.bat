@echo off
echo Starting Simple ESP32 RFID Bridge...
echo This bridge works without Laravel framework dependencies
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

REM Start the simple bridge (no Laravel dependencies)
php esp32_simple_bridge.php %COM_PORT% %BAUD_RATE% %SERVER_URL%

echo.
echo Bridge stopped.
pause
