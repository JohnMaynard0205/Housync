# RFID Scanner Setup Guide for Railway Deployment

## Overview
Your Railway project has a complete RFID access control system with ESP32 integration. This guide will help you configure your RFID scanner to work with your deployed application.

## System Components

### 1. Hardware Requirements
- ESP32 development board with RFID reader (MFRC522)
- USB cable for ESP32 connection
- RFID cards/tags (13.56MHz)
- Computer with PHP installed

### 2. Software Components
- **ESP32Reader.php** - PHP script for serial communication
- **Laravel API endpoints** - For RFID data processing
- **Web interface** - For RFID card management
- **Database** - For storing cards and access logs

## Quick Setup

### Step 1: Check Your Setup
Run the setup script to verify your environment:
```bash
# Windows PowerShell
powershell -ExecutionPolicy Bypass -File setup-rfid-scanner.ps1

# Or Windows Command Prompt
setup-rfid-scanner.bat
```

### Step 2: Connect ESP32
1. Connect your ESP32 RFID reader to a USB port
2. Install ESP32 drivers if needed
3. Note the COM port (usually COM3, COM7, etc.)

### Step 3: Run the Scanner
```bash
# Basic usage (replace COM3 with your port)
php ESP32Reader.php --port=COM3

# With custom URL (if different)
php ESP32Reader.php --port=COM7 --url=https://your-app.up.railway.app

# Show help
php ESP32Reader.php --help
```

## API Endpoints Available

Your deployed app has these RFID endpoints:

### For ESP32 Communication
- `POST /api/rfid-scan` - Main endpoint for card scanning
- `POST /api/rfid/verify` - Verify card access
- `GET /api/rfid/test` - Test connection
- `GET /api/system-info` - System status

### For Web Interface
- `GET /api/rfid/latest-uid` - Get last scanned card
- `POST /api/rfid/scan/request` - Request card scan from web
- `GET /api/rfid/recent-logs` - Get recent access logs

## Web Interface Access

### RFID Management Dashboard
Access your RFID management interface at:
```
https://housync.up.railway.app/landlord/security
```

Features available:
- View all RFID cards
- Assign cards to tenants
- Monitor access logs
- Manage card status (active/inactive)
- Real-time access monitoring

### Card Assignment Process
1. Log in as landlord
2. Go to Security → RFID Management
3. Click "Assign New Card"
4. Scan a card using ESP32Reader.php
5. Assign to a tenant
6. Set expiration date (optional)

## Configuration Options

### Environment Variables
Add these to your Railway environment if needed:
```
RFID_COM_PORT=COM3
RFID_BAUD_RATE=115200
RFID_SCAN_TIMEOUT=15
```

### ESP32Reader Configuration
The ESP32Reader.php supports these options:
- `--port=COMx` - Serial port (default: COM3)
- `--url=URL` - Laravel base URL (default: https://housync.up.railway.app)
- `--help` - Show help message

## Testing Your Setup

### 1. Test Railway Connection
```bash
curl https://housync.up.railway.app/api/system-info
```

### 2. Test RFID API
```bash
curl -X POST https://housync.up.railway.app/api/rfid/test
```

### 3. Test ESP32Reader
```bash
php ESP32Reader.php --help
```

### 4. Test Card Scanning
1. Run ESP32Reader.php
2. Tap an RFID card
3. Check the console output
4. Verify in web interface

## Troubleshooting

### Common Issues

#### ESP32 Not Detected
- Check USB cable connection
- Install ESP32 drivers
- Try different COM ports
- Check Device Manager (Windows)

#### Connection Failed
- Verify Railway app URL
- Check internet connection
- Ensure API endpoints are accessible
- Check firewall settings

#### Cards Not Recognized
- Verify card frequency (13.56MHz)
- Check ESP32 wiring
- Ensure MFRC522 is properly connected
- Test with different cards

#### Permission Denied
- Run as administrator (Windows)
- Check COM port permissions
- Ensure port is not in use by other applications

### Debug Mode
Enable debug output by modifying ESP32Reader.php:
```php
// Add more verbose logging
echo "Debug: Processing card data...\n";
```

## Advanced Features

### Web-Triggered Scanning
Your system supports scanning cards from the web interface:
1. Go to RFID management page
2. Click "Scan New Card"
3. The system will wait for ESP32Reader.php to detect a card
4. Card UID will be automatically filled

### Real-Time Monitoring
- Access logs are updated in real-time
- Web interface shows live card scanning
- Entry/exit tracking (IN/OUT states)
- Denial reasons for blocked cards

### Card Management
- Bulk card assignment
- Expiration date management
- Card status tracking
- Tenant reassignment
- Access history per card

## Security Features

### Access Control
- Card activation/deactivation
- Expiration date enforcement
- Tenant assignment validation
- Apartment-specific access

### Logging
- All access attempts logged
- Denial reasons tracked
- Timestamp recording
- Device identification

### Rate Limiting
- API endpoints are rate limited
- Prevents abuse and spam
- 60 requests per minute limit

## Support

### Log Files
Check these locations for debugging:
- Laravel logs: `storage/logs/laravel.log`
- ESP32Reader output: Console window
- Access logs: Web interface

### Database Tables
Your RFID data is stored in:
- `rfid_cards` - Card information
- `tenant_rfid_assignments` - Card-tenant relationships
- `access_logs` - All access attempts

### API Documentation
Test API endpoints using tools like:
- Postman
- curl commands
- Browser developer tools

## Next Steps

1. **Test the setup** using the provided scripts
2. **Configure your ESP32** with the correct COM port
3. **Run ESP32Reader.php** to start scanning
4. **Access the web interface** to manage cards
5. **Assign cards to tenants** through the dashboard
6. **Monitor access logs** in real-time

Your RFID system is fully deployed and ready to use with your Railway application!
