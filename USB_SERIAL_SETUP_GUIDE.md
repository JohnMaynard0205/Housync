# USB/Serial RFID Setup Guide

This guide will help you set up the ESP32 RFID system using USB/Serial communication instead of WiFi, and fix the manual scanning issue.

## 🔧 Problem Fixed

The issue was that the ESP32 code was designed for WiFi communication, but you're using USB/Serial. The bridge wasn't properly sending scan request commands to the ESP32.

## 📋 Step-by-Step Setup

### Step 1: Upload New ESP32 Code

1. **Open Arduino IDE**
2. **Load the new code**: `esp32_serial_rfid_code.ino`
3. **Set your board**: ESP32 Dev Module
4. **Set your COM port**: Check Device Manager for the correct port
5. **Upload the code** to your ESP32

### Step 2: Test Basic Communication

Before running the bridge, test if the ESP32 is responding:

```bash
# Test basic serial communication
php test_serial_communication.php COM7 115200

# Replace COM7 with your actual port
php test_serial_communication.php COM3 115200
```

**Expected output:**
```
✅ Connected to COM7
Sending command: PING
Response: PONG
---
Sending command: STATUS  
Response: STATUS: IDLE
---
Sending command: SCAN_REQUEST
Response: SCAN_REQUEST_ACTIVE
Please tap your RFID card now...
---
```

### Step 3: Run the Bridge

Choose one of these options:

**Option A: Laravel Artisan Command (Recommended)**
```bash
php artisan rfid:bridge COM7 115200 --server-url=http://localhost:8000
```

**Option B: Simple Bridge**
```bash
php esp32_simple_bridge.php COM7 115200 http://localhost:8000
```

**Option C: Batch File**
```bash
start_enhanced_rfid_bridge.bat COM7 115200 http://localhost:8000
```

### Step 4: Test Manual Scanning

1. **Open your Housync web application**
2. **Navigate to**: RFID Card Assignment page
3. **Click**: "Scan Card" button
4. **Watch the bridge console** - you should see:
   ```
   🔍 Scan request sent to ESP32, waiting for card tap...
   ```
5. **Tap your RFID card** on the ESP32 reader
6. **Check the web interface** - the Card UID should appear automatically

## 🔍 How It Works Now

### Communication Flow:
1. **Web Interface** → Click "Scan Card" button
2. **Laravel API** → Creates scan request file
3. **Bridge** → Detects scan request file
4. **Bridge** → Sends `SCAN_REQUEST` command to ESP32 via serial
5. **ESP32** → Enters manual scan mode, waits for card
6. **ESP32** → Detects card tap, sends JSON data back
7. **Bridge** → Receives card data, fulfills scan request
8. **Web Interface** → Gets card UID and fills the form

### ESP32 Commands:
- `SCAN_REQUEST` - Start manual scan mode
- `SCAN_STOP` - Stop manual scan mode  
- `PING` - Test communication (responds with `PONG`)
- `STATUS` - Get current status

### ESP32 Responses:
- `SCAN_REQUEST_ACTIVE` - Manual scan mode started
- `SCAN_COMPLETED` - Card was scanned successfully
- `SCAN_TIMEOUT` - No card detected within timeout
- `{"cardUID":"A1B2C3D4",...}` - JSON data with card info

## 🚨 Troubleshooting

### ESP32 Not Responding
1. **Check COM port**: Use Device Manager to find the correct port
2. **Check connections**: Ensure USB cable is properly connected
3. **Reset ESP32**: Press the reset button and try again
4. **Test with Arduino Serial Monitor**: Open Arduino IDE → Tools → Serial Monitor

### Bridge Not Connecting
1. **Close other applications**: Make sure no other app is using the COM port
2. **Try different ports**: COM3, COM4, COM5, etc.
3. **Check permissions**: Run command prompt as administrator

### Manual Scan Not Working
1. **Check bridge console**: Should show "Scan request sent to ESP32"
2. **Check ESP32 response**: Should show "SCAN_REQUEST_ACTIVE"
3. **Verify card reader**: Test with Arduino Serial Monitor first
4. **Check web browser console**: Look for JavaScript errors

### Card Not Detected
1. **Check wiring**: Ensure RFID reader is properly connected
2. **Test cards**: Try different RFID cards
3. **Check distance**: Hold card close to the reader
4. **Verify card type**: Ensure cards are compatible with MFRC522

## 🔧 Hardware Setup

### ESP32 to MFRC522 Wiring:
```
ESP32    →    MFRC522
3.3V     →    3.3V
GND      →    GND
Pin 5    →    SS/SDA
Pin 18   →    SCK
Pin 19   →    MISO
Pin 23   →    MOSI
Pin 0    →    RST
```

**Note**: No LEDs or buzzer are required - the system works with just the RFID scanner and provides feedback through the serial console.

## 📝 Files Created/Modified

### New Files:
- `esp32_serial_rfid_code.ino` - ESP32 code for USB/Serial communication
- `test_serial_communication.php` - Test script for serial communication
- `USB_SERIAL_SETUP_GUIDE.md` - This guide

### Modified Files:
- `app/Console/Commands/RfidBridgeCommand.php` - Added ESP32 command support
- `esp32_simple_bridge.php` - Added ESP32 command support

## ✅ Expected Results

After following this guide, you should be able to:

1. ✅ **Click "Scan Card"** button in web interface
2. ✅ **See scan request** in bridge console
3. ✅ **ESP32 enters scan mode** with serial console messages
4. ✅ **Tap RFID card** and see detection in console
5. ✅ **Card UID appears** automatically in web form
6. ✅ **Success indication** in serial console

## 🆘 Still Having Issues?

If you're still not getting the Card UID:

1. **Run the test script first**: `php test_serial_communication.php COM7`
2. **Check the bridge logs** for error messages
3. **Verify ESP32 code upload** was successful
4. **Test with Arduino Serial Monitor** to ensure ESP32 is working
5. **Check browser developer tools** for JavaScript errors

The key fix was adding proper command communication between the bridge and ESP32 for manual scan requests!
