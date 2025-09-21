<?php
/**
 * Test Serial Communication with ESP32
 * 
 * This script tests basic serial communication with the ESP32
 * to verify the connection is working properly.
 */

$serialPort = $argv[1] ?? 'COM7';
$baudRate = intval($argv[2] ?? 115200);

echo "Testing serial communication...\n";
echo "Serial Port: $serialPort\n";
echo "Baud Rate: $baudRate\n\n";

$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

try {
    if ($isWindows) {
        $handle = fopen($serialPort, 'r+b');
    } else {
        exec("stty -F $serialPort $baudRate");
        $handle = fopen($serialPort, 'r+');
    }
    
    if (!$handle) {
        throw new Exception("Cannot open serial port $serialPort");
    }
    
    stream_set_blocking($handle, false);
    echo "✅ Connected to $serialPort\n\n";
    
    // Test commands
    $commands = ['PING', 'STATUS', 'SCAN_REQUEST'];
    
    foreach ($commands as $command) {
        echo "Sending command: $command\n";
        fwrite($handle, $command . "\n");
        fflush($handle);
        
        // Wait for response
        sleep(1);
        $response = '';
        while (($data = fread($handle, 1024)) !== false && strlen($data) > 0) {
            $response .= $data;
        }
        
        if (!empty($response)) {
            echo "Response: " . trim($response) . "\n";
        } else {
            echo "No response received\n";
        }
        echo "---\n";
        sleep(2);
    }
    
    echo "\nListening for RFID data for 30 seconds...\n";
    echo "Please tap an RFID card now.\n\n";
    
    $startTime = time();
    $buffer = '';
    
    while (time() - $startTime < 30) {
        $data = fread($handle, 1024);
        
        if ($data !== false && strlen($data) > 0) {
            $buffer .= $data;
            
            // Process complete lines
            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);
                
                $line = trim($line);
                if (!empty($line)) {
                    echo "[" . date('H:i:s') . "] $line\n";
                    
                    // Check if it's JSON data (RFID scan)
                    $jsonData = json_decode($line, true);
                    if ($jsonData && isset($jsonData['cardUID'])) {
                        echo "🎉 RFID Card detected: " . $jsonData['cardUID'] . "\n";
                        echo "Communication test successful!\n";
                        break 2;
                    }
                }
            }
        }
        
        usleep(100000); // 100ms
    }
    
    fclose($handle);
    echo "\nTest completed.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
