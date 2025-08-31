@echo off
echo Starting ESP32 RFID Bridge...
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

echo.
echo Make sure your ESP32 is connected to %COM_PORT%
echo Press any key to continue or Ctrl+C to cancel...
pause > nul

echo.
echo Starting bridge...
php esp32_bridge.php %COM_PORT% %BAUD_RATE%

echo.
echo Bridge stopped.
pause
