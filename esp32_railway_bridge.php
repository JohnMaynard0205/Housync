<?php
/**
 * ESP32 RFID Bridge Script for Railway Deployment
 * 
 * This script connects to the ESP32 via USB serial and forwards RFID data to Railway API.
 * The web application and database run on Railway, while ESP32 stays local via USB.
 * 
 * Usage: php esp32_railway_bridge.php [COM_PORT] [BAUD_RATE] [RAILWAY_URL]
 * Example: php esp32_railway_bridge.php COM7 115200 https://your-app.up.railway.app
 */

class ESP32RailwayBridge 
{
    private $serialPort;
    private $baudRate;
    private $handle;
    private $isWindows;
    private $railwayUrl;
    
    public function __construct($serialPort = 'COM7', $baudRate = 115200, $railwayUrl = '')
    {
        $this->serialPort = $serialPort;
        $this->baudRate = $baudRate;
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $this->railwayUrl = rtrim($railwayUrl, '/');
        
        echo "ESP32 RFID → Railway Bridge starting...\n";
        echo "Serial Port: {$this->serialPort}\n";
        echo "Baud Rate: {$this->baudRate}\n";
        echo "OS: " . ($this->isWindows ? 'Windows' : 'Unix') . "\n";
        echo "Railway URL: {$this->railwayUrl}\n";
        echo "API Endpoint: {$this->railwayUrl}/api/rfid/verify\n\n";
        
        if (empty($this->railwayUrl)) {
            echo "❌ ERROR: Railway URL is required!\n";
            echo "Usage: php esp32_railway_bridge.php COM7 115200 https://your-app.up.railway.app\n";
            exit(1);
        }
    }
    
    public function connect()
    {
        try {
            if ($this->isWindows) {
                // Windows COM port handling
                $this->handle = fopen($this->serialPort, 'r+b');
            } else {
                // Unix/Linux serial port handling
                exec("stty -F {$this->serialPort} {$this->baudRate}");
                $this->handle = fopen($this->serialPort, 'r+');
            }
            
            if (!$this->handle) {
                throw new Exception("Cannot open serial port {$this->serialPort}");
            }
            
            // Set non-blocking mode
            stream_set_blocking($this->handle, false);
            
            echo "✅ Connected to {$this->serialPort}\n";
            
            // Test Railway API connection
            if ($this->testRailwayConnection()) {
                echo "✅ Railway API connection verified\n";
            } else {
                echo "⚠️  Railway API connection failed - check URL and network\n";
            }
            
            echo "Waiting for RFID data...\n\n";
            
            return true;
        } catch (Exception $e) {
            echo "❌ Connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function testRailwayConnection()
    {
        $healthUrl = $this->railwayUrl . '/health';
        
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'header' => "User-Agent: ESP32-Bridge/1.0\r\n"
            ]
        ]);
        
        $result = @file_get_contents($healthUrl, false, $context);
        
        if ($result !== false) {
            $data = json_decode($result, true);
            if ($data && isset($data['status']) && $data['status'] === 'healthy') {
                echo "🏥 Health check: {$data['status']} | DB: {$data['database']}\n";
                return true;
            }
        }
        
