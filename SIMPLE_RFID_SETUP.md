# Simple RFID Setup (No LEDs/Buzzer)

## 🎯 Quick Setup for RFID Scanner Only

This is a simplified setup guide for using just the ESP32 with RFID scanner via USB/Serial - no LEDs or buzzer required.

## 🔌 Hardware Connection

**You only need:**
- ESP32 microcontroller
- MFRC522 RFID reader
- USB cable to computer
- RFID cards

**Wiring:**
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

## 📝 Step-by-Step Setup

### 1. Upload ESP32 Code
- Open Arduino IDE
- Load `esp32_serial_rfid_code.ino`
- Select your ESP32 board and COM port
- Upload the code

### 2. Test Connection
```bash
# Test if ESP32 responds
php test_serial_communication.php COM7 115200
```

### 3. Start the Bridge
```bash
# Use Laravel command
php artisan rfid:bridge COM7 115200

# Or simple bridge
php esp32_simple_bridge.php COM7 115200 http://localhost:8000
```

### 4. Use Manual Scanning
1. Open Housync web interface
2. Go to RFID Card Assignment page  
3. Click "Scan Card" button
4. Tap RFID card on reader
5. Card UID appears automatically!

## 📱 What You'll See

**In Bridge Console:**
```
🔍 Scan request sent to ESP32, waiting for card tap...
🔑 RFID Card Detected: A1B2C3D4
✅ Scan request fulfilled with card: A1B2C3D4
```

**In Serial Monitor (Optional):**
```
SCAN_REQUEST_ACTIVE
Please tap your RFID card now...
🔑 Card detected: A1B2C3D4
{"cardUID":"A1B2C3D4","timestamp":"12345","reader_location":"main_entrance","scan_mode":"manual"}
SCAN_COMPLETED
```

**In Web Interface:**
- Card UID field automatically fills with: `A1B2C3D4`
- Success message appears
- Form ready to submit

## 🔧 Troubleshooting

**Card not detected?**
- Check wiring connections
- Try different RFID cards
- Hold card close to reader
- Check COM port in Device Manager

**Bridge not connecting?**
- Close other applications using the COM port
- Try different COM ports (COM3, COM4, etc.)
- Reset ESP32 and try again

**Manual scan not working?**
- Verify bridge shows "Scan request sent to ESP32"
- Check ESP32 responds with "SCAN_REQUEST_ACTIVE"
- Make sure web server is running

## ✅ That's It!

No LEDs, no buzzer, no complex setup - just:
1. Wire RFID reader to ESP32
2. Upload code
3. Run bridge
4. Click "Scan Card" and tap card
5. UID appears automatically!

The system provides all feedback through the console messages, making it simple and reliable.
