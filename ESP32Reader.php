<?php

/**
 * Enhanced ESP32 RFID Reader for PHP
 * Command-line script to read RFID data from ESP32 via serial port
 * Now supports web scan requests from Laravel application
 * 
 * Usage: php ESP32Reader.php --port=COM7 --url=http://localhost:8000
 */

class ESP32Reader
{
    private $port;
    private $baudrate;
    private $laravelUrl;
    private $apiEndpoint;
    private $handle;
    private $running = false;
    private $lastScanRequestCheck = 0;

    public function __construct ($port = 'COM7', $baudrate = 115200, $laravelUrl = 'http://localhost:8000')
    {
        $this->port = $port;
        $this->baudrate = $baudrate;
        $this->laravelUrl = rtrim($laravelUrl, '/');
        $this->apiEndpoint = $this->laravelUrl . '/api/rfid-scan';
        
        echo "Enhanced ESP32 RFID Reader Initialized\n";
        echo "Port: {$this->port}\n";
        echo "Baudrate: {$this->baudrate}\n";
        echo "Laravel URL: {$this->laravelUrl}\n";
        echo "API Endpoint: {$this->apiEndpoint}\n";
        echo "Web Scan Requests: Enabled\n";
        echo str_repeat('-', 50) . "\n";
    }