        return false;
    }
    
    public function listen()
    {
        if (!$this->handle) {
            echo "❌ No connection established\n";
            return;
        }
        
        $buffer = '';
        $lastHealthCheck = 0;
        
        while (true) {
            $data = fread($this->handle, 4096);
            
            if ($data !== false && strlen($data) > 0) {
                $buffer .= $data;
                
                // Process complete lines
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 1);
                    
                    $this->processLine(trim($line));
                }
            }
            
            // Periodic health check (every 5 minutes)
            if (time() - $lastHealthCheck > 300) {
                $this->performHealthCheck();
                $lastHealthCheck = time();
            }
            
            // Small delay to prevent excessive CPU usage
            usleep(100000); // 100ms
        }
    }
    
    private function processLine($line)
    {
        if (empty($line)) {
            return;
        }
        
        echo "[" . date('Y-m-d H:i:s') . "] Raw: $line\n";
        
        // Try to parse JSON data
        $jsonData = json_decode($line, true);
        
        if ($jsonData && isset($jsonData['cardUID'])) {
            $this->processRfidData($jsonData);
        } else {
            // Log non-JSON data (debug info, etc.)
            if (strpos($line, 'Card detected!') !== false || 
                strpos($line, 'UID:') !== false ||
                strpos($line, 'RFID') !== false) {
                echo "📡 ESP32: $line\n";
            }
        }
    }
    
    private function processRfidData($data)
    {
        $cardUID = strtoupper($data['cardUID']);
        $timestamp = $data['timestamp'] ?? null;
        
        echo "🔑 RFID Card Detected: $cardUID\n";
        
        // Send to Railway API
        $response = $this->sendToRailway($cardUID, $timestamp);
        
        if ($response) {
            $this->handleApiResponse($response, $cardUID);
        } else {
            echo "❌ Failed to communicate with Railway API\n";
        }
        
        echo "---\n";
    }
    
    private function sendToRailway($cardUID, $timestamp)
    {
        $apiUrl = $this->railwayUrl . '/api/rfid/verify';
        
        $postData = json_encode([
            'card_uid' => $cardUID,
            'reader_location' => 'main_entrance',
            'timestamp' => $timestamp,
            'device_id' => 'local_bridge_' . gethostname()
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n" .
                           "User-Agent: ESP32-Bridge/1.0\r\n" .
                           "Content-Length: " . strlen($postData) . "\r\n",
                'content' => $postData,
                'timeout' => 10
            ]
        ]);
        
        echo "📤 Sending to Railway: $postData\n";
        
        $result = @file_get_contents($apiUrl, false, $context);
        
        if ($result !== false) {
            return json_decode($result, true);
        }
        
        return null;
    }
    
    private function handleApiResponse($response, $cardUID)
    {
        echo "📥 Railway response: " . json_encode($response) . "\n";
        
        if (isset($response['access_granted'])) {
            if ($response['access_granted']) {
                echo "✅ ACCESS GRANTED";
                if (!empty($response['tenant_name']) && $response['tenant_name'] !== 'null') {
                    echo " for tenant: {$response['tenant_name']}";
                }
                echo "\n";
                
                // Optional: Send success signal back to ESP32
                $this->sendResponseToESP32('granted', $response['tenant_name'] ?? null);
                
            } else {
                echo "❌ ACCESS DENIED";
                if (!empty($response['denial_reason'])) {
                    echo " - Reason: {$response['denial_reason']}";
                }
                echo "\n";
                
                // Optional: Send denial signal back to ESP32
                $this->sendResponseToESP32('denied', null, $response['denial_reason'] ?? null);
            }
        } else {
            echo "⚠️  Unexpected response format from Railway\n";
        }
    }
    
    private function sendResponseToESP32($result, $tenantName = null, $denialReason = null)
    {
        if (!$this->handle) {
            return;
        }
        
        $response = [
            'result' => $result,
            'timestamp' => date('c'),
            'tenant' => $tenantName,
            'reason' => $denialReason
        ];
        
        $jsonResponse = json_encode($response) . "\n";
        fwrite($this->handle, $jsonResponse);
        fflush($this->handle);
        
        echo "📤 Response sent to ESP32: $jsonResponse";
    }
    
    private function performHealthCheck()
    {
        echo "🏥 Performing health check...\n";
        
        if ($this->testRailwayConnection()) {
            echo "✅ Railway connection healthy\n";
        } else {
            echo "⚠️  Railway connection issues detected\n";
        }
    }
    
    public function disconnect()
    {
        if ($this->handle) {
            fclose($this->handle);
            echo "🔌 Disconnected from {$this->serialPort}\n";
        }
    }
    
    public function __destruct()
    {
        $this->disconnect();
    }
}

// Handle command line arguments
$serialPort = $argv[1] ?? 'COM7';
$baudRate = intval($argv[2] ?? 115200);
$railwayUrl = $argv[3] ?? '';

// Prompt for Railway URL if not provided
if (empty($railwayUrl)) {
    echo "Enter your Railway app URL (e.g., https://your-app.up.railway.app): ";
    $railwayUrl = trim(fgets(STDIN));
}

// Create and run bridge
$bridge = new ESP32RailwayBridge($serialPort, $baudRate, $railwayUrl);

// Handle Ctrl+C gracefully
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGINT, function() use ($bridge) {
        echo "\n🛑 Shutting down bridge...\n";
        $bridge->disconnect();
        exit(0);
    });
}

echo "=== ESP32 → Railway Bridge ===\n";
echo "This bridge connects your local ESP32 to Railway cloud database\n";
echo "Press Ctrl+C to stop\n\n";

if ($bridge->connect()) {
    $bridge->listen();
} else {
    echo "❌ Failed to start ESP32 → Railway bridge\n";
    exit(1);
}
