<?php
/**
 * Simple ESP32 Serial Debug Script
 * This will show ALL data coming from the ESP32 without any filtering
 */

echo "ESP32 Serial Debug Tool\n";
echo "=======================\n";
echo "Port: COM3\n";
echo "This will show ALL data received from ESP32\n";
echo "Press Ctrl+C to stop\n\n";

// Configure and open serial port
$command = "mode COM3: BAUD=115200 PARITY=N DATA=8 STOP=1";
exec($command, $output, $returnVar);

if ($returnVar !== 0) {
    die("Failed to configure COM3\n");
}

$handle = fopen("COM3", 'r+b');
if (!$handle) {
    die("Cannot open COM3\n");
}

stream_set_blocking($handle, false);

echo "✅ Connected to ESP32 on COM3\n";
echo "Waiting for data... (tap RFID cards now)\n";
echo "----------------------------------------\n";

$dataCount = 0;
while (true) {
    $data = fgets($handle);
    if ($data !== false && !empty(trim($data))) {
        $dataCount++;
        $trimmed = trim($data);
        echo "[$dataCount] Raw: '" . $trimmed . "' (len: " . strlen($trimmed) . ")\n";
        
        // Check if it looks like JSON
        $json = json_decode($trimmed, true);
        if ($json) {
            echo "     JSON parsed successfully:\n";
            foreach ($json as $key => $value) {
                echo "       $key: $value\n";
            }
        } else {
            echo "     Not JSON data\n";
        }
        echo "----------------------------------------\n";
    }
    
    usleep(10000); // 10ms delay
}

fclose($handle);
?>
