# RFID Security System Setup Guide

This guide will help you set up the complete RFID access control system for your apartment management platform.

## Prerequisites

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- ESP32 with RFID RC522 scanner
- RFID cards (Mifare Classic or compatible)

## 1. Database Setup

### Create MySQL Database
```sql
CREATE DATABASE housync_rfid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Environment Configuration
1. Copy `env-mysql-setup.txt` content to a new `.env` file in the Housync directory
2. Update the database credentials:
   ```
   DB_DATABASE=housync_rfid
   DB_USERNAME=your_mysql_username
   DB_PASSWORD=your_mysql_password
   ```

### Run Migrations
```bash
cd Housync
php artisan key:generate
php artisan migrate
php artisan db:seed  # Optional: adds sample data
```

## 2. ESP32 Hardware Setup

### Required Components
- ESP32 Development Board
- RFID RC522 Module
- Jumper wires
- USB cable for data connection

### Wiring Diagram
```
RFID RC522    ESP32
VCC       ->  3.3V
RST       ->  GPIO 0
GND       ->  GND
MISO      ->  GPIO 19
MOSI      ->  GPIO 23
SCK       ->  GPIO 18
SDA(SS)   ->  GPIO 5
```

### ESP32 Code
The ESP32 code provided in your original message is perfect! Upload it to your ESP32:

1. Install required libraries in Arduino IDE:
   - MFRC522 library
   - ArduinoJson library
2. Upload the code to your ESP32
3. Connect ESP32 to your computer via USB

## 3. Bridge Script Setup

### Start the Bridge
1. **Windows**: Double-click `start_rfid_bridge.bat` or run:
   ```cmd
   php esp32_bridge.php COM3 115200
   ```

2. **Linux/Mac**: 
   ```bash
   php esp32_bridge.php /dev/ttyUSB0 115200
   ```

### Test the Connection
1. Open the bridge script - you should see "Connected to COMx"
2. Scan an RFID card near the ESP32
3. You should see the card UID in the bridge output
4. Check the access_logs table in your database

## 4. Web Interface Usage

### Access Security Management
1. Login as a landlord
2. Navigate to Security (should be in the main menu)
3. You'll see the RFID management dashboard

### Assign RFID Cards to Tenants
1. Click "Assign New Card" 
2. Get the Card UID by:
   - Scanning it with your ESP32 (check bridge output)
   - Looking for unregistered card attempts in access logs
   - Reading it from the physical card if printed
3. Select the tenant and apartment
4. Set optional expiration date and notes
5. Save the assignment

### Monitor Access
- View real-time access attempts in the Security dashboard
- Check detailed access logs with filtering options
- Monitor denied access attempts and reasons

## 5. System Features

### For Landlords
- **Card Management**: Assign, activate, deactivate RFID cards
- **Access Monitoring**: Real-time access logs and statistics
- **Tenant Integration**: Cards linked to tenant assignments
- **Security Controls**: Mark cards as lost/stolen, set expiration dates

### For System Security
- **Access Logging**: Every card scan is logged with timestamp
- **Denial Reasons**: Detailed reasons for denied access
- **Card Validation**: Checks card status, tenant status, expiration
- **Real-time Processing**: Immediate response to ESP32 requests

## 6. Troubleshooting

### Bridge Script Issues
```bash
# Check COM port availability (Windows)
mode

# Check serial port (Linux)
ls /dev/tty*

# Test serial communication
# Make sure no other program is using the port
```

### Database Connection Issues
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

### ESP32 Issues
- Verify wiring connections
- Check power supply (3.3V for RFID module)
- Ensure ESP32 drivers are installed
- Check serial monitor for debug output

### Access Denied Issues
1. Check if card is registered in the system
2. Verify card status is "active"
3. Check if card is expired
4. Verify tenant assignment is active
5. Review access logs for specific denial reasons

## 7. API Integration

The system provides an API endpoint for ESP32 communication:

### Endpoint
```
POST /api/rfid/verify
Content-Type: application/json

{
    "card_uid": "A1B2C3D4",
    "reader_location": "main_entrance"
}
```

### Response
```json
{
    "card_uid": "A1B2C3D4",
    "access_granted": true,
    "tenant_name": "John Doe",
    "denial_reason": null,
    "timestamp": "2025-08-10T12:34:56Z"
}
```

## 8. Security Best Practices

1. **Regular Audits**: Review card assignments and access logs regularly
2. **Immediate Deactivation**: Deactivate cards when tenants move out
3. **Expiration Dates**: Set expiration dates for temporary access
4. **Lost/Stolen Cards**: Immediately mark compromised cards
5. **Backup Access**: Maintain alternative access methods
6. **Database Backups**: Regular backups of access logs and card data

## 9. Maintenance

### Log Retention
Access logs are retained indefinitely by default. To manage storage:

```sql
-- Delete logs older than 1 year
DELETE FROM access_logs WHERE access_time < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

### Database Optimization
```bash
# Optimize database tables
php artisan db:optimize
```

## 10. Support

If you encounter issues:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Review ESP32 serial output
3. Check database connectivity
4. Verify RFID hardware connections

## System Architecture

```
[RFID Card] -> [ESP32 + RC522] -> [USB Serial] -> [PHP Bridge] -> [MySQL Database] -> [Laravel Web Interface]
```

The system provides a complete end-to-end solution for RFID-based access control integrated with your existing tenant management system.
