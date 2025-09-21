#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>

// RFID Configuration
#define SS_PIN  5
#define RST_PIN 0

// Initialize RFID reader
MFRC522 rfid(SS_PIN, RST_PIN);

// Variables
String lastCardUID = "";
unsigned long lastReadTime = 0;
const unsigned long READ_DELAY = 2000; // 2 seconds between reads

// Scan request variables
bool scanRequestActive = false;
unsigned long scanRequestTime = 0;
const unsigned long SCAN_REQUEST_TIMEOUT = 15000; // 15 seconds timeout for scan requests

void setup() {
  Serial.begin(115200);
  
  // Initialize SPI bus
  SPI.begin();
  
  // Initialize RFID reader
  rfid.PCD_Init();
  
  // Show RFID reader details
  rfid.PCD_DumpVersionToSerial();
  Serial.println("RFID Reader initialized.");
  
  Serial.println("=== ESP32 RFID Serial Bridge Ready ===");
  Serial.println("Mode: Serial Bridge with Manual Scan Support");
  Serial.println("Commands:");
  Serial.println("- SCAN_REQUEST: Start manual scan mode");
  Serial.println("- SCAN_STOP: Stop manual scan mode");
  Serial.println("Ready to scan cards...");
  Serial.println("========================================");
}

void loop() {
  // Check for incoming serial commands
  if (Serial.available()) {
    String command = Serial.readStringUntil('\n');
    command.trim();
    handleSerialCommand(command);
  }
  
  // Check if scan request has timed out
  if (scanRequestActive && (millis() - scanRequestTime > SCAN_REQUEST_TIMEOUT)) {
    scanRequestActive = false;
    Serial.println("SCAN_TIMEOUT");
  }
  
  // Check if new card is present
  if (!rfid.PICC_IsNewCardPresent()) {
    return;
  }
  
  // Read the card
  if (!rfid.PICC_ReadCardSerial()) {
    return;
  }
  
  // Get current time
  unsigned long currentTime = millis();
  
  // Check if enough time has passed since last read (prevent duplicate reads)
  if (currentTime - lastReadTime < READ_DELAY) {
    return;
  }
  
  // Get card UID
  String cardUID = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    cardUID += String(rfid.uid.uidByte[i] < 0x10 ? "0" : "");
    cardUID += String(rfid.uid.uidByte[i], HEX);
  }
  cardUID.toUpperCase();
  
  // Check if it's the same card (avoid duplicate reads)
  if (cardUID == lastCardUID) {
    return;
  }
  
  // Debug output
  Serial.println("🔑 Card detected: " + cardUID);
  
  // Send JSON data to serial bridge
  sendToSerial(cardUID);
  
  // If this was a manual scan request, deactivate it
  if (scanRequestActive) {
    scanRequestActive = false;
    Serial.println("SCAN_COMPLETED");
  }
  
  // Update variables
  lastCardUID = cardUID;
  lastReadTime = currentTime;
  
  // Halt PICC
  rfid.PICC_HaltA();
  // Stop encryption on PCD
  rfid.PCD_StopCrypto1();
  
  Serial.println("---");
}

void sendToSerial(String cardUID) {
  // Create JSON payload for the bridge (based on your working code)
  StaticJsonDocument<200> doc;
  doc["cardUID"] = cardUID;
  doc["timestamp"] = String(millis());
  doc["reader_location"] = "main_entrance";
  doc["device_id"] = "esp32_serial";
  doc["scan_mode"] = scanRequestActive ? "manual" : "automatic";
  
  String jsonPayload;
  serializeJson(doc, jsonPayload);
  
  // Send JSON data to serial (this is what the bridge expects)
  Serial.println(jsonPayload);
  
  // Debug output
  Serial.println("📤 Sent to bridge: " + jsonPayload);
}

void handleSerialCommand(String command) {
  if (command == "SCAN_REQUEST") {
    scanRequestActive = true;
    scanRequestTime = millis();
    Serial.println("SCAN_REQUEST_ACTIVE");
    Serial.println("Please tap your RFID card now...");
    
  } else if (command == "SCAN_STOP") {
    scanRequestActive = false;
    Serial.println("SCAN_REQUEST_STOPPED");
    
  } else if (command == "PING") {
    Serial.println("PONG");
    
  } else if (command == "STATUS") {
    Serial.println("STATUS: " + String(scanRequestActive ? "WAITING_FOR_CARD" : "IDLE"));
    
  } else if (command.startsWith("{")) {
    // Handle JSON responses from bridge (access granted/denied)
    StaticJsonDocument<300> responseDoc;
    DeserializationError error = deserializeJson(responseDoc, command);
    
    if (!error) {
      String result = responseDoc["result"];
      String tenant = responseDoc["tenant"];
      
      if (result == "granted") {
        Serial.println("✅ ACCESS GRANTED");
        if (tenant != "null" && tenant.length() > 0) {
          Serial.println("👤 Tenant: " + tenant);
        }
      } else {
        Serial.println("❌ ACCESS DENIED");
      }
    }
  } else if (command.length() > 0) {
    Serial.println("UNKNOWN_COMMAND: " + command);
  }
}

void printCardInfo() {
  Serial.println("Card UID:");
  for (byte i = 0; i < rfid.uid.size; i++) {
    Serial.print(rfid.uid.uidByte[i] < 0x10 ? " 0" : " ");
    Serial.print(rfid.uid.uidByte[i], HEX);
  }
  Serial.println();
  
  Serial.print("PICC type: ");
  MFRC522::PICC_Type piccType = rfid.PICC_GetType(rfid.uid.sak);
  Serial.println(rfid.PICC_GetTypeName(piccType));
}

// Test function - call this to simulate a card scan
void simulateCardScan(String testUID) {
  Serial.println("🔑 Card detected: " + testUID);
  
  StaticJsonDocument<200> doc;
  doc["cardUID"] = testUID;
  doc["timestamp"] = String(millis());
  doc["reader_location"] = "main_entrance";
  doc["scan_mode"] = scanRequestActive ? "manual" : "test";
  
  String jsonOutput;
  serializeJson(doc, jsonOutput);
  Serial.println(jsonOutput);
  
  if (scanRequestActive) {
    scanRequestActive = false;
    Serial.println("SCAN_COMPLETED");
  }
  
  Serial.println("---");
}
