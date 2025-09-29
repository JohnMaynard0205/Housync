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
const unsigned long READ_DELAY = 2000; // 2 second minimum between any reads
bool waitForCardRemoval = false; // require card to be removed before next read
unsigned long cardRemovedTime = 0; // track when card was removed
const unsigned long REMOVAL_DELAY = 1000; // 1 second after card removal before allowing next scan

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
  Serial.println("Mode: Serial Bridge");
  Serial.println("Ready to scan cards...");
  Serial.println("========================================");
}

void loop() {
  // Require card removal between reads to allow IN/OUT toggling on repeated taps
  if (waitForCardRemoval) {
    // If no card is present anymore, start removal timer
    if (!rfid.PICC_IsNewCardPresent()) {
      if (cardRemovedTime == 0) {
        cardRemovedTime = millis(); // start removal timer
        Serial.println("📤 Card removed, waiting before next scan...");
      } else if (millis() - cardRemovedTime >= REMOVAL_DELAY) {
        // Card has been removed long enough
        waitForCardRemoval = false;
        cardRemovedTime = 0;
        Serial.println("✅ Ready for next scan");
      }
    } else {
      cardRemovedTime = 0; // reset timer if card is still present
    }
    return; // don't process while waiting
  }

  // Check for commands (keeping PING for testing)
  if (Serial.available()) {
    String command = Serial.readStringUntil('\n');
    command.trim();
    
    if (command == "PING") {
      Serial.println("PONG");
    } else if (command == "STATUS") {
      Serial.println("STATUS: AUTOMATIC_SCANNING");
    }
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
  
  // Check if enough time has passed since last read
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
  
  // We allow the same UID again as long as card was removed in between
  
  // Debug output
  Serial.println("🔑 Card detected: " + cardUID);
  
  // Send JSON data to serial bridge
  sendToSerial(cardUID);
  
  // Update variables
  lastCardUID = cardUID;
  lastReadTime = currentTime;
  waitForCardRemoval = true; // must remove card before next read
  
  // Halt PICC
  rfid.PICC_HaltA();
  // Stop encryption on PCD
  rfid.PCD_StopCrypto1();
  
  Serial.println("---");
}

void sendToSerial(String cardUID) {
  // Create JSON payload for the bridge (exactly like your working code)
  StaticJsonDocument<200> doc;
  doc["cardUID"] = cardUID;
  doc["timestamp"] = String(millis());
  doc["reader_location"] = "main_entrance";
  doc["device_id"] = "esp32_serial";
  doc["scan_mode"] = "automatic";
  
  String jsonPayload;
  serializeJson(doc, jsonPayload);
  
  // Send JSON data to serial (this is what the bridge expects)
  Serial.println(jsonPayload);
  
  // Debug output
  Serial.println("📤 Sent to bridge: " + jsonPayload);
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