    /**
     * Connect to ESP32 serial port
     */
    public function connect()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            $command = "mode {$this->port}: BAUD={$this->baudrate} PARITY=N DATA=8 STOP=1";
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                throw new Exception("Failed to configure port {$this->port}");
            }

            $this->handle = fopen($this->port, 'r+b');
        } else {
            // Linux/macOS
            $this->handle = fopen($this->port, 'r+b');
        }

        if (!$this->handle) {
            throw new Exception("Cannot open serial port {$this->port}");
        }

        // Set non-blocking mode
        stream_set_blocking($this->handle, false);
        
        echo "✅ Connected to ESP32 on {$this->port}\n";
        return true;
    }

    /**
     * Read data from ESP32
     */
    public function readData()
    {
        if (!$this->handle) {
            return false;
        }

        $data = fgets($this->handle);
        if ($data !== false && !empty(trim($data))) {
            return trim($data);
        }
        
        return false;
    }

    /**
     * Send RFID data to Laravel API
     */
    public function sendToLaravel($cardUID, $timestamp = null)
    {
        if (!$timestamp) {
            $timestamp = date('Y-m-d H:i:s');
        }

        $postData = json_encode([
            'cardUID' => $cardUID,
            'timestamp' => $timestamp
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postData)
                ],
                'content' => $postData,
                'timeout' => 10
            ]
        ]);

        $response = @file_get_contents($this->apiEndpoint, false, $context);
        
        if ($response === false) {
            echo "❌ Failed to send data to Laravel API\n";
            return false;
        }

        $responseData = json_decode($response, true);
        
        if ($responseData && isset($responseData['success']) && $responseData['success']) {
            echo "✅ Data sent successfully to Laravel\n";
            if (isset($responseData['card_uid'])) {
                echo "   Card: {$responseData['card_uid']}\n";
                
                // Show appropriate status based on card status
                if (isset($responseData['card_status'])) {
                    if ($responseData['card_status'] === 'new_card') {
                        echo "   Status: 🆕 New card detected (ready for assignment)\n";
                    } elseif ($responseData['card_status'] === 'registered_card') {
                        if (isset($responseData['access_granted']) && $responseData['access_granted']) {
                            echo "   Access: ✅ GRANTED\n";
                            if (isset($responseData['tenant_name'])) {
                                echo "   Tenant: {$responseData['tenant_name']}\n";
                            }
                        } else {
                            echo "   Access: ❌ DENIED\n";
                            if (isset($responseData['denial_reason'])) {
                                echo "   Reason: {$responseData['denial_reason']}\n";
                            }
                        }
                    }
                } else {
                    // Fallback for older response format
                    if (isset($responseData['access_granted'])) {
                        if ($responseData['access_granted']) {
                            echo "   Access: ✅ GRANTED\n";
                            if (isset($responseData['tenant_name'])) {
                                echo "   Tenant: {$responseData['tenant_name']}\n";
                            }
                        } else {
                            if (isset($responseData['denial_reason']) && $responseData['denial_reason'] !== 'card_not_found') {
                                echo "   Access: ❌ DENIED\n";
                                echo "   Reason: {$responseData['denial_reason']}\n";
                            } else {
                                echo "   Status: 🆕 New card detected\n";
                            }
                        }
                    }
                }
                
                // Show message if available
                if (isset($responseData['message'])) {
                    echo "   Message: {$responseData['message']}\n";
                }
            }
            return true;
        } else {
            echo "❌ Laravel API returned error: " . ($responseData['message'] ?? 'Unknown error') . "\n";
            return false;
        }
    }

    /**
     * Check for web scan requests from Laravel
     */
    public function checkWebScanRequests()
    {
        $scanRequestDir = dirname(__FILE__) . '/storage/app/scan_requests';
        
        // Create directory if it doesn't exist
        if (!is_dir($scanRequestDir)) {
            mkdir($scanRequestDir, 0755, true);
        }
        
        $requestFiles = glob($scanRequestDir . '/web_scan_*.json');
        
        foreach ($requestFiles as $requestFile) {
            if (!file_exists($requestFile)) {
                continue;
            }
            
            $requestData = json_decode(file_get_contents($requestFile), true);
            
            if (!$requestData || $requestData['status'] !== 'pending') {
                continue;
            }
            
            // Check if request has timed out
            $requestedAt = strtotime($requestData['requested_at']);
            $timeout = $requestData['timeout'];
            
            if (time() - $requestedAt > $timeout) {
                // Mark as timed out
                $requestData['status'] = 'timeout';
                $requestData['error'] = 'Request timed out';
                file_put_contents($requestFile, json_encode($requestData, JSON_PRETTY_PRINT));
                echo "⏰ Web scan request timed out: {$requestData['scan_id']}\n";
                continue;
            }
            
            echo "🌐 Processing web scan request: {$requestData['scan_id']}\n";
            echo "   Waiting for RFID card tap...\n";
            
            // Send scan request to ESP32
            if ($this->handle) {
                fwrite($this->handle, "SCAN_REQUEST\n");
                fflush($this->handle);
            }
            
            // Mark as processing
            $requestData['status'] = 'processing';
            file_put_contents($requestFile, json_encode($requestData, JSON_PRETTY_PRINT));
        }
    }
    
    /**
     * Store the latest card UID for web interface access
     */
    public function storeLatestCardUID($cardUID)
    {
        $latestCardFile = dirname(__FILE__) . '/storage/app/latest_card.json';
        $latestCardDir = dirname($latestCardFile);
        
        // Create directory if it doesn't exist
        if (!is_dir($latestCardDir)) {
            mkdir($latestCardDir, 0755, true);
        }
        
        $latestCardData = [
            'card_uid' => $cardUID,
            'scanned_at' => date('c'),
            'timestamp' => time()
        ];
        
        file_put_contents($latestCardFile, json_encode($latestCardData, JSON_PRETTY_PRINT));
        echo "💾 Latest card UID stored for web interface: $cardUID\n";
    }

    /**
     * Fulfill web scan request with detected card UID
     */
    public function fulfillWebScanRequest($cardUID)
    {
        $scanRequestDir = dirname(__FILE__) . '/storage/app/scan_requests';
        $requestFiles = glob($scanRequestDir . '/web_scan_*.json');
        
        foreach ($requestFiles as $requestFile) {
            if (!file_exists($requestFile)) {
                continue;
            }
            
            $requestData = json_decode(file_get_contents($requestFile), true);
            
            if (!$requestData || $requestData['status'] !== 'processing') {
                continue;
            }
            
            // Fulfill the request
            $requestData['status'] = 'completed';
            $requestData['card_uid'] = $cardUID;
            $requestData['completed_at'] = date('c');
            
            file_put_contents($requestFile, json_encode($requestData, JSON_PRETTY_PRINT));
            
            echo "✅ Web scan request fulfilled: {$requestData['scan_id']} with card: $cardUID\n";
            
            // Only fulfill the first matching request
            break;
        }
    }

    /**
     * Process incoming RFID data
     */
    public function processRfidData($data)
    {
        echo "📡 Raw data: $data\n";
        
        // Filter out ESP32 boot messages and debug info
        $ignoredMessages = [
            'v:', 'mode:', 'load:', 'entry', 'Firmware Version:',
            'RFID Reader initialized', 'Ready to scan cards',
            'Format:', 'Card detected!', 'UID:', 'SCAN_REQUEST_ACTIVE',
            'Please tap your RFID card'
        ];
        
        foreach ($ignoredMessages as $ignored) {
            if (strpos($data, $ignored) !== false) {
                echo "⏭️  Ignoring ESP32 debug message\n";
                return false;
            }
        }

        $cardUID = null;
        $timestamp = null;

        // Try to parse as JSON first
        $jsonData = json_decode($data, true);
        if ($jsonData && isset($jsonData['cardUID'])) {
            $cardUID = $jsonData['cardUID'];
            $timestamp = $jsonData['timestamp'] ?? null;
        } else if (preg_match('/^([A-F0-9]+)(:(\d+))?$/i', $data, $matches)) {
            // Try simple format: CARD_UID:TIMESTAMP or just CARD_UID
            $cardUID = $matches[1];
            $timestamp = isset($matches[3]) ? date('Y-m-d H:i:s', $matches[3] / 1000) : null;
        }

        if ($cardUID) {
            echo "🎯 Detected RFID card: $cardUID\n";
            
            // Store the latest card UID for web interface access
            $this->storeLatestCardUID($cardUID);
            
            // Check if this fulfills any web scan requests
            $this->fulfillWebScanRequest($cardUID);
            
            // Send to Laravel for activity logging (including new cards)
            return $this->sendToLaravel($cardUID, $timestamp);
        }
        
        echo "⚠️  Unknown data format, ignoring\n";
        return false;
    }

    /**
     * Test Laravel connection
     */
    public function testLaravelConnection()
    {
        echo "🔍 Testing Laravel connection...\n";
        
        $testUrl = $this->laravelUrl . '/api/system-info';
        $response = @file_get_contents($testUrl);
        
        if ($response === false) {
            echo "❌ Cannot connect to Laravel at {$this->laravelUrl}\n";
            echo "   Make sure Laravel server is running: php artisan serve\n";
            return false;
        }
        
        $data = json_decode($response, true);
        if ($data) {
            echo "✅ Laravel connection successful\n";
            echo "   PHP Version: " . ($data['php_version'] ?? 'Unknown') . "\n";
            echo "   Laravel Version: " . ($data['laravel_version'] ?? 'Unknown') . "\n";
            echo "   Database Connected: " . ($data['database_connected'] ? 'Yes' : 'No') . "\n";
            return true;
        }
        
        echo "⚠️  Laravel responded but with unexpected data\n";
        return false;
    }

    /**
     * Main reading loop with web scan request support
     */
    public function run()
    {
        $this->running = true;
        
        echo "🚀 Starting Enhanced RFID reader...\n";
        echo "💡 Tap RFID cards on the reader\n";
        echo "🌐 Monitoring for web scan requests\n";
        echo "⏹️  Press Ctrl+C to stop\n";
        echo str_repeat('-', 50) . "\n";

        // Test Laravel connection first
        if (!$this->testLaravelConnection()) {
            echo "❌ Cannot continue without Laravel connection\n";
            return false;
        }

        // Connect to ESP32
        try {
            $this->connect();
        } catch (Exception $e) {
            echo "❌ Connection failed: " . $e->getMessage() . "\n";
            return false;
        }

        $lastDataTime = 0;
        $emptyReads = 0;
        
        while ($this->running) {
            $data = $this->readData();
            
            if ($data !== false) {
                $emptyReads = 0;
                $lastDataTime = time();
                
                // Process the RFID data
                $this->processRfidData($data);
                
            } else {
                $emptyReads++;
                
                // Show status every 10 seconds of no data
                if ($emptyReads % 1000 === 0) {
                    echo "⏳ Waiting for RFID data... (" . date('H:i:s') . ")\n";
                }
            }
            
            // Check for web scan requests every 500ms
            if (time() - $this->lastScanRequestCheck >= 0.5) {
                $this->checkWebScanRequests();
                $this->lastScanRequestCheck = time();
            }
            
            // Small delay to prevent excessive CPU usage
            usleep(10000); // 10ms
            
            // Check if we should continue
            if (connection_aborted()) {
                break;
            }
        }
        
        $this->close();
        echo "\n🛑 Enhanced RFID reader stopped\n";
    }

    /**
     * Stop the reader
     */
    public function stop()
    {
        $this->running = false;
    }

    /**
     * Close serial connection
     */
    public function close()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
            echo "🔌 Serial connection closed\n";
        }
    }

    /**
     * Get available COM ports (Windows)
     */
    public static function getAvailablePorts()
    {
        $ports = [];
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = 'powershell -Command "[System.IO.Ports.SerialPort]::GetPortNames()"';
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                $ports = array_filter($output, function($port) {
                    return !empty(trim($port));
                });
            }
        }
        
        if (empty($ports)) {
            // Fallback to common ports
            $ports = ['COM1', 'COM3', 'COM7', 'COM8', 'COM9', 'COM10', 'COM11'];
        }
        
        return $ports;
    }
}

