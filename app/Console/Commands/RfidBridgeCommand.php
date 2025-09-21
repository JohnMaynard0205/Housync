<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RfidCard;
use App\Models\AccessLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class RfidBridgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rfid:bridge 
                            {port=COM7 : Serial port (e.g., COM7, /dev/ttyUSB0)} 
                            {baud=115200 : Baud rate} 
                            {--server-url=http://localhost:8000 : Server URL for API calls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Enhanced RFID Bridge for ESP32 communication with on-demand scanning support';

    private $serialPort;
    private $baudRate;
    private $handle;
    private $isWindows;
    private $serverUrl;
    private $lastScanCheck = 0;
    private $running = true;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->serialPort = $this->argument('port');
        $this->baudRate = (int) $this->argument('baud');
        $this->serverUrl = rtrim($this->option('server-url'), '/');
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $this->info("ESP32 Enhanced RFID Bridge starting...");
        $this->info("Serial Port: {$this->serialPort}");
        $this->info("Baud Rate: {$this->baudRate}");
        $this->info("OS: " . ($this->isWindows ? 'Windows' : 'Unix'));
        $this->info("Server URL: {$this->serverUrl}");
        $this->info("Database: " . config('database.default'));
        $this->newLine();

        // Handle Ctrl+C gracefully
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, [$this, 'handleShutdown']);
            pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
        }

        if ($this->connect()) {
            $this->info("✅ Bridge started successfully!");
            $this->info("Press Ctrl+C to stop the bridge");
            $this->newLine();
            $this->listen();
        } else {
            $this->error("❌ Failed to start ESP32 bridge");
            return 1;
        }

        return 0;
    }

    private function connect()
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

            $this->info("✅ Connected to {$this->serialPort}");
            $this->info("Waiting for RFID data and scan requests...");
            $this->newLine();

            return true;
        } catch (Exception $e) {
            $this->error("❌ Connection failed: " . $e->getMessage());
            return false;
        }
    }

    private function listen()
    {
        if (!$this->handle) {
            $this->error("❌ No connection established");
            return;
        }

        $buffer = '';

        while ($this->running) {
            // Handle signals
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

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

        $this->line("[" . date('Y-m-d H:i:s') . "] Raw: $line");

        // Try to parse JSON data
        $jsonData = json_decode($line, true);

        if ($jsonData && isset($jsonData['cardUID'])) {
            $this->processRfidData($jsonData);
        } else {
            // Log non-JSON data (debug info, etc.)
            if (strpos($line, 'Card detected!') !== false ||
                strpos($line, 'UID:') !== false ||
                strpos($line, 'RFID') !== false) {
                $this->info("📡 ESP32: $line");
            }
        }
    }

    private function processRfidData($data)
    {
        $cardUID = strtoupper($data['cardUID']);
        $timestamp = $data['timestamp'] ?? null;
        $accessTime = now();

        $this->info("🔑 RFID Card Detected: $cardUID");

        // Check if this is for a pending scan request
        $this->checkAndFulfillScanRequest($cardUID);

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
                $this->error("❌ Access DENIED: Card not registered");
            } else {
                $tenantAssignmentId = $rfidCard->tenant_assignment_id;
                $apartmentId = $rfidCard->apartment_id;

                if ($rfidCard->canGrantAccess()) {
                    $accessResult = 'granted';
                    $this->info("✅ Access GRANTED for tenant: {$rfidCard->tenantAssignment->tenant->name}");
                } else {
                    $denialReason = $rfidCard->getAccessDenialReason();
                    $this->error("❌ Access DENIED: {$denialReason}");
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

            $this->info("📝 Access log created (ID: {$accessLog->id})");

            // Send response back to ESP32 (optional)
            $this->sendResponse($accessResult, $rfidCard);

        } catch (Exception $e) {
            DB::rollBack();
            $this->error("❌ Error processing RFID data: " . $e->getMessage());
            Log::error('ESP32 Enhanced Bridge Error', [
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
        }

        $this->line("---");
    }

    private function checkForScanRequests()
    {
        // Check for pending scan request files
        $tempDir = storage_path('app');
        $scanFiles = glob($tempDir . '/temp_scan_*.json');

        foreach ($scanFiles as $file) {
            if (!file_exists($file)) continue;
            
            $scanData = json_decode(file_get_contents($file), true);

            if (!$scanData || $scanData['status'] !== 'waiting') {
                continue;
            }

            $requestedAt = \Carbon\Carbon::parse($scanData['requested_at']);

            // Check if scan has timed out
            if ($requestedAt->addSeconds($scanData['timeout'])->isPast()) {
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
                
                $this->info("🔍 Scan request sent to ESP32, waiting for card tap...");
            }
        }
    }

    private function checkAndFulfillScanRequest($cardUID)
    {
        // Check for pending scan request files
        $tempDir = storage_path('app');
        $scanFiles = glob($tempDir . '/temp_scan_*.json');

        foreach ($scanFiles as $file) {
            if (!file_exists($file)) continue;
            
            $scanData = json_decode(file_get_contents($file), true);

            if (!$scanData || $scanData['status'] !== 'waiting') {
                continue;
            }

            $requestedAt = \Carbon\Carbon::parse($scanData['requested_at']);

            // Check if scan has timed out
            if ($requestedAt->addSeconds($scanData['timeout'])->isPast()) {
                continue;
            }

            // Fulfill the scan request
            $scanData['status'] = 'completed';
            $scanData['card_uid'] = $cardUID;
            $scanData['completed_at'] = now()->toISOString();

            file_put_contents($file, json_encode($scanData));

            $this->info("✅ Scan request fulfilled with card: $cardUID");

            // Notify the web application
            $this->notifyWebApplication(basename($file, '.json'), $cardUID);

            break; // Only fulfill one request per card scan
        }
    }

    private function notifyWebApplication($scanId, $cardUID)
    {
        try {
            $url = $this->serverUrl . '/api/rfid/scan/update';

            $response = Http::timeout(5)->post($url, [
                'scan_id' => $scanId,
                'card_uid' => $cardUID
            ]);

            if ($response->successful()) {
                $this->info("📤 Web application notified of scan completion");
            } else {
                $this->warn("⚠️  Failed to notify web application (HTTP {$response->status()})");
            }

        } catch (Exception $e) {
            $this->warn("⚠️  Error notifying web application: " . $e->getMessage());
        }
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

        $this->info("📤 Response sent to ESP32: $jsonResponse");
    }

    private function sendScanRequestToESP32()
    {
        if (!$this->handle) {
            $this->warn("⚠️  No serial connection to send scan request");
            return;
        }

        // Send scan request command to ESP32
        $command = "SCAN_REQUEST\n";
        fwrite($this->handle, $command);
        fflush($this->handle);

        $this->info("📤 Scan request command sent to ESP32");
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

    public function handleShutdown()
    {
        $this->newLine();
        $this->info("🛑 Shutting down bridge...");
        $this->running = false;
        $this->disconnect();
        exit(0);
    }

    private function disconnect()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->info("🔌 Disconnected from {$this->serialPort}");
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
