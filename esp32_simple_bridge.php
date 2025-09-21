<?php
/**
 * Simple ESP32 RFID Bridge Script (Standalone)
 * 
 * This is a simplified version that works without Laravel framework.
 * It only handles on-demand scanning for the web interface.
 * 
 * Usage: php esp32_simple_bridge.php [COM_PORT] [BAUD_RATE] [SERVER_URL]
 * Example: php esp32_simple_bridge.php COM7 115200 http://localhost:8000
 */

class ESP32SimpleBridge 
{
    private $serialPort;
    private $baudRate;
    private $handle;
    private $isWindows;
    private $serverUrl;
    private $lastScanCheck = 0;
    private $running = true;
    
    public function __construct($serialPort = 'COM7', $baudRate = 115200, $serverUrl = 'http://localhost:8000')
    {
        $this->serialPort = $serialPort;
        $this->baudRate = $baudRate;
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $this->serverUrl = rtrim($serverUrl, '/');
        
        echo "ESP32 Simple RFID Bridge starting...\n";
        echo "Serial Port: {$this->serialPort}\n";
        echo "Baud Rate: {$this->baudRate}\n";
        echo "OS: " . ($this->isWindows ? 'Windows' : 'Unix') . "\n";
        echo "Server URL: {$this->serverUrl}\n\n";
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
            echo "Waiting for RFID data and scan requests...\n\n";
            
            return true;
        } catch (Exception $e) {
            echo "❌ Connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function listen()
    {
        if (!$this->handle) {
            echo "❌ No connection established\n";
            return;
        }
        
        $buffer = '';
        
        while ($this->running) {
            // Read serial data
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
            
            // Check for scan requests every 500ms
            if (time() - $this->lastScanCheck >= 0.5) {
                $this->checkForScanRequests();
                $this->lastScanCheck = time();
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
        
        // Check if this is for a pending scan request
        $this->checkAndFulfillScanRequest($cardUID);
        
        // Send to server for access verification
        $this->sendToServer($cardUID, $timestamp);
        
        echo "---\n";
    }
    
    private function sendToServer($cardUID, $timestamp)
    {
        $apiUrl = $this->serverUrl . '/api/rfid/verify';
        
        $postData = json_encode([
            'card_uid' => $cardUID,
            'reader_location' => 'main_entrance',
            'timestamp' => $timestamp,
            'device_id' => 'simple_bridge_' . gethostname()
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n" .
                           "User-Agent: ESP32-Simple-Bridge/1.0\r\n" .
                           "Content-Length: " . strlen($postData) . "\r\n",
                'content' => $postData,
                'timeout' => 10
            ]
        ]);
        
        echo "📤 Sending to server: $postData\n";
        
        $result = @file_get_contents($apiUrl, false, $context);
        
        if ($result !== false) {
            $response = json_decode($result, true);
            $this->handleApiResponse($response, $cardUID);
        } else {
            echo "❌ Failed to communicate with server\n";
        }
    }
    
    private function handleApiResponse($response, $cardUID)
    {
        echo "📥 Server response: " . json_encode($response) . "\n";
        
        if (isset($response['access_granted'])) {
            if ($response['access_granted']) {
                echo "✅ ACCESS GRANTED";
                if (!empty($response['tenant_name']) && $response['tenant_name'] !== 'null') {
                    echo " for tenant: {$response['tenant_name']}";
                }
                echo "\n";
                
                // Send success signal back to ESP32
                $this->sendResponseToESP32('granted', $response['tenant_name'] ?? null);
                
            } else {
                echo "❌ ACCESS DENIED";
                if (!empty($response['denial_reason'])) {
                    echo " - Reason: {$response['denial_reason']}";
                }
                echo "\n";
                
                // Send denial signal back to ESP32
                $this->sendResponseToESP32('denied', null, $response['denial_reason'] ?? null);
            }
        } else {
            echo "⚠️  Unexpected response format from server\n";
        }
    }
    
    private function checkForScanRequests()
    {
        // Get the storage path (assuming Laravel structure)
        $tempDir = __DIR__ . '/storage/app';
        
        // Create directory if it doesn't exist
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        
        $scanFiles = glob($tempDir . '/temp_scan_*.json');
        
        foreach ($scanFiles as $file) {
            if (!file_exists($file)) continue;
            
            $scanData = json_decode(file_get_contents($file), true);
            
            if (!$scanData || $scanData['status'] !== 'waiting') {
                continue;
            }
            
            $requestedAt = new DateTime($scanData['requested_at']);
            $timeout = $requestedAt->getTimestamp() + $scanData['timeout'];
            
            // Check if scan has timed out
            if (time() > $timeout) {
                $scanData['status'] = 'timeout';
                $scanData['error'] = 'Scan request timed out';
                file_put_contents($file, json_encode($scanData));
                continue;
            }
            
            // Send scan request to ESP32 if not already sent
            if (!isset($scanData['esp32_notified']) || !$scanData['esp32_notified']) {
                $this->sendScanRequestToESP32();
                
                // Mark as notified
                $scanData['esp32_notified'] = true;
                file_put_contents($file, json_encode($scanData));
                
                echo "🔍 Scan request sent to ESP32, waiting for card tap...\n";
            }
        }
    }
    
    private function checkAndFulfillScanRequest($cardUID)
    {
        $tempDir = __DIR__ . '/storage/app';
        $scanFiles = glob($tempDir . '/temp_scan_*.json');
        
        foreach ($scanFiles as $file) {
            if (!file_exists($file)) continue;
            
            $scanData = json_decode(file_get_contents($file), true);
            
            if (!$scanData || $scanData['status'] !== 'waiting') {
                continue;
            }
            
            $requestedAt = new DateTime($scanData['requested_at']);
            $timeout = $requestedAt->getTimestamp() + $scanData['timeout'];
            
            // Check if scan has timed out
            if (time() > $timeout) {
                continue;
            }
            
            // Fulfill the scan request
            $scanData['status'] = 'completed';
            $scanData['card_uid'] = $cardUID;
            $scanData['completed_at'] = date('c');
            
            file_put_contents($file, json_encode($scanData));
            
            echo "✅ Scan request fulfilled with card: $cardUID\n";
            
            // Notify the web application
            $this->notifyWebApplication(basename($file, '.json'), $cardUID);
            
            break; // Only fulfill one request per card scan
        }
    }
    
    private function notifyWebApplication($scanId, $cardUID)
    {
        try {
            $url = $this->serverUrl . '/api/rfid/scan/update';
            
            $postData = json_encode([
                'scan_id' => $scanId,
                'card_uid' => $cardUID
            ]);
            
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n" .
                               "User-Agent: ESP32-Simple-Bridge/1.0\r\n" .
                               "Content-Length: " . strlen($postData) . "\r\n",
                    'content' => $postData,
                    'timeout' => 5
                ]
            ]);
            
            $result = @file_get_contents($url, false, $context);
            
            if ($result !== false) {
                echo "📤 Web application notified of scan completion\n";
            } else {
                echo "⚠️  Failed to notify web application\n";
            }
            
        } catch (Exception $e) {
            echo "⚠️  Error notifying web application: " . $e->getMessage() . "\n";
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
    
    private function sendScanRequestToESP32()
    {
        if (!$this->handle) {
            echo "⚠️  No serial connection to send scan request\n";
            return;
        }

        // Send scan request command to ESP32
        $command = "SCAN_REQUEST\n";
        fwrite($this->handle, $command);
        fflush($this->handle);

        echo "📤 Scan request command sent to ESP32\n";
    }

    private function sendCommandToESP32($command)
    {
        if (!$this->handle) {
            return false;
        }

        fwrite($this->handle, $command . "\n");
        fflush($this->handle);
        
        return true;
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
$serverUrl = $argv[3] ?? 'http://localhost:8000';

// Create and run simple bridge
$bridge = new ESP32SimpleBridge($serialPort, $baudRate, $serverUrl);

// Handle Ctrl+C gracefully
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGINT, function() use ($bridge) {
        echo "\n🛑 Shutting down simple bridge...\n";
        $bridge->disconnect();
        exit(0);
    });
}

echo "=== ESP32 Simple RFID Bridge ===\n";
echo "Features: Access Control + On-Demand Scanning (No Laravel dependency)\n";
echo "Press Ctrl+C to stop\n\n";

if ($bridge->connect()) {
    $bridge->listen();
} else {
    echo "❌ Failed to start ESP32 simple bridge\n";
    exit(1);
}
