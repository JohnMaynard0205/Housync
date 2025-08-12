<?php
/**
 * ESP32 RFID Bridge Script
 * 
 * This script connects to the ESP32 via serial port and processes RFID card data.
 * Run this script continuously to monitor the ESP32 serial output.
 * 
 * Usage: php esp32_bridge.php [COM_PORT] [BAUD_RATE]
 * Example: php esp32_bridge.php COM3 115200
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Boot Laravel
$app->boot();

use App\Models\RfidCard;
use App\Models\AccessLog;
use App\Models\TenantAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ESP32Bridge 
{
    private $serialPort;
    private $baudRate;
    private $handle;
    private $isWindows;
    
    public function __construct($serialPort = 'COM7', $baudRate = 115200)
    {
        $this->serialPort = $serialPort;
        $this->baudRate = $baudRate;
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        echo "ESP32 RFID Bridge starting...\n";
        echo "Serial Port: {$this->serialPort}\n";
        echo "Baud Rate: {$this->baudRate}\n";
        echo "OS: " . ($this->isWindows ? 'Windows' : 'Unix') . "\n";
        echo "Database: " . config('database.default') . "\n\n";
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
            echo "Waiting for RFID data...\n\n";
            
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
        $accessTime = now();
        
        echo "🔑 RFID Card Detected: $cardUID\n";
        
        try {
            DB::beginTransaction();
            
            // Find the RFID card
            $rfidCard = RfidCard::where('card_uid', $cardUID)->first();
            
            $accessResult = 'denied';
            $denialReason = null;
            $tenantAssignmentId = null;
            $apartmentId = null;
            
            if (!$rfidCard) {
                $denialReason = 'card_not_found';
                echo "❌ Access DENIED: Card not registered\n";
            } else {
                $tenantAssignmentId = $rfidCard->tenant_assignment_id;
                $apartmentId = $rfidCard->apartment_id;
                
                if ($rfidCard->canGrantAccess()) {
                    $accessResult = 'granted';
                    echo "✅ Access GRANTED for tenant: {$rfidCard->tenantAssignment->tenant->name}\n";
                } else {
                    $denialReason = $rfidCard->getAccessDenialReason();
                    echo "❌ Access DENIED: {$denialReason}\n";
                }
            }
            
            // Create access log
            $accessLog = AccessLog::create([
                'card_uid' => $cardUID,
                'rfid_card_id' => $rfidCard?->id,
                'tenant_assignment_id' => $tenantAssignmentId,
                'apartment_id' => $apartmentId,
                'access_result' => $accessResult,
                'denial_reason' => $denialReason,
                'access_time' => $accessTime,
                'reader_location' => 'main_entrance',
                'raw_data' => $data
            ]);
            
            DB::commit();
            
            echo "📝 Access log created (ID: {$accessLog->id})\n";
            
            // Send response back to ESP32 (optional)
            $this->sendResponse($accessResult, $rfidCard);
            
        } catch (Exception $e) {
            DB::rollBack();
            echo "❌ Error processing RFID data: " . $e->getMessage() . "\n";
            Log::error('ESP32 Bridge Error', [
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        echo "---\n";
    }
    
    private function sendResponse($accessResult, $rfidCard = null)
    {
        if (!$this->handle) {
            return;
        }
        
        $response = [
            'result' => $accessResult,
            'timestamp' => now()->toISOString(),
            'tenant' => $rfidCard?->tenantAssignment?->tenant?->name ?? null
        ];
        
        $jsonResponse = json_encode($response) . "\n";
        fwrite($this->handle, $jsonResponse);
        fflush($this->handle);
        
        echo "📤 Response sent to ESP32: $jsonResponse";
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
$serialPort = $argv[1] ?? 'COM3';
$baudRate = intval($argv[2] ?? 115200);

// Create and run bridge
$bridge = new ESP32Bridge($serialPort, $baudRate);

// Handle Ctrl+C gracefully
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGINT, function() use ($bridge) {
        echo "\n🛑 Shutting down bridge...\n";
        $bridge->disconnect();
        exit(0);
    });
}

if ($bridge->connect()) {
    echo "Press Ctrl+C to stop the bridge\n\n";
    $bridge->listen();
} else {
    echo "❌ Failed to start ESP32 bridge\n";
    exit(1);
}
