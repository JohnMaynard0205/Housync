# RFID Card Scanning Setup

This guide explains how to use the new on-demand RFID card scanning feature that allows you to scan cards directly from the web interface.

## Features

- **Manual Card Entry**: Type the Card UID directly (original functionality)
- **Automatic Card Scanning**: Click "Scan Card" button to automatically read the UID from your RFID scanner
- **Real-time Status Updates**: See live feedback during the scanning process
- **Timeout Handling**: Automatic timeout after 15 seconds if no card is detected

## Setup Instructions

### 1. Hardware Requirements

- ESP32 microcontroller with RFID reader (MFRC522)
- USB connection to your computer
- RFID cards to scan

### 2. Software Requirements

- Laravel application running (your Housync system)
- PHP installed on your system
- ESP32 connected via USB/Serial port

### 3. Starting the Enhanced Bridge

#### Option A: Using Laravel Artisan Command (Recommended)
```bash
# Default settings (COM7, 115200 baud, localhost:8000)
php artisan rfid:bridge

# Custom settings
php artisan rfid:bridge COM3 115200 --server-url=http://localhost:8000
php artisan rfid:bridge /dev/ttyUSB0 115200 --server-url=https://your-domain.com
```

#### Option B: Using the Batch File (Windows)
```bash
# Uses Laravel Artisan command internally
start_enhanced_rfid_bridge.bat

# Custom settings
start_enhanced_rfid_bridge.bat COM3 115200 http://localhost:8000
```

#### Option C: Simple Bridge (No Laravel Dependencies)
```bash
# If you encounter Laravel framework issues, use the simple bridge
start_simple_rfid_bridge.bat

# Manual command
php esp32_simple_bridge.php COM7 115200 http://localhost:8000
```

### 4. Using the Scan Feature

1. **Navigate** to the RFID card assignment page in your Housync system
2. **Click** the "Scan Card" button next to the Card UID field
3. **Wait** for the "Please tap your RFID card now..." message
4. **Tap** your RFID card on the scanner
5. **Verify** the Card UID appears automatically in the input field

## How It Works

### Frontend Process
1. User clicks "Scan Card" button
2. JavaScript sends a request to `/api/rfid/scan` 
3. System creates a temporary scan request file
4. Frontend polls `/api/rfid/scan/{scanId}/status` every 500ms
5. When card is detected, UID is automatically filled in

### Backend Process
1. Enhanced bridge monitors for scan request files
2. When a card is tapped, bridge captures the UID
3. Bridge updates the scan request file with the UID
4. Bridge notifies the web application via API
5. Frontend receives the UID and updates the form

## Troubleshooting

### Laravel Framework Errors
If you encounter Laravel container/reflection errors:
- **Use the Artisan command**: `php artisan rfid:bridge` instead of the direct PHP script
- **Alternative**: Use the simple bridge: `php esp32_simple_bridge.php`
- **Check Laravel installation**: Ensure all dependencies are installed with `composer install`
- **Clear caches**: Run `php artisan config:clear` and `php artisan cache:clear`

### Bridge Not Connecting
- Check if the COM port is correct (Device Manager → Ports)
- Ensure no other application is using the serial port
- Verify ESP32 is properly connected and powered
- Try different COM ports if unsure (COM3, COM4, COM5, etc.)

### Scan Button Not Working
- Check browser console for JavaScript errors
- Verify the Laravel server is running
- Ensure the enhanced bridge is running
- Check network connectivity between bridge and server
- Verify API routes are registered: `php artisan route:list | findstr "api/rfid"`

### Cards Not Being Detected
- Verify ESP32 RFID reader is working (check serial monitor)
- Ensure cards are compatible with MFRC522 reader
- Check wiring connections between ESP32 and RFID reader
- Test with Arduino IDE serial monitor first

### Scan Timeout Issues
- Default timeout is 15 seconds
- Make sure to tap the card within the timeout period
- Check if the bridge is processing other cards simultaneously
- Verify the bridge is monitoring scan request files

## API Endpoints

The following endpoints are available for the scanning functionality:

- `POST /api/rfid/scan` - Initiate a card scan request
- `GET /api/rfid/scan/{scanId}/status` - Check scan status
- `POST /api/rfid/scan/update` - Update scan status (used by bridge)

## Configuration

### Timeout Settings
You can modify the scan timeout in the JavaScript code:
```javascript
timeout: 15 // Change this value (in seconds)
```

### Polling Interval
The frontend polls for updates every 500ms. You can adjust this:
```javascript
}, 500); // Change this value (in milliseconds)
```

## Security Considerations

- The scan API endpoints are rate-limited (60 requests per minute)
- Temporary scan files are automatically cleaned up after completion
- Only authenticated users can access the scan functionality
- All scan requests have built-in timeouts to prevent resource leaks

## Files Modified/Added

### New Files:
- `esp32_enhanced_bridge.php` - Enhanced bridge with scan support
- `start_enhanced_rfid_bridge.bat` - Batch file to start the bridge
- `RFID_SCAN_SETUP.md` - This documentation file

### Modified Files:
- `app/Http/Controllers/RfidController.php` - Added scan API endpoints
- `routes/web.php` - Added scan API routes
- `resources/views/landlord/security/create.blade.php` - Added scan button and JavaScript

## Support

If you encounter issues:
1. Check the bridge console output for error messages
2. Check browser developer tools for JavaScript errors
3. Verify all components are running (Laravel server, enhanced bridge, ESP32)
4. Ensure proper network connectivity between components