// Signal handling for graceful shutdown
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGINT, function($signal) {
        global $reader;
        if ($reader) {
            $reader->stop();
        }
        exit(0);
    });
}

// Command line execution
if (php_sapi_name() === 'cli') {
    // Parse command line arguments
    $options = [
        'port' => 'COM7',
        'url' => 'http://localhost:8000',
        'help' => false
    ];
    
    $shortopts = "h";
    $longopts = ["port:", "url:", "help"];
    $parsed = getopt($shortopts, $longopts);
    
    if (isset($parsed['h']) || isset($parsed['help'])) {
        echo "Enhanced ESP32 RFID Reader for Laravel\n";
        echo "Usage: php ESP32Reader.php [options]\n\n";
        echo "Options:\n";
        echo "  --port=COMx    Serial port (default: COM7)\n";
        echo "  --url=URL      Laravel base URL (default: http://localhost:8000)\n";
        echo "  --help, -h     Show this help message\n\n";
        echo "Features:\n";
        echo "  ✅ RFID data reading and Laravel API integration\n";
        echo "  ✅ Web scan request support for direct Card UID retrieval\n";
        echo "  ✅ Activity logging to Laravel database\n";
        echo "  ✅ Real-time card processing\n\n";
        echo "Available COM ports:\n";
        $ports = ESP32Reader::getAvailablePorts();
        foreach ($ports as $port) {
            echo "  - $port\n";
        }
        echo "\nExample:\n";
        echo "  php ESP32Reader.php --port=COM8 --url=http://localhost:8000\n";
        exit(0);
    }
    
    if (isset($parsed['port'])) {
        $options['port'] = $parsed['port'];
    }
    
    if (isset($parsed['url'])) {
        $options['url'] = $parsed['url'];
    }
    
    // Create and run the reader
    $reader = new ESP32Reader($options['port'], 115200, $options['url']);
    
    // Handle process control signals
    if (function_exists('pcntl_async_signals')) {
        pcntl_async_signals(true);
    }
    
    $reader->run();
    
} else {
    echo "This script must be run from command line\n";
}
?>