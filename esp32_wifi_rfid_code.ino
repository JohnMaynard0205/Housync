#include <SPI.h>
#include <MFRC522.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// RFID Configuration
#define SS_PIN  5
#define RST_PIN 0

// Initialize RFID reader
MFRC522 rfid(SS_PIN, RST_PIN);

// WiFi credentials
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Railway deployment URL (update this with your actual Railway URL)
const char* serverURL = "https://your-app-name.up.railway.app/api/rfid/verify";

// Variables
String lastCardUID = "";
unsigned long lastReadTime = 0;
const unsigned long READ_DELAY = 2000; // 2 seconds between reads
const unsigned long WIFI_TIMEOUT = 10000; // 10 seconds WiFi connection timeout

void setup() {
  Serial.begin(115200);
  
  // Initialize SPI bus
  SPI.begin();
  
  // Initialize RFID reader
  rfid.PCD_Init();
  
  // Show RFID reader details
  rfid.PCD_DumpVersionToSerial();
  Serial.println("RFID Reader initialized.");
  
  // Connect to WiFi
  connectToWiFi();
  
  Serial.println("=== ESP32 RFID Security System Ready ===");
  Serial.println("Mode: WiFi + HTTP API");
  Serial.println("Server: " + String(serverURL));
  Serial.println("Ready to scan cards...");
  Serial.println("========================================");
}

void loop() {
  // Check WiFi connection
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi disconnected. Reconnecting...");
    connectToWiFi();
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
  
  // Check if it's the same card (avoid duplicate reads)
  if (cardUID == lastCardUID) {
    return;
  }
  
  Serial.println("🔑 Card detected: " + cardUID);
  
  // Send data to server
  if (WiFi.status() == WL_CONNECTED) {
    sendToServer(cardUID);
  } else {
    Serial.println("❌ WiFi not connected. Cannot verify access.");
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

void connectToWiFi() {
  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);
  
  unsigned long startTime = millis();
  while (WiFi.status() != WL_CONNECTED && millis() - startTime < WIFI_TIMEOUT) {
    delay(500);
    Serial.print(".");
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("");
    Serial.println("✅ WiFi connected!");
    Serial.println("IP address: " + WiFi.localIP().toString());
    Serial.println("Signal strength: " + String(WiFi.RSSI()) + " dBm");
  } else {
    Serial.println("");
    Serial.println("❌ WiFi connection failed!");
    Serial.println("Please check your credentials and network.");
  }
}

void sendToServer(String cardUID) {
  HTTPClient http;
  http.begin(serverURL);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("User-Agent", "ESP32-RFID-Scanner/1.0");
  
  // Create JSON payload
  StaticJsonDocument<200> doc;
  doc["card_uid"] = cardUID;
  doc["reader_location"] = "main_entrance";
  doc["timestamp"] = String(millis());
  doc["device_id"] = WiFi.macAddress();
  
  String jsonPayload;
  serializeJson(doc, jsonPayload);
  
  Serial.println("📤 Sending to server: " + jsonPayload);
  
  int httpResponseCode = http.POST(jsonPayload);
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println("📥 Server response (" + String(httpResponseCode) + "): " + response);
    
    // Parse response to show access result
    StaticJsonDocument<300> responseDoc;
    DeserializationError error = deserializeJson(responseDoc, response);
    
    if (!error) {
      bool accessGranted = responseDoc["access_granted"];
      String tenantName = responseDoc["tenant_name"];
      String denialReason = responseDoc["denial_reason"];
      
      if (accessGranted) {
        Serial.println("✅ ACCESS GRANTED");
        if (tenantName != "null" && tenantName.length() > 0) {
          Serial.println("👤 Tenant: " + tenantName);
        }
        
        // Optional: Add LED/buzzer indication for granted access
        // digitalWrite(GREEN_LED_PIN, HIGH);
        // delay(1000);
        // digitalWrite(GREEN_LED_PIN, LOW);
        
      } else {
        Serial.println("❌ ACCESS DENIED");
        if (denialReason != "null" && denialReason.length() > 0) {
          Serial.println("🚫 Reason: " + denialReason);
        }
        
        // Optional: Add LED/buzzer indication for denied access
        // digitalWrite(RED_LED_PIN, HIGH);
        // delay(500);
        // digitalWrite(RED_LED_PIN, LOW);
      }
    }
  } else {
    Serial.println("❌ HTTP Error: " + String(httpResponseCode));
    Serial.println("Check your server URL and network connection.");
  }
  
  http.end();
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

// Optional: Add functions for LED/buzzer feedback
/*
void setupIndicators() {
  pinMode(GREEN_LED_PIN, OUTPUT);
  pinMode(RED_LED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);
}

void indicateAccess(bool granted) {
  if (granted) {
    digitalWrite(GREEN_LED_PIN, HIGH);
    tone(BUZZER_PIN, 1000, 200); // Short beep
    delay(1000);
    digitalWrite(GREEN_LED_PIN, LOW);
  } else {
    digitalWrite(RED_LED_PIN, HIGH);
    tone(BUZZER_PIN, 200, 500); // Long low beep
    delay(500);
    digitalWrite(RED_LED_PIN, LOW);
  }
}
*/
