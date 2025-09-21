@echo off
echo Testing ESP32 Serial Communication...
echo.

REM Default values
set COM_PORT=COM7
set BAUD_RATE=115200

REM Check if arguments were provided
if not "%1"=="" set COM_PORT=%1
if not "%2"=="" set BAUD_RATE=%2

echo Configuration:
echo - Serial Port: %COM_PORT%
echo - Baud Rate: %BAUD_RATE%
echo.
echo This will test basic communication with your ESP32
echo Make sure your ESP32 is connected and has the serial RFID code uploaded
echo.

REM Run the test
php test_serial_communication.php %COM_PORT% %BAUD_RATE%

echo.
echo Test completed.
pause
